<script setup lang="ts">
const config = useRuntimeConfig();
const appName = config.public.appName;

useHead({
  titleTemplate: (pageTitle?: string): string => {
    return pageTitle ? `${pageTitle} - ${appName}` : appName;
  },
  link: [
    { rel: 'apple-touch-icon', sizes: '180x180', href: '/apple-touch-icon.png' },
    { rel: 'icon', type: 'image/png', sizes: '32x32', href: '/favicon-32x32.png' },
    { rel: 'icon', type: 'image/png', sizes: '16x16', href: '/favicon-16x16.png' },
    { rel: 'manifest', href: '/site.webmanifest' },
  ],
});

useSeoMeta({
  ogTitle: appName,
  description: 'Automate HR processes and get valuable insights',
  ogDescription: 'Automate HR processes and get valuable insights',
  ogType: 'website',
  ogImage: {
    url: `${config.public.baseURL}/android-chrome-512x512.png`,
    type: 'image/png',
    width: 512,
    height: 512,
    alt: 'HRF Logo',
  },
});
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
    <UNotifications />
  </NuxtLayout>
</template>

<style>
body {
  @apply bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300;
}
</style>
