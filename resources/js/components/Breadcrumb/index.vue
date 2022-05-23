<template>
  <el-breadcrumb class="app-breadcrumb" separator="/">
    <transition-group name="breadcrumb">
      <el-breadcrumb-item v-for="(item, index) in levelList" :key="item.path">
        <span v-if="item.redirect==='noredirect'||index==levelList.length-1" class="no-redirect">
          {{ generateTitle(item.meta.title) }}
        </span>
        <a v-else @click.prevent="handleLink(item)">{{ generateTitle(item.meta.title) }}</a>
      </el-breadcrumb-item>
    </transition-group>
  </el-breadcrumb>
</template>

<script>
import i18n from '@/utils/i18n';
import {pathToRegexp} from 'path-to-regexp';
import {onBeforeMount, reactive, toRefs, watch} from "vue";
import {useRoute, useRouter} from "vue-router";

export default {
  name: 'Breadcrumb',
  setup() {
    const resData = reactive({
      levelList: null,
    })

    const {generateTitle} = i18n()

    const route = useRoute()
    const router = useRouter()
    watch(
      () => route.path,
      () => {
        getBreadcrumb()
      }
    )

    onBeforeMount(() => {
      getBreadcrumb()
    })
    const getBreadcrumb = () => {
      let matched = route.matched.filter(item => item.name);

      const first = matched[0];
      if (first && first.name.trim().toLocaleLowerCase() !== 'Dashboard'.toLocaleLowerCase()) {
        matched = [{path: '/dashboard', meta: {title: 'dashboard'}}].concat(matched);
      }

      resData.levelList = matched.filter(
        item => item.meta && item.meta.title && item.meta.breadcrumb !== false
      );
    }
    const pathCompile = (path) => {
      const {params} = route;
      var toPath = pathToRegexp.compile(path);
      return toPath(params);
    }
    const handleLink = (item) => {
      const {redirect, path} = item;
      if (redirect) {
        router.push(redirect);
        return;
      }
      router.push(pathCompile(path));
    }

    return {
      ...toRefs(resData),
      getBreadcrumb,
      pathCompile,
      handleLink,
      generateTitle
    }
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.app-breadcrumb.el-breadcrumb {
  display: inline-block;
  font-size: 14px;
  line-height: 50px;
  margin-left: 10px;

  .no-redirect {
    color: #97a8be;
    cursor: text;
  }
}
</style>
