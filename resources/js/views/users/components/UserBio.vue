<template>
  <el-card class="box-card user-bio">
    <div slot="header" class="clearfix">
      <span>{{ t('user.about_me') }}</span>
    </div>
    <div class="user-education user-bio-section">
      <div class="user-bio-section-header">
        <icon class-name="book" />
        <span>{{ t('user.education') }}</span>
      </div>
      <div class="user-bio-section-body">
        <div class="text-muted">
          B.S. in Communication Engineering from Hunan University of Technology
        </div>
      </div>
    </div>
    <div class="user-skills user-bio-section">
      <div class="user-bio-section-header">
        <icon class-name="list-check" />
        <span>{{ t('user.skills') }}</span>
      </div>
      <div class="user-bio-section-body">
        <div class="progress-item">
          <span>Laravel</span>
          <el-progress :percentage="90" />
        </div>
        <div class="progress-item">
          <span>Vue</span>
          <el-progress :percentage="68" />
        </div>
        <div class="progress-item">
          <span>JavaScript</span>
          <el-progress :percentage="12" />
        </div>
        <div class="progress-item">
          <span>HTML &amp; CSS</span>
          <el-progress :percentage="100" status="success" />
        </div>
      </div>
    </div>
  </el-card>
</template>

<script setup>
import {useI18n} from "vue-i18n";

const {t} = useI18n({useScope: 'global'})

</script>

<style lang="scss">
.user-bio {
  margin-top: 20px;
  color: #606266;
  span {
    padding-left: 4px;
  }
  .user-bio-section {
    font-size: 14px;
    padding: 15px 0;
    .user-bio-section-header {
      border-bottom: 1px solid #dfe6ec;
      padding-bottom: 10px;
      margin-bottom: 10px;
      font-weight: bold;
    }
  }
}
</style>
