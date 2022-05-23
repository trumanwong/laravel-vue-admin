<template>
  <section>
    <el-table
        ref="tableRef"
        v-loading="loading"
        :style="'width: 100%; height: ' + (tableHeight === '' ? '100%' : tableHeight)"
        highlight-current-row
        :tooltip-effect="tooltipEffect"
        :row-key="getRowKey"
        :stripe="stripe"
        :data="tableData"
        :height="tableHeight"
        :border="border"
        @row-click="toggleExpand"
        @sort-change="handleSortChange"
        @selection-change="handleSelectionChange"
    >
      <template v-for="(item,index) in tableColumn" :key="index">
        <el-table-column
            v-if="item.filters"
            :key="index+1"
            :width="item.width"
            :align="item.align"
            :label="item.label"
            :prop="item.prop"
            :sortable="item.sortable"
            :filters="item.filters ? item.filters : []"
            :filter-method="filterHandler"
            :show-overflow-tooltip="!item.operate"
        >
          <template #default="scope">
            <slot v-if="item.slot" :name="item.prop" v-bind="scope"/>
            <span v-else>{{ show(scope.row, item.prop, scope) }}</span>
          </template>
        </el-table-column>
        <el-table-column
            v-else-if="item.type === 'selection'"
            :type="item.type"
            :width="item.width"
            :key="index+1"
        >
        </el-table-column>
        <el-table-column
            v-else
            :key="index+1"
            :width="item.width"
            :align="item.align ? item.align : 'center'"
            :label="item.label"
            :prop="item.prop"
            :sortable="item.sortable"
            show-overflow-tooltip
        >
          <template #default="scope">
            <slot v-if="item.slot" :name="item.prop" v-bind="scope"/>
            <span v-else>{{ show(scope.row, item.prop) }}</span>
          </template>
        </el-table-column>
      </template>
      <el-table-column
          v-if="tableOption.label"
          :fixed="tableOption.fixed"
          :align="tableOption.align ? tableOption.align : 'center'"
          :width="tableOption.width"
          :label="tableOption.label"
      >
        <template #default="scope">
          <template v-if="tableOption.item_actions">
            <slot v-if="tableOption.slot" name="table_options" v-bind="scope"/>
            <template v-else-if="tableOption.item_actions.length <= 3">
              <el-button v-for="(action, index) in tableOption.item_actions"
                         :type="action.type ? action.type : 'primary'"
                         :size="action.size ? action.size : ''"
                         @click="callAction(action.name, scope.row)">
                <el-svg-item :el-svg-name="action.icon" :title="action.label"></el-svg-item>
              </el-button>
            </template>
            <template v-else>
              <el-dropdown>
                  <span class="el-dropdown-link">
                    {{ t('table.actions') }}<i class="el-icon-arrow-down el-icon--right"></i>
                  </span>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item v-for="(action, index) in tableOption.item_actions"
                                    :command="action.name"
                                    @click.native="callAction(action.name, scope.row)">
                    <el-svg-item :el-svg-name="action.icon" :title="action.label"></el-svg-item>
                  </el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </template>
        </template>
      </el-table-column>
    </el-table>
  </section>

  <template v-if="paginate && pagination.total>0">
    <section class="pagination-container">
      <el-pagination
          :page-sizes="pageSizes"
          :layout="layout"
          :current-page="pagination.current_page"
          :page-size="pagination.per_page"
          :total="pagination.total"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
      />
    </section>
  </template>
</template>

<script>
import {useI18n} from "vue-i18n";
import ElSvgItem from "./Item/ElSvgItem.vue"
import {defineEmits} from "vue"

export default {
  components: {ElSvgItem},
  props: {
    loading: {
      type: Boolean,
      default: false
    },
    stripe: {
      type: Boolean,
      default: true
    },
    border: {
      type: Boolean,
      default: true
    },
    tooltipEffect: {
      type: String,
      default: 'dark'
    },
    tableData: {
      type: Array,
      required: true
    },
    tableColumn: {
      type: Array,
      required: true
    },
    tableOption: {
      type: Object,
      default: () => {
        return {}
      }
    },
    itemActions: {
      type: Array,
      default() {
        return [
          {
            name: 'edit-item',
            icon: 'fas fa-pencil-alt',
            class: 'btn btn-info'
          },
          {
            name: 'delete-item',
            icon: 'fas fa-trash-alt',
            class: 'btn btn-danger'
          }
        ]
      }
    },
    paginate: {
      // 是否需要翻页组件
      type: Boolean,
      default: true
    },
    layout: {
      // 翻页组件布局，子组件名用逗号分隔
      type: String,
      default: 'total, sizes, prev, pager, next, jumper'
    },
    pageSizes: {
      // 每页显示个数选择器的选项设置
      type: Array,
      default: () => [10, 30, 50, 100]
    },
    pagination: {
      type: Object,
      default: () => {
        return {
          total: 0,
          currentPage: 1,
          pageSize: 10
        }
      },
    },
    tableHeight: {
      type: String,
    }
  },
  setup(props, ctx) {
    const {t} = useI18n()
    const resData = reactive({
      tableRef: ref(null)
    })
    const show = (row, key) => {
      let arr = key.split('.')
      for (let i in arr) {
        row = row[arr[i]]
      }
      return row
    }
    const handleSortChange = (sort) => {
      ctx.emit('handle-sort-change', sort)
    }
    const handleSelectionChange = (val) => {
      ctx.emit('handle-selection-change', val)
    }
    const filterHandler = (value, row, column) => {
      const property = column['property']
      return row[property] === value
    }
    const toggleExpand = (row) => {
      resData.tableRef.toggleRowExpansion(row)
    }
    const handleSizeChange = (perPage) => {
      ctx.emit('set-params', 'per_page', perPage)
    }
    const handleCurrentChange = (current) => {
      ctx.emit('set-params', 'page', current)
    }
    const callAction = (action, data) => {
      console.log(1111)
      ctx.emit('table-action', action, data)
    }
    const closeTag = (obj) => {
      ctx.emit('close-tag', obj)
    }
    const handleToggleRowSelection = (row, isSelected) => {
      nextTick(() => {
        resData.tableRef.toggleRowSelection(row, isSelected)
      })
    }
    const getRowKey = (row) => {
      return row.id
    }
    return {
      ...toRefs(resData),
      show,
      handleSortChange,
      handleSelectionChange,
      filterHandler,
      toggleExpand,
      handleSizeChange,
      handleCurrentChange,
      callAction,
      closeTag,
      handleToggleRowSelection,
      getRowKey
    }
  }
}
</script>


<style scoped>
.pagination-container {
  background: #fff;
  padding: 32px 16px;
}
.pagination-container.hidden {
  display: none;
}
</style>