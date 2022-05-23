import {login, logout, getInfo} from '@/api/auth'
import {isLogged, setToken, removeToken} from '@/utils/auth'
import router, {resetRouter} from '../router'
import {defineStore} from "pinia"
import {permissionStore} from "@/store/permission";

export const userStore = defineStore('user', {
  state: () => {
    return {
      id: null,
      user: null,
      token: null,
      name: '',
      avatar: '',
      introduction: '',
      roles: [],
      permissions: [],
    }
  },
  actions: {
    // user login
    login(userInfo) {
      const {email, password} = userInfo
      return new Promise((resolve, reject) => {
        login({email: email.trim(), password: password})
          .then(response => {
            setToken(response.data.token)
            resolve()
          })
          .catch(error => {
            console.log(error)
            reject(error)
          })
      })
    },
    // get user info
    getInfo() {
      return new Promise((resolve, reject) => {
        getInfo()
          .then(response => {
            const {data} = response

            if (!data) {
              reject('Verification failed, please Login again.')
            }

            const {roles, name, avatar, introduction, permissions, id} = data
            // roles must be a non-empty array
            if (!roles || roles.length <= 0) {
              reject('getInfo: roles must be a non-null array!')
            }

            this.$patch((state) => {
              state.id = id
              state.introduction = introduction
              state.name = name
              state.roles = roles
              state.permissions = permissions
              state.avatar = avatar
            })
            resolve(data)
          })
          .catch(error => {
            console.log(error)
            reject(error)
          })
      })
    },
    // user logout
    logout() {
      return new Promise((resolve, reject) => {
        logout()
          .then(() => {
            this.$patch((state) => {
              state.token = ''
              state.roles = []
            })
            removeToken()
            resetRouter()
            resolve()
          })
          .catch(error => {
            reject(error)
          })
      })
    },
    // remove token
    resetToken() {
      return new Promise(resolve => {
        this.$patch((state) => {
          state.token = ''
          state.roles = []
        })
        removeToken()
        resolve()
      })
    },
    // Dynamically modify permissions
    changeRoles(role) {
      return new Promise(async resolve => {
        const roles = [role.name]
        const permissions = role.permissions.map(permission => permission.name)
        this.$patch((state) => {
          state.permissions = permissions
          state.roles = roles
        })
        resetRouter()

        // generate accessible routes map based on roles

        const usePermissionStore = permissionStore()
        const accessRoutes = await usePermissionStore.generateRoutes(roles, permissions)

        // dynamically add accessible routes
        accessRoutes.forEach((item) => {
          router.addRoute(item)
        })

        resolve()
      })
    },
  }
})
