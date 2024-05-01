<script setup lang="ts">
const authStore = useAuthStore();
const { user } = storeToRefs(authStore);
</script>

<template>
  <header class="min-w-screen flex items-center bg-gray-100 dark:bg-gray-800 border-b dark:border-b-gray-600 h-20">
    <div class="px-12 w-full flex items-center justify-between">
      <slot>
        <h1 class="font-semibold text-2xl text-gray-700 dark:text-gray-300">HR Funnel</h1>
      </slot>
      <div class="flex gap-4">
        <ColorModeButton />
        <div v-if="user" class="flex items-center gap-4">
          <p>{{ user.name }}</p>
          <UButton v-if="user" color="primary" variant="outline" label="Exit" @click="authStore.logout" />
        </div>
        <LoginButton v-else />
      </div>
    </div>
  </header>
</template>
