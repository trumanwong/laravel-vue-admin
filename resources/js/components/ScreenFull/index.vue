<template>
  <div id="screenfull" class="pl-1 pr-1 mtPx-2" >
      <icon :class-name="isFullscreen ? 'fullscreen-exit' : 'fullscreen'" class="nav-svg-icon" @click="click"/>
  </div>
</template>

<script setup>
import screenfull from 'screenfull'
import { onMounted, onUnmounted, reactive, toRefs } from 'vue'
import { ElMessage } from 'element-plus'

const resData = reactive({
  isFullscreen: false
})

onMounted(() => {
  init()
})
onUnmounted(() => {
  destroy()
})

const click = () => {
  if (!screenfull.isEnabled) {
    ElMessage({
      message: 'you browser can not work',
      type: 'warning'
    })
    return false
  }
  screenfull.toggle()
}
const change = () => {
  resData.isFullscreen = screenfull.isFullscreen
    console.log(resData.isFullscreen)
}
const init = () => {
  if (screenfull.isEnabled) {
    screenfull.on('change', change)
  }
}
const destroy = () => {
  if (screenfull.enabled) {
    screenfull.off('change', change)
  }
}

let { isFullscreen } = toRefs(resData)
</script>

<style lang="scss">
.nav-svg-icon {
  font-size: 18px;
  color: #5a5e66;
  margin-top: 4px;
}
</style>
