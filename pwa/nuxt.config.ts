// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  modules: ['@nuxt/eslint', '@pinia/nuxt', '@nuxt/test-utils/module'],
  experimental: {
    // See https://github.com/api-platform/create-client/issues/382
    renderJsonPayloads: false,
  },
  runtimeConfig: {
    public: {
      baseURL: '',
      entrypoint: '',
    }
  }
});
