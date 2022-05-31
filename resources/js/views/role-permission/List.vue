<template>
  <div class="app-container scroll-y">
    <custom-table :table-data="tableData" :table-column="basicColumn" :table-option="tableOption"
                  :pagination="pagination" :paginate="true" :page-sizes="pageSizes" :loading="loading"
                  @table-action="tableActions" @set-params="setParams">
      <template #name="scope">
        <span>{{ uppercaseFirst(scope.row.name) }}</span>
      </template>
      <template #table_options="scope">
        <div v-if="scope.row.name !== 'admin'">
          <el-button v-for="(action, index) in tableOption.item_actions"
                     :type="action.type ? action.type : 'primary'"
                     :size="action.size ? action.size : ''"
                     @click="tableActions(action.name, scope.row)">
            <el-svg-item :el-svg-name="action.icon" :title="action.label"></el-svg-item>
          </el-button>
        </div>
      </template>
    </custom-table>

    <el-dialog v-model="dialogVisible" :title="'Edit Permissions - ' + currentRole.name">
      <div v-loading="dialogLoading" class="form-container">
        <div class="permissions-container">
          <div class="block">
            <el-form :model="currentRole" label-width="80px" label-position="top">
              <el-form-item label="Menus">
                <el-tree ref="refMenuPermissions" :data="menuPermissions"
                         :default-checked-keys="permissionKeys(roleMenuPermissions)" :props="permissionProps"
                         show-checkbox node-key="id" class="permission-tree"/>
              </el-form-item>
            </el-form>
          </div>
          <div class="block">
            <el-form :model="currentRole" label-width="80px" label-position="top">
              <el-form-item label="Permissions">
                <el-tree ref="refOtherPermissions" :data="otherPermissions"
                         :default-checked-keys="permissionKeys(roleOtherPermissions)" :props="permissionProps"
                         show-checkbox node-key="id" class="permission-tree"/>
              </el-form-item>
            </el-form>
          </div>
          <div class="clear-left"/>
        </div>
        <div style="text-align:right;">
          <el-button type="danger" @click="dialogVisible=false">
            {{ t('permission.cancel') }}
          </el-button>
          <el-button type="primary" @click="confirmPermission">
            {{ t('permission.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import CustomTable from '@/components/CustomTable.vue'
import ElSvgItem from "@/components/Item/ElSvgItem.vue"
import Resource from '@/api/resource'
import RoleResource from '@/api/role'
import checkPermission from '@/utils/permission'
import {useI18n} from "vue-i18n"
import {uppercaseFirst} from "../../utils"
import {userStore} from "../../store/user"
import {ElMessage} from "element-plus" // Permission checking

export default {
  name: 'RoleList',
  components: {CustomTable, ElSvgItem},
  setup() {
    const {t} = useI18n({useScope: 'global'})
    const roleResource = new RoleResource()
    const permissionResource = new Resource('permissions')
    const useUserStore = userStore()
    const refMenuPermissions = ref(null)
    const refOtherPermissions = ref(null)

    const resData = reactive({
      basicColumn: [{
        prop: 'name',
        label: t('roles.name'),
        width: '150'
      }, {
        prop: 'description',
        label: t('table.description'),
      }],
      tableOption: {},
      tableData: [],
      loading: true,
      pagination: {
        total: 0,
        currentPage: 1,
        pageSize: 10
      },
      params: {
        page: 1,
        per_page: 10,
        keyword: '',
        role: '',
      },
      dialogLoading: false,
      dialogVisible: false,
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      currentRole: {},
      roleMenuPermissions: computed(() => {
        if (!resData.currentRole.permissions) {
          return []
        }
        return classifyPermissions(resData.currentRole.permissions).menu
      }),
      roleOtherPermissions: computed(() => {
        if (!resData.currentRole.permissions) {
          return []
        }
        return classifyPermissions(resData.currentRole.permissions).other
      }),
    })

    if (useUserStore.permissions.includes('manage user')) {
      resData.tableOption = {
        slot: true,
        label: t('table.actions'),
        fixed: 'right',
        item_actions: [
          {name: 'edit-item', type: 'primary', icon: 'EditPen', label: t('permission.editPermission')},
        ],
      }
    }
    const getRoles = async () => {
      resData.loading = true
      await roleResource.list(resData.params).then((res) => {
        res.data.forEach((role, index) => {
          role['description'] = t('roles.description.' + role.name)
        })
        resData.tableData = res.data
        resData.pagination = res.meta
        resData.loading = false
      })
    }

    const tableActions = (action, data) => {
      if (action === 'edit-item') {
        handleEditPermissions(data)
      }
    }

    const getPermissions = async () => {
      const {data} = await permissionResource.list({})
      const {all, menu, other} = classifyPermissions(data)
      resData.permissions = all
      resData.menuPermissions = menu
      resData.otherPermissions = other
    }

    const classifyPermissions = (permissions) => {
      const all = []
      const menu = []
      const other = []
      permissions.forEach(permission => {
        const permissionName = permission.name
        all.push(permission)
        if (permissionName.startsWith('view menu')) {
          menu.push(normalizeMenuPermission(permission))
        } else {
          other.push(normalizePermission(permission))
        }
      })
      return {all, menu, other}
    }

    const normalizeMenuPermission = (permission) => {
      return {id: permission.id, name: uppercaseFirst(permission.name.substring(10))}
    }

    const normalizePermission = (permission) => {
      return {
        id: permission.id,
        name: uppercaseFirst(permission.name),
        disabled: permission.name === 'manage permission'
      }
    }

    const permissionKeys = (permissions) => {
      return permissions.map(permssion => permssion.id)
    }

    const handleEditPermissions = (data) => {
      resData.dialogVisible = true
      resData.currentRole = data
      nextTick(() => {
        refMenuPermissions.value?.setCheckedKeys(permissionKeys(resData.roleMenuPermissions))
        refOtherPermissions.value?.setCheckedKeys(permissionKeys(resData.roleOtherPermissions))
      })
    }

    const confirmPermission = () => {
      const checkedMenu = refMenuPermissions.value?.getCheckedKeys()
      const checkedOther = refOtherPermissions.value?.getCheckedKeys()
      const checkedPermissions = checkedMenu.concat(checkedOther)
      resData.dialogLoading = true

      roleResource.update(resData.currentRole.id, {permissions: checkedPermissions}).then(response => {
        ElMessage({
          message: 'Permissions has been updated successfully',
          type: 'success',
          duration: 5 * 1000,
        })
        resData.dialogLoading = false
        resData.dialogVisible = false
        getRoles()
      })
    }

    onMounted(() => {
      getRoles()
      getPermissions()
    })

    const setParams = (key, value) => {
      if (key !== 'per_page' && key !== 'page') {
        resData.params.page = 1
      }
      resData.params[key] = value
      getRoles()
    }

    return {
      ...toRefs(resData),
      refMenuPermissions,
      refOtherPermissions,
      t,
      setParams,
      uppercaseFirst,
      permissionKeys,
      handleEditPermissions,
      confirmPermission,
      tableActions,
      checkPermission
    }
  },
}
</script>

<style lang="scss" scoped>
.permissions-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;

  .block {
    float: left;
    min-width: 250px;
  }

  .clear-left {
    clear: left;
  }
}
</style>
