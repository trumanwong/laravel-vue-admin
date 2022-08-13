/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout/Layout.vue'

const chartsRoutes = {
  path: '/charts',
  component: Layout,
  redirect: 'noRedirect',
  name: 'Charts',
  meta: {
    title: 'charts',
    bootstrapIcon: 'bar-chart-fill',
    permissions: ['view menu charts'],
  },
  children: [
    {
      path: 'keyboard',
      component: () => import('@/views/charts/Keyboard.vue'),
      name: 'KeyboardChart',
      meta: {title: 'keyboardChart', bootstrapIcon: 'bar-chart-steps', noCache: true},
    },
    {
      path: 'line',
      component: () => import('@/views/charts/Line.vue'),
      name: 'LineChart',
      meta: {title: 'lineChart', bootstrapIcon: 'pie-chart-fill', noCache: true},
    },
    {
      path: 'mixchart',
      component: () => import('@/views/charts/MixChart.vue'),
      name: 'MixChart',
      meta: {title: 'mixChart', bootstrapIcon: 'file-earmark-bar-graph-fill', noCache: true},
    },
  ],
}

export default chartsRoutes
