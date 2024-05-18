// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  modules: [
    '@nuxt/ui',
    '@nuxt/eslint',
    '@nuxt/test-utils/module',
    '@pinia/nuxt',
    '@pinia-plugin-persistedstate/nuxt',
    '@vueuse/nuxt',
  ],
  experimental: {
    // See https://github.com/api-platform/create-client/issues/382
    renderJsonPayloads: false,
  },
  components: [{ path: '~/components', pathPrefix: false }],
  runtimeConfig: {
    public: {
      appName: 'HRF',
      baseURL: '',
      entrypoint: '',
    },
  },
  ui: {
    icons: ['heroicons', 'mdi'],
  },
});
