<template>
  <el-card v-if="user.name">
    <el-tabs v-model="activeActivity" @tab-click="handleClick">
      <el-tab-pane :label="t('user.timeline')" name="first">
        <div class="block">
          <el-timeline>
            <el-timeline-item
                placement="top"
                v-for="(item, index) in timeLinesData"
                :key="index"
                :timestamp="item.created_at"
            >
              <el-card>
                <h4>{{item.title}}</h4>
                <p>{{item.content}}</p>
              </el-card>
            </el-timeline-item>
          </el-timeline>
        </div>
      </el-tab-pane>
      <el-tab-pane v-loading="updating" :label="t('user.account')" name="second">
        <el-form :model="user" label-width="120px">
          <el-form-item :label="t('user.name')">
            <el-input v-model="user.name" :disabled="disabled"/>
          </el-form-item>
          <el-form-item :label="t('user.email')">
            <el-input v-model="user.email" :disabled="disabled"/>
          </el-form-item>
          <el-form-item :label="t('user.sex')">
            <el-radio-group v-model="user.sex">
              <el-radio :label="0">{{ t('user.male') }}</el-radio>
              <el-radio :label="1">{{ t('user.female') }}</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item :label="t('user.birthday')">
            <el-date-picker
                v-model="user.birthday"
                type="datetime"
                :placeholder="t('user.birthday')"
                value-format="YYYY-MM-DD HH:mm:ss"
            />
          </el-form-item>
          <el-form-item :label="t('user.description')">
            <el-input
                v-model="user.description"
                maxlength="255"
                :placeholder="t('user.description')"
                show-word-limit
                type="textarea"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="onSubmit">
              {{t('form.save')}}
            </el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>
  </el-card>
</template>

<script setup>
import UserResource from '@/api/user'
import dayjs from 'dayjs'
import {ElMessage} from "element-plus"
import {useI18n} from "vue-i18n";

const props = defineProps({
  user: {
    type: Object,
    default: () => {
      return {
        name: '',
        email: '',
        avatar: '',
        roles: [],
        sex: 0,
        birthday: '2006-01-02 15:04:05',
        description: ''
      }
    },
  },
})

const {t} = useI18n({useScope: 'global'})

const userResource = new UserResource('users')
const resData = reactive({
  activeActivity: 'first',
  disabled: computed(() => {
    if (props.user.id) {
      getTimeLines()
    }
    return props.user.roles && props.user.roles.includes('admin')
  }),
  updating: false,
  timeLinesData: [],
  timeLinesParams: {
    page: 1,
    per_page: 10,
  },
  timeLinesPagination: {
    total: 0,
    currentPage: 1,
    pageSize: 10
  }
})

const handleClick = (tab, event) => {

}
const onSubmit = () => {
  resData.updating = true
  let params = {
    sex: props.user.sex,
    description: props.user.description
  }
  if (props.user.birthday) {
    params.birthday = dayjs(props.user.birthday).format('YYYY-MM-DD HH:mm:ss')
  }
  userResource
      .update(props.user.id, params)
      .then(response => {
        resData.updating = false
        ElMessage({
          message: 'User information has been updated successfully',
          type: 'success',
          duration: 5 * 1000,
        })
      })
      .catch(error => {
        console.log(error)
        resData.updating = false
      })
}

const getTimeLines = () => {
  userResource.logs(props.user.id, resData.timeLinesParams).then((res) => {
    resData.timeLinesData = res.data
    resData.timeLinesPagination = res.pages
  })
}

const {activeActivity, updating, disabled, timeLinesData, timeLinesPagination} = toRefs(resData)
</script>

<style lang="scss" scoped>
.user-activity {
  .user-block {
    .username, .description {
      display: block;
      margin-left: 50px;
      padding: 2px 0;
    }

    img {
      width: 40px;
      height: 40px;
      float: left;
    }

    :after {
      clear: both;
    }

    .img-circle {
      border-radius: 50%;
      border: 2px solid #d2d6de;
      padding: 2px;
    }

    span {
      font-weight: 500;
      font-size: 12px;
    }
  }

  .post {
    font-size: 14px;
    border-bottom: 1px solid #d2d6de;
    margin-bottom: 15px;
    padding-bottom: 15px;
    color: #666;

    .image {
      width: 100%;
    }

    .user-images {
      padding-top: 20px;
    }
  }

  .list-inline {
    padding-left: 0;
    margin-left: -5px;
    list-style: none;

    li {
      display: inline-block;
      padding-right: 5px;
      padding-left: 5px;
      font-size: 13px;
    }

    .link-black {
      &:hover, &:focus {
        color: #999;
      }
    }
  }

  .el-carousel__item h3 {
    color: #475669;
    font-size: 14px;
    opacity: 0.75;
    line-height: 200px;
    margin: 0;
  }

  .el-carousel__item:nth-child(2n) {
    background-color: #99a9bf;
  }

  .el-carousel__item:nth-child(2n+1) {
    background-color: #d3dce6;
  }
}
</style>
