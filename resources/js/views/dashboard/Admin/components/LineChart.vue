<template>
  <div :style="{width: width, height: height}"></div>
</template>

<script setup>
import * as echarts from 'echarts'
import {getCurrentInstance, nextTick, onBeforeUnmount, onMounted, reactive, watch, markRaw} from "vue"
//获取store和router
let {proxy} = getCurrentInstance()
const props = defineProps({
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
    default: '350px'
  },
  autoResize: {
    type: Boolean,
    default: true
  },
  chartData: {
    type: Object,
    required: true
  }
})
const state = reactive({
  chart: null
})

watch(
    () => props.chartData,
    (val) => {
      setOptions(val)
    },
    {immediate: false}
)

onMounted(() => {
  nextTick(() => {
    initChart()
  })
})
onBeforeUnmount(() => {
  if (!state.chart) {
    return
  }
  state.chart.dispose()
  state.chart = null
})
const initChart = () => {
  state.chart = markRaw(echarts.init(proxy.$el, 'macarons'))
  setOptions(props.chartData)
}
const setOptions = ({expectedData, actualData} = {}) => {
  state.chart.setOption({
    tooltip: {
      trigger: 'axis'
    },
    legend: {
      data: ['expected', 'actual']
    },
    grid: {
      left: 10,
      right: 10,
      containLabel: true
    },
    toolbox: {
      feature: {
        saveAsImage: {}
      }
    },
    xAxis: {
      data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      boundaryGap: false,
      axisTick: {
        show: false
      }
    },
    yAxis: {
      axisTick: {
        show: false
      }
    },
    series: [
      {
        name: 'expected',
        smooth: true,
        type: 'line',
        data: expectedData,
        animationDuration: 2800,
        animationEasing: 'cubicInOut'
      },
      {
        name: 'actual',
        smooth: true,
        type: 'line',
        data: actualData,
        animationDuration: 2800,
        animationEasing: 'quadraticOut'
      }
    ],
    color: [
      '#5470c6',
      '#91cc75'
    ]
  })
}
</script>

<style scoped lang="scss"></style>
