import {createRouter, createWebHashHistory} from 'vue-router'

/* Layout */
import Layout from '@/layout/Layout.vue'

/* Router for modules */
import chartsRoutes from './modules/charts'
import adminRoutes from './modules/admin'
import errorRoutes from './modules/error'

export const constantRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index.vue'),
    hidden: true,
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/AuthRedirect.vue'),
    hidden: true,
  },
  {
    path: '/404',
    redirect: { name: 'Page404' },
    component: () => import('@/views/error-page/404.vue'),
    hidden: true,
  },
  {
    path: '/401',
    component: () => import('@/views/error-page/401.vue'),
    hidden: true,
  },
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index.vue'),
        name: 'Dashboard',
        meta: { title: 'dashboard', bootstrapIcon: 'house-fill', noCache: false },
      },
    ],
  },
  {
    path: '/profile',
    component: Layout,
    redirect: '/profile/edit',
    children: [
      {
        path: 'edit',
        component: () => import('@/views/users/SelfProfile.vue'),
        name: 'SelfProfile',
        meta: {title: 'userProfile', bootstrapIcon: 'person-circle', noCache: true},
      },
    ],
  },
]

export const asyncRoutes = [
  chartsRoutes,
  adminRoutes,
  errorRoutes,
  { path: '/:pathMatch(.*)*', name: 'NotFound', redirect: '/404', hidden: true }
]

const router = createRouter({
  routes: constantRoutes,
  scrollBehavior: () => ({ top: 0 }),
  history: createWebHashHistory(),
})

export function resetRouter() {
  const asyncRouterNameArr = asyncRoutes.map((mItem) => mItem.name)
  asyncRouterNameArr.forEach((name) => {
    if (router.hasRoute(name)) {
      router.removeRoute(name)
    }
  })
}

export default router
