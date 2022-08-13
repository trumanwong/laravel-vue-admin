/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout/Layout.vue'

const errorRoutes = {
  path: '/error',
  component: Layout,
  redirect: 'noredirect',
  name: 'ErrorPages',
  meta: {
    title: 'errorPages',
    icon: '404',
  },
  hidden: true,
  children: [
    {
      path: '401',
      component: () => import('@/views/error-page/401.vue'),
      name: 'Page401',
      meta: { title: 'page401', noCache: true },
    },
    {
      path: '404',
      component: () => import('@/views/error-page/404.vue'),
      name: 'Page404',
      meta: { title: 'page404', noCache: true },
    },
  ],
}

export default errorRoutes
