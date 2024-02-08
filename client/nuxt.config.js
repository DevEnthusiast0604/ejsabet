require('dotenv').config()
const { join } = require('path')
const { copySync, removeSync } = require('fs-extra')

module.exports = {
  ssr: false,

  srcDir: __dirname,

  env: {
    serverUrl: process.env.APP_URL,
    apiUrl: process.env.API_URL || process.env.APP_URL + '/api',
    appName: process.env.APP_NAME || 'Alzex',
    appLocale: process.env.APP_LOCALE || 'es',
    githubAuth: !!process.env.GITHUB_CLIENT_ID
  },

  head: {
    title: process.env.APP_NAME,
    titleTemplate: '%s - ' + process.env.APP_NAME,
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' },
      { hid: 'description', name: 'description', content: 'Nuxt.js project' },
      { name: 'mobile-web-app-capable', content: 'yes' },
      { name: 'apple-touch-fullscreen', content: 'yes' },
      { name: 'apple-mobile-web-app-capable', content: 'yes' },
      { name: 'apple-mobile-web-app-status-bar-style', content: 'black-translucent' },
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  pwa: {
    icon: {
      source: 'static/icon-pwa.png',
      fileName: 'icon-pwa.png'
    },
    meta: {
      title: process.env.APP_NAME,
      mobileAppIOS: true,
      theme_color: '#000000'
    },
    manifest: {
      name: process.env.APP_NAME,
      short_name: process.env.APP_NAME,
      lang: 'es',
      display: 'standalone',
      theme_color: '#000000',
      background_color: '#000000',
      icon: {
        "src": "icon-pwa.png",
        "sizes": [64, 120, 144, 152, 192, 384, 512],
        "type": "image/png",
        "purpose": ["any", "maskable"]
      }
       // useWebmanifestExtension:true,
    },
  },

  loading: { color: '#007bff' },

  router: {
    middleware: ['locale', 'check-auth']
  },

  // css: [
  //   { src: '~assets/sass/app.scss', lang: 'scss' }
  // ],

  plugins: [
    '~components/global',
    '~plugins/i18n',
    '~plugins/vform',
    '~plugins/axios',
    '~plugins/fontawesome',
    '~plugins/custom',
    '~plugins/roles.js',
    '~plugins/routing-alert.js',
    '~plugins/nuxt-client-init',
    { src: "~plugins/jquery", mode: "client" },
    { src: '~plugins/bootstrap', mode: 'client' }
  ],

  modules: [
    '@nuxtjs/router',
    '@nuxtjs/pwa',
    '@nuxtjs/firebase',
  ],

  buildModules: [
    '@nuxtjs/moment',
  ],

  build: {
    extractCSS: true
  },

  hooks: {
    generate: {
      done (generator) {
        // Copy dist files to public/_nuxt
        if (generator.nuxt.options.dev === false && generator.nuxt.options.mode === 'spa') {
          const publicDir = join(generator.nuxt.options.rootDir, 'public', '_nuxt')
          const swDir = join(generator.nuxt.options.rootDir, 'public')
          removeSync(publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '_nuxt'), publicDir)
          copySync(join(generator.nuxt.options.generate.dir, '200.html'), join(publicDir, 'index.html'))
          copySync(join(generator.nuxt.options.generate.dir, 'sw.js'), join(swDir, 'sw.js'))
          //copySync(join(generator.nuxt.options.generate.dir, 'manifest.json'), publicDir)
          removeSync(generator.nuxt.options.generate.dir)
        }
      }
    }
  },

  firebase: {
    config: {
      apiKey: process.env.FIREBASE_API_KEY,
      authDomain: process.env.FIREBASE_AUTH_DOMAIN,
      databaseURL: process.env.FIREBASE_DATABASE_URL,
      projectId: process.env.FIREBASE_PROJECT_ID,
      storageBucket: process.env.FIREBASE_STORAGE_BUCKET,
      messagingSenderId: process.env.FIREBASE_MESSAGING_SENDER_ID,
      appId: process.env.FIREBASE_APP_ID,
      measurementId: process.env.FIREBASE_MEASUREMENT_ID
    },
    services: {
      database: true // Just as example. Can be any other service.
    }
  },
}
