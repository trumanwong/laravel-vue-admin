/** When your routing table is too long, you can split it into small modules**/
import Layout from '@/layout/Layout.vue'

const adminRoutes = {
  path: '/administrator',
  component: Layout,
  redirect: '/administrator/users',
  name: 'Administrator',
  alwaysShow: true,
  meta: {
    title: 'administrator',
    bootstrapIcon: 'person-workspace',
    permissions: ['view menu administrator'],
  },
  children: [
    /** User managements */
    {
      path: 'users/edit/:id(\\d+)',
      component: () => import('@/views/users/UserProfile.vue'),
      name: 'UserProfile',
      meta: { title: 'userProfile', noCache: true, permissions: ['manage user'] },
      hidden: true,
    },
    {
      path: 'users',
      component: () => import('@/views/users/List.vue'),
      name: 'UserList',
      meta: {title: 'users', bootstrapIcon: 'people', permissions: ['manage user']},
    },
    /** Role and permission */
    {
      path: 'roles',
      component: () => import('@/views/role-permission/List.vue'),
      name: 'RoleList',
      meta: {title: 'rolePermission', bootstrapIcon: 'person-lines-fill', permissions: ['manage permission']},
    },
  ],
}

export default adminRoutes
