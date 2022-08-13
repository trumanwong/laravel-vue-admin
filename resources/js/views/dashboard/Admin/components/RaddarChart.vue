<template>
  <div :class="className" :style="{ height: height, width: width }"/>
</template>

<script setup>
import * as echarts from 'echarts'
import 'echarts/theme/macarons' // echarts theme
import {markRaw} from "vue"

const animationDuration = 3000

defineProps({
  className: {
    type: String,
    default: 'chart'
  },
  width: {
    type: String,
    default: '100%'
  },
  height: {
    type: String,
    default: '300px'
  },
})
const resData = reactive({
  chart: null,
})

onMounted(() => {
  nextTick(() => {
    initChart()
  })
})

onBeforeUnmount(() => {
  if (!resData.chart) {
    return
  }
  resData.chart.dispose()
  resData.chart = null
})

let {proxy} = getCurrentInstance()
const initChart = () => {
  resData.chart = markRaw(echarts.init(proxy.$el, 'macarons'))

  resData.chart.setOption({
    graphic: {
      elements: [
        {
          type: 'text',
          left: 'center',
          top: 'center',
          style: {
            text: `Laravel-Vue-Admin`,
            fontSize: 40,
            fontWeight: 'bold',
            lineDash: [0, 200],
            lineDashOffset: 0,
            fill: 'transparent',
            stroke: '#054b5d',
            lineWidth: 1
          },
          keyframeAnimation: {
            duration: 3000,
            loop: true,
            keyframes: [
              {
                percent: 0.7,
                style: {
                  fill: 'transparent',
                  lineDashOffset: 200,
                  lineDash: [200, 0]
                }
              },
              {
                // Stop for a while.
                percent: 0.8,
                style: {
                  fill: 'transparent'
                }
              },
              {
                percent: 1,
                style: {
                  fill: '#283443'
                }
              }
            ]
          }
        }
      ]
    }
  })
}
</script>

<style scoped lang="scss"></style>
