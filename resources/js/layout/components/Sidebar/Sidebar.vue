<template>
  <div id="Sidebar" class="reset-menu-style">
    <!--logo-->
    <Logo v-if="settings.sidebarLogo" :collapse="!isCollapse" />
    <!--router nav-->
    <el-scrollbar>
      <el-menu
        class="el-menu-vertical"
        :default-active="activeMenu"
        :collapse="!isCollapse"
        :unique-opened="false"
        :collapse-transition="false"
        :background-color="scssJson.menuBg"
        :text-color="scssJson.menuText"
        :active-text-color="scssJson.menuActiveText"
        mode="vertical"
      >
        <sidebar-item v-for="route in routes" :key="route.path" :item="route" :base-path="route.path" />
      </el-menu>
    </el-scrollbar>
  </div>
</template>

<script setup>
import Logo from './Logo.vue'
import SidebarItem from './SidebarItem.vue'
//get scss variable
import scssExportJson from '@/styles/variables-to-js.scss'
import { appStore } from '@/store/app'
import { permissionStore } from '@/store/permission'

const useAppStore = appStore()
const settings = computed(() => {
  return useAppStore.settings
})

const route = useRoute()
const usePermissionStore = permissionStore()
const routes = computed(() => {
  return usePermissionStore.routes
})
const isCollapse = computed(() => {
  return useAppStore.sidebar.opened
})

//change  scss variable to js
const dillScssExportToJson = (scssExportJson) => {
  const jsonString = scssExportJson.replace(/:export\s*/, '').replace(/[\s+\r\n]/g, '')
  const scssJson = {}
  jsonString
    .slice(1, jsonString.length - 2)
    .split(';')
    .forEach((fItem) => {
      const arr = fItem.split(':')
      scssJson[arr[0]] = arr[1]
    })
  return scssJson
}
const scssJson = dillScssExportToJson(scssExportJson)
const activeMenu = computed(() => {
  const { meta, fullPath } = route
  // if set path, the sidebar will highlight the path you set
  if (meta.activeMenu) {
    return meta.activeMenu
  }
  return fullPath
})
</script>

<style lang="scss">
.reset-menu-style {
  .el-menu {
    border-right: none;
  }
  .el-scrollbar__wrap {
    padding-bottom: 10vh;
  }
}

.el-menu-vertical {
  width: $sideBarWidth;

  .el-menu-item, .el-sub-menu {
    svg {
      margin-right: 16px;
      position: relative;
      fill: currentColor;
      vertical-align: -7px !important;
    }
  }
}
</style>
