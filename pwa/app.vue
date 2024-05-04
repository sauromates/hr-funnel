<script setup lang="ts">
const config = useRuntimeConfig();
const appName = config.public.appName;

useHead({
  titleTemplate: (pageTitle?: string): string => {
    return pageTitle ? `${pageTitle} - ${appName}` : appName;
  },
});
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
    <UNotifications />
  </NuxtLayout>
</template>
