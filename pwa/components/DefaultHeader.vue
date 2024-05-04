<script setup lang="ts">
const authStore = useAuthStore();
const { logout } = authStore;
const { user } = storeToRefs(authStore);
const { isMediumScreen } = useTailwindMediaQuery();
</script>

<template>
  <header
    class="flex items-center justify-between py-4 px-2 sm:px-4 lg:px-12 w-full bg-gray-100 dark:bg-gray-800 border-b dark:border-b-gray-600"
  >
    <slot name="left">
      <h1 class="font-semibold text-xl cursor-pointer" @click="navigateHome">HR Funnel</h1>
    </slot>
    <div class="flex gap-4">
      <ColorModeButton />

      <div v-if="isMediumScreen">
        <div v-if="user" class="flex items-center gap-4">
          <p>{{ user.name }}</p>
          <UButton v-if="user" color="primary" variant="outline" label="Log out" @click="logout" />
        </div>
        <div v-else class="flex items-center gap-4">
          <LoginButton />
          <UButton variant="outline" label="Signup" />
        </div>
      </div>

      <slot v-else name="right">
        <NavigationDropdown />
      </slot>
    </div>
  </header>
</template>
