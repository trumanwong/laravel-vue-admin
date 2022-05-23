<template>
  <div class="errPage-container">
    <el-button icon="arrow-left" class="pan-back-btn" @click="back">
      {{ $t('common.back') }}
    </el-button>
    <el-row>
      <el-col :span="12">
        <h1 class="text-jumbo text-ginormous">
          Oops!
        </h1>
        <h2>You don't have permission to go to this page</h2>
        <h6>If you are dissatisfied, please contact admistrator.</h6>
        <ul class="list-unstyled">
          <li>Or you can go:</li>
          <li class="link-type">
            <router-link to="/dashboard">
              {{ $t('route.dashboard') }}
            </router-link>
          </li>
          <li class="link-type">
            <a href="https://www.google.com/">just looking around</a>
          </li>
          <li><a href="#" @click.prevent="dialogVisible=true">show a picture</a></li>
        </ul>
      </el-col>
      <el-col :span="12">
        <img :src="noAuthGif" width="313" height="428" alt="Girl has dropped her ice cream.">
      </el-col>
    </el-row>
    <el-dialog :visible.sync="dialogVisible" title="Casual look">
      <img :src="noAuthJpg" class="pan-img">
    </el-dialog>
  </div>
</template>

<script>
import noAuthGif from '@/assets/401_images/401.gif'
import noAuthJpg from '@/assets/401_images/401.jpg'

export default {
  name: 'Page401',
  setup() {
    const resData = {
      noAuthGif: noAuthGif,
      noAuthJpg: noAuthJpg,
      dialogVisible: false,
    }

    const route = useRoute()
    const router = useRouter()
    const back = () => {
      if (route.query.noGoBack) {
        router.push({ path: '/dashboard' });
      } else {
        router.go(-1);
      }
    }

    return {
      ...toRefs(resData),
      back,
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .errPage-container {
    width: 800px;
    max-width: 100%;
    margin: 100px auto;
    .pan-back-btn {
      background: #008489;
      color: #fff;
      border: none!important;
    }
    .pan-gif {
      margin: 0 auto;
      display: block;
    }
    .pan-img {
      display: block;
      margin: 0 auto;
      width: 100%;
    }
    .text-jumbo {
      font-size: 60px;
      font-weight: 700;
      color: #484848;
    }
    .list-unstyled {
      font-size: 14px;
      li {
        padding-bottom: 5px;
      }
      a {
        color: #008489;
        text-decoration: none;
        &:hover {
          text-decoration: underline;
        }
      }
    }
  }
</style>
