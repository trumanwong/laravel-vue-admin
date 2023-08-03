import {createI18n} from 'vue-i18n'
import Cookies from 'js-cookie'
import elementEnLocale from 'element-plus/dist/locale/en.mjs' // element-plus lang
import elementZhLocale from 'element-plus/dist/locale/zh-cn.mjs'// element-plus lang
import enLocale from './en'
import zhLocale from './zh'

const messages = {
  en: {
    ...enLocale,
    ...elementEnLocale,
  },
  zh: {
    ...zhLocale,
    ...elementZhLocale,
  }
}

export function getLanguage() {
  const chooseLanguage = Cookies.get('language')
  if (chooseLanguage) {
    return chooseLanguage
  }

  // if has not choose language
  const language = (navigator.language || navigator.browserLanguage).toLowerCase()
  const locales = Object.keys(messages)
  for (const locale of locales) {
    if (language.indexOf(locale) > -1) {
      return locale
    }
  }
  return 'en'
}

const i18n = createI18n({
  messages,
  locale: getLanguage(),
  legacy: false,
  globalInjection: true,
})

export default i18n
