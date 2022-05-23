import './styles/index.scss';
import {createApp} from 'vue'
import Cookies from 'js-cookie';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import router from './router';
import i18n from './lang'; // Internationalization
import App from './views/App.vue'
import SvgIcon from '@/icons/SvgIcon.vue'
import './permission'; // permission control

const app = createApp(App)
app.use(i18n)

app.config.globalProperties.productionTip = false;

app.use(ElementPlus, {
  size: Cookies.get('size') || 'medium', // set element-plus default size
  i18n: (key, value) => i18n.t(key, value),
});

// pinia
import {createPinia} from 'pinia'
app.use(createPinia())
// svg-icon
// import svg-icon doc in  https://github.com/anncwb/vite-plugin-svg-icons/blob/main/README.zh_CN.md
import 'virtual:svg-icons-register'
app.component('SvgIcon', SvgIcon)

// element svg icon
import ElSvgIcon from '@/components/ElSvgIcon.vue'

app.component('ElSvgIcon', ElSvgIcon)

app.use(router).mount('#app')