<template>
  <div class="app-container scroll-y">
    <el-form v-if="user" :model="user">
      <el-row :gutter="20">
        <el-col :span="6">
          <user-card :user="user" />
          <user-bio />
        </el-col>
        <el-col :span="18">
          <user-activity :user="user" />
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>

<script>
import UserBio from './components/UserBio.vue'
import UserCard from './components/UserCard.vue'
import UserActivity from './components/UserActivity.vue'
import {onMounted, reactive, toRefs} from "vue"
import {userStore} from "@/store/user";

export default {
  name: 'SelfProfile',
  components: { UserBio, UserCard, UserActivity },
  setup() {
    const resData = reactive({
      user: {},
    })

    onMounted(() => {
      getUser()
    })

    const useUserStore = userStore()

    const getUser = async () => {
        resData.user = await useUserStore.getInfo()
    }

    return {
      ...toRefs(resData)
    }
  },
};
</script>
