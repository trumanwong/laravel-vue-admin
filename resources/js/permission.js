import router from './router'
import {ElMessage} from 'element-plus'
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' // progress bar style
import {isLogged} from '@/utils/auth'
import getPageTitle from '@/utils/get-page-title'
import {userStore} from "@/store/user"
import {permissionStore} from "@/store/permission"

NProgress.configure({showSpinner: false}) // NProgress Configuration

const whiteList = ['/login', '/auth-redirect'] // no redirect whitelist

router.beforeEach(async (to, from, next) => {
  // start progress bar
  NProgress.start()
  // set page title
  document.title = getPageTitle(to.meta.title)

  // determine whether the user has logged in
  const isUserLogged = isLogged()
  const useUserStore = userStore()
  const usePermissionStore = permissionStore()

  if (isUserLogged) {
    if (to.path === '/login') {
      // if is logged in, redirect to the home page
      next({path: '/'})
      NProgress.done()
    } else {
      // determine whether the user has obtained his permission roles through getInfo
      const hasRoles = useUserStore.roles && useUserStore.roles.length > 0
      if (hasRoles) {
        next()
      } else {
        try {
          // get user info
          // note: roles must be a object array! such as: ['admin'] or ,['manager','editor']
          const {roles, permissions} = await useUserStore.getInfo()

          // generate accessible routes map based on roles
          const accessRoutes = await usePermissionStore.generateRoutes(roles, permissions)
          accessRoutes.forEach((item) => {
            router.addRoute(item)
          })
          next({...to, replace: true})
        } catch (error) {
          // remove token and go to login page to re-login
          await useUserStore.resetToken()
          ElMessage.error(error.message || 'Has Error')
          next(`/login?redirect=${to.path}`)
          NProgress.done()
        }
      }
    }
  } else {
    /* has no token*/

    if (whiteList.indexOf(to.matched[0] ? to.matched[0].path : '') !== -1) {
      // in the free login whitelist, go directly
      next()
    } else {
      // other pages that do not have permission to access are redirected to the login page.
      next(`/login?redirect=${to.path}`)
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  // finish progress bar
  NProgress.done()
})