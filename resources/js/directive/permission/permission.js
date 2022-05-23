import {userStore} from '@/store/user';

export default {
  inserted(el, binding, vnode) {
    const {value} = binding;
    const permissions = userStore.permissions;

    if (value && value instanceof Array && value.length > 0) {
      const requiredPermissions = value;
      const hasPermission = permissions.some(permission => {
        return requiredPermissions.includes(permission);
      });

      if (!hasPermission) {
        el.parentNode && el.parentNode.removeChild(el);
      }
    } else {
      throw new Error(`Permissions are required! Example: v-permission="['manage user','manage permission']"`);
    }
  },
};
