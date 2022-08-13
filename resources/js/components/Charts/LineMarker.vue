<template>
  <div :id="id" :class="className" :style="{height:height,width:width}"/>
</template>

<script setup>
import * as echarts from 'echarts'
import Resource from "../../api/resource"
import {markRaw} from "vue"

const resource = new Resource('requests')
const props = defineProps({
  className: {
    type: String,
    default: 'chart',
  },
  id: {
    type: String,
    default: 'chart',
  },
  width: {
    type: String,
    default: '200px',
  },
  height: {
    type: String,
    default: '200px',
  },
})

const resData = reactive({
  chart: null,
  times: []
})

for (let i = 0; i < 24; i++) {
  resData.times.push((i < 10 ? '0' + i : i) + ':00')
}

const initChart = () => {
  resData.chart = markRaw(echarts.init(document.getElementById(props.id)))

  resource.list().then((res) => {
    const {data} = res
    resData.chart.setOption({
      backgroundColor: '#394056',
      title: {
        top: 20,
        text: 'Requests',
        textStyle: {
          fontWeight: 'normal',
          fontSize: 16,
          color: '#F1F1F3',
        },
        left: '1%',
      },
      tooltip: {
        trigger: 'axis',
        axisPointer: {
          lineStyle: {
            color: '#57617B',
          },
        },
      },
      legend: {
        top: 20,
        icon: 'rect',
        itemWidth: 14,
        itemHeight: 5,
        itemGap: 13,
        data: ['API'],
        right: '4%',
        textStyle: {
          fontSize: 12,
          color: '#F1F1F3',
        },
      },
      grid: {
        top: 100,
        left: '2%',
        right: '2%',
        bottom: '2%',
        containLabel: true,
      },
      xAxis: [{
        type: 'category',
        boundaryGap: false,
        axisLine: {
          lineStyle: {
            color: '#57617B',
          },
        },
        data: resData.times,
      }],
      yAxis: [{
        type: 'value',
        name: '(%)',
        axisTick: {
          show: false,
        },
        axisLine: {
          lineStyle: {
            color: '#57617B',
          },
        },
        axisLabel: {
          margin: 10,
          textStyle: {
            fontSize: 14,
          },
        },
        splitLine: {
          lineStyle: {
            color: '#57617B',
          },
        },
      }],
      series: [{
        name: 'API',
        type: 'line',
        smooth: true,
        symbol: 'circle',
        symbolSize: 5,
        showSymbol: false,
        lineStyle: {
          normal: {
            width: 1,
          },
        },
        areaStyle: {
          normal: {
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
              offset: 0,
              color: 'rgba(137, 189, 27, 0.3)',
            }, {
              offset: 0.8,
              color: 'rgba(137, 189, 27, 0)',
            }], false),
            shadowColor: 'rgba(0, 0, 0, 0.1)',
            shadowBlur: 10,
          },
        },
        itemStyle: {
          normal: {
            color: 'rgb(137,189,27)',
            borderColor: 'rgba(137,189,2,0.27)',
            borderWidth: 12,
          },
        },
        data: data,
      }],
    })
  })
}

onMounted(() => {
  initChart()
})

onBeforeUnmount(() => {
  if (!resData.chart) {
    return
  }
  resData.chart.dispose()
  resData.chart = null
})
</script>
