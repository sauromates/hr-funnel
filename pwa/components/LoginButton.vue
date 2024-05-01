<script setup lang="ts">
const loginModal = ref<boolean>(false);
</script>

<template>
  <UButton variant="outline" @click="loginModal = true">Login</UButton>
  <UModal v-model="loginModal">
    <UCard>
      <template #header>
        <h1>Please login to use the system</h1>
      </template>
      <LoginForm />
    </UCard>
  </UModal>
</template>
