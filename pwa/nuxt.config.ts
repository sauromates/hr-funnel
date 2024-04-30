// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  modules: ['@nuxt/eslint', '@nuxt/test-utils/module', '@nuxt/ui', '@pinia/nuxt', '@pinia-plugin-persistedstate/nuxt'],
  experimental: {
    // See https://github.com/api-platform/create-client/issues/382
    renderJsonPayloads: false,
  },
  runtimeConfig: {
    public: {
      baseURL: '',
      entrypoint: '',
    },
  },
});
