import colors from 'vuetify/es5/util/colors'

export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    titleTemplate: '%s - app',
    title: 'app',
    htmlAttrs: {
      lang: 'en'
    },
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' },
      { name: 'format-detection', content: 'telephone=no' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [
  ],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    '~/plugins/vee-validate',
    '~/plugins/i18n',
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/vuetify
    '@nuxtjs/vuetify',
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    '@nuxtjs/auth-next',
    '@nuxtjs/axios',
    ['@nuxtjs/i18n', {
      lazy: true,
      loadLanguagesAsync: true,
      locales: [// 使用する言語の設定
        {
          name: 'English',
          code: 'en',
          iso: 'en-US',
          file: 'en.js'
        },
        {
          name: '日本語',
          code: 'ja',
          iso: 'ja-JA',
          file: 'ja.js'
        },
      ],
      langDir: 'locales/', // 翻訳ファイルのディレクトリパス
      defaultLocale: 'en',// デフォルトの言語
      fallbackLocale: 'en',
      strategy: 'prefix',// URLに言語のプレフィックスを追加する
      detectBrowserLanguage: {
        useCookie: true,
        cookieKey: 'i18n_redirected',
      },
      rootRedirect: 'en',
      vueI18n: {
        fallbackLocale: 'en'// 翻訳ファイルが見つからなかった場合の言語を指定
      },
      vueI18nLoader: true,
    }],
  ],

  auth: {
    redirect: {
      login: '/login',// User will be redirected to this path if login is required.
      logout: '/login',// User will be redirected to this path if after logout, current route is protected.
      callback: false,
      home: false// User will be redirected to this path after login.
    },
    strategies: {
      laravelSanctum: {
        provider: 'laravel/sanctum',
        url: 'http://localhost:8080',
        endpoints: {
          login: { url: '/login', method: 'post', propertyName: false },
          user: { url: '/api/user', method: 'get', propertyName: false },
          logout: false
        },
      },
    },
  },

  axios: {
    baseURL: "http://localhost:8080",
    browserBaseURL: "http://localhost:8080",
    credentials: true,
  },

  router: {
    middleware: [
      'guest',
    ],
  },

  // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
  vuetify: {
    customVariables: ['~/assets/variables.scss'],
    theme: {
      dark: false,
      themes: {
        light: {
          primary: colors.blue.darken2,
          accent: colors.grey.darken3,
          secondary: colors.amber.darken3,
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3,
          background: colors.grey.lighten4,
          text: colors.grey.black,
        }
      }
    }
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {
    transpile: [
      "vee-validate/dist/rules"
    ]
  }
}
