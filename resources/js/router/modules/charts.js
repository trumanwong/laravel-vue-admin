/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout/Layout.vue'

const chartsRoutes = {
  path: '/charts',
  component: Layout,
  redirect: 'noRedirect',
  name: 'Charts',
  meta: {
    title: 'charts',
    icon: 'chart',
    permissions: ['view menu charts'],
  },
  children: [
    {
      path: 'keyboard',
      component: () => import('@/views/charts/Keyboard.vue'),
      name: 'KeyboardChart',
      meta: { title: 'keyboardChart', elSvgIcon: 'DataBoard', noCache: true },
    },
    {
      path: 'line',
      component: () => import('@/views/charts/Line.vue'),
      name: 'LineChart',
      meta: { title: 'lineChart', elSvgIcon: 'DataLine', noCache: true },
    },
    {
      path: 'mixchart',
      component: () => import('@/views/charts/MixChart.vue'),
      name: 'MixChart',
      meta: { title: 'mixChart', elSvgIcon: 'Promotion',noCache: true },
    },
  ],
}

export default chartsRoutes
