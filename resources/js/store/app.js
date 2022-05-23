import Cookies from 'js-cookie'
import {getLanguage} from '@/lang'
import {defineStore} from "pinia"
import defaultSettings from '../settings'

export const appStore = defineStore('app', {
  state: () => {
    return {
      sidebar: {
        opened: Cookies.get('sidebarStatus') ? !!+Cookies.get('sidebarStatus') : true,
        withoutAnimation: false,
      },
      device: 'desktop',
      language: getLanguage(),
      size: Cookies.get('size') || 'medium',
      cachedViews: [],
      cachedViewsDeep: [],
      settings: defaultSettings
    }
  },
  actions: {
    toggleSideBar() {
      this.$patch((state) => {
        state.sidebar.opened = !state.sidebar.opened;
        state.sidebar.withoutAnimation = false;
        if (state.sidebar.opened) {
          Cookies.set('sidebarStatus', 1);
        } else {
          Cookies.set('sidebarStatus', 0);
        }
      })
    },
    openSideBar(data) {
      this.$patch((state) => {
        Cookies.set('sidebarStatus', 1)
        state.sidebar.opened = data
      })
    },
    closeSideBar(withoutAnimation) {
      this.$patch((state) => {
        Cookies.set('sidebarStatus', 0)
        state.sidebar.opened = false
        state.sidebar.withoutAnimation = withoutAnimation;
      })
    },
    toggleDevice(device) {
      this.$patch((state) => {
        state.device = device
      })
    },
    setLanguage(language) {
      this.$patch((state) => {
        state.language = language
        Cookies.set('language', language)
      })
    },
    setSize(size) {
      this.$patch((state) => {
        state.size = size
        Cookies.set('size', size)
      })
    },

    /*keepAlive缓存*/
    addCachedView(view) {
      this.$patch((state) => {
        if (state.cachedViews.includes(view)) {
          return
        }
        state.cachedViews.push(view)
      })
    },
    delCachedView(view) {
      this.$patch((state) => {
        const index = state.cachedViews.indexOf(view)
        index > -1 && state.cachedViews.splice(index, 1)
      })
    },
    resetCachedView() {
      this.$patch((state) => {
        state.cachedViews = []
      })
    },
    /*third  keepAlive*/
    addCachedViewDeep(view) {
      this.$patch((state) => {
        if (state.cachedViewsDeep.includes(view)) {
          return
        }
        state.cachedViewsDeep.push(view)
      })
    },
    delCachedViewDeep(view) {
      this.$patch((state) => {
        const index = state.cachedViewsDeep.indexOf(view)
        index > -1 && state.cachedViewsDeep.splice(index, 1)
      })
    },
    resetCachedViewDeep() {
      this.$patch((state) => {
        state.cachedViewsDeep = []
      })
    },
    changeSetting(data) {
      this.$patch((state) => {
        state.settings = {...state.settings, ...data}
      })
    }
  }
})
