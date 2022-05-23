<template>
  <div class="app-main" :class="{ 'show-tag-view': settings.showTagsView }">
    <router-view v-slot="{ Component }">
      <!--has transition  Judging by settings.mainNeedAnimation-->
      <transition v-if="settings.mainNeedAnimation" name="fade-transform" mode="out-in">
        <keep-alive :include="cachedViews">
          <component :is="Component" />
        </keep-alive>
      </transition>
      <!-- no transition -->
      <keep-alive v-else :include="cachedViews">
        <component :is="Component" />
      </keep-alive>
    </router-view>
  </div>
</template>

<script setup>
import { appStore } from '@/store/app'
import getPageTitle from '@/utils/get-page-title'
import i18n from '@/utils/i18n'

const {generateTitle} = i18n()
const route = useRoute()
const useAppStore = appStore()
const settings = computed(() => {
  return useAppStore.settings
})

const key = computed(() => route.path)

const cachedViews = computed(() => {
  return useAppStore.cachedViews
})

let oldRoute = {}
let deepOldRouter = null

const removeDeepChildren = (deepOldRouter) => {
  deepOldRouter.children?.forEach((fItem) => {
    useAppStore.delCachedViewDeep(fItem.name)
  })
}

watch(
  () => route.name,
  () => {
    // set page title
    document.title = getPageTitle(generateTitle(route.meta.title))
    // L2 and L3 routing cache
    const routerLevel = route.matched.length
    // L2 routing cache
    if (routerLevel === 2) {
      if (deepOldRouter?.name) {
        if (deepOldRouter.meta?.leaveRmCachePage && deepOldRouter.meta?.cachePage) {
          useAppStore.delCachedView(deepOldRouter.name)
          //remove the deepOldRouter‘s children component
          removeDeepChildren(deepOldRouter)
        }
      } else {
        if (oldRoute?.name) {
          if (oldRoute.meta?.leaveRmCachePage && oldRoute.meta?.cachePage) {
            useAppStore.delCachedView(oldRoute.name)
          }
        }
      }

      if (route.name) {
        if (route.meta?.cachePage) {
          useAppStore.addCachedView(route.name)
        }
      }
      deepOldRouter = null
    }

    // L3 routing cache
    if (routerLevel === 3) {
      //三级时存储当前路由对象的上一级
      const parentRoute = route.matched[1]
      //deepOldRouter不为空，且deepOldRouter不是当前路由的父对象，则需要清除deepOldRouter缓存
      //一般为三级路由跳转三级路由的情况
      if (deepOldRouter?.name && deepOldRouter.name !== parentRoute.name) {
        if (deepOldRouter.meta?.leaveRmCachePage && deepOldRouter.meta?.cachePage) {
          useAppStore.delCachedView(deepOldRouter.name)
          //remove the deepOldRouter‘s children component
          removeDeepChildren(deepOldRouter)
        }
      } else {
        //否则走正常两级路由处理流程
        if (oldRoute?.name) {
          if (oldRoute.meta?.leaveRmCachePage && oldRoute.meta?.cachePage) {
            useAppStore.delCachedViewDeep(oldRoute.name)
          }
        }
      }

      //取的是第二级的name
      if (parentRoute.name && parentRoute.meta?.cachePage) {
        deepOldRouter = parentRoute
        useAppStore.addCachedView(deepOldRouter.name)
        if (route.name) {
          if (route.meta?.cachePage) {
            //和第三级的name进行缓存
            useAppStore.addCachedViewDeep(route.name)
          }
        }
      }
    }
    oldRoute = JSON.parse(JSON.stringify({ name: route.name, meta: route.meta }))
  },
  { immediate: true }
)
</script>

<style scoped lang="scss">
.app-main {
  padding: $appMainPadding;
  /*50 = navbar  */
  position: relative;
  overflow: hidden;
}
.show-tag-view {
  height: calc(100vh - #{$navBarHeight} - #{$tagViewHeight}) !important;
}
.fixed-header + .app-main {
  padding-top: 50px;
}
</style>

<style lang="scss">
.el-popup-parent--hidden {
  .fixed-header {
    padding-right: 15px;
  }
}
</style>
