<template>
  <div class="app-container">
    <el-form v-if="user" :model="user">
      <el-row :gutter="20">
        <el-col :span="6">
          <user-card :user="user" />
        </el-col>
        <el-col :span="18">
          <user-activity :user="user" />
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import Resource from '@/api/resource'
import UserBio from './components/UserBio.vue'
import UserCard from './components/UserCard.vue'
import UserActivity from './components/UserActivity.vue'
import {userStore} from "../../store/user"
import {getCurrentInstance, onMounted, reactive, toRefs} from "vue"

const userResource = new Resource('users')
export default {
  name: 'EditUser',
  components: { UserBio, UserCard, UserActivity },
  setup() {
    const resData = reactive({
      user: {}
    })
    const useUserStore = userStore()
    const {proxy} = getCurrentInstance()
    const getUser = async (id) => {
      const { data } = await userResource.get(id)
      resData.user = data
    }
    onMounted(() => {
      const id = proxy.$route.params && proxy.$route.params.id
      const currentUserId = useUserStore.id
      if (id === currentUserId) {
        proxy.$route.$router.push('/profile/edit')
        return
      }
      getUser(id)
    })

    return {
      ...toRefs(resData)
    }
  },
}
</script>
