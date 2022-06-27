<template>
  <el-card v-if="user.name">
    <div class="user-profile">
      <div class="user-avatar box-center">
        <pan-thumb :image="user.avatar" :height="'100px'" :width="'100px'" :hoverable="false"/>
      </div>
      <div class="box-center">
        <div class="user-name text-center">
          {{ user.name }}
        </div>
        <div class="user-role text-center text-muted">
          {{ getRole() }}
        </div>
      </div>
      <div class="box-social">
        <el-descriptions
            :column="1"
            :size="size"
            border
        >
          <el-descriptions-item :label="t('user.name')">{{ user.name }}</el-descriptions-item>
          <el-descriptions-item :label="t('user.email')">{{ user.email }}</el-descriptions-item>
          <el-descriptions-item :label="t('user.sex')">{{ user.sex ? t('user.male') : t('user.female') }}</el-descriptions-item>
          <el-descriptions-item :label="t('user.age')">{{ user.age }}</el-descriptions-item>
          <el-descriptions-item :label="t('user.description')">
            {{user.description}}
            </el-descriptions-item>
        </el-descriptions>
      </div>
    </div>
  </el-card>
</template>

<script setup>
import PanThumb from '@/components/PanThumb/index.vue'
import {uppercaseFirst, toThousandFilter} from "@/utils/index"
import {useI18n} from "vue-i18n";

const {t} = useI18n({useScope: 'global'})

const props = defineProps({
  user: {
    type: Object,
    default: () => {
      return {
        name: '',
        email: '',
        avatar: '',
        roles: [],
      };
    },
  },
})

const getRole = () => {
  const roles = Object.values(props.user.roles.map(value => uppercaseFirst(value)))
  return roles.join('|')
}
</script>

<style lang="scss" scoped>
.user-profile {
  .user-name {
    font-weight: bold;
  }

  .box-center {
    padding-top: 10px;
  }

  .user-role {
    padding-top: 10px;
    font-weight: 400;
    font-size: 14px;
  }

  .box-social {
    padding-top: 30px;

    .el-table {
      border-top: 1px solid #dfe6ec;
    }
  }

  .user-follow {
    padding-top: 20px;
  }
}
</style>
