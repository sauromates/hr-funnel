<script setup lang="ts">
const { isAuthenticated, user, logout } = useAuthStore();
</script>

<template>
  <UCard>
    <template #header>
      <div v-if="user" class="flex items-center justify-between">
        <h1>Welcome, {{ user.name }}</h1>
        <UButton @click="logout">Exit</UButton>
      </div>

      <h1 v-else>Please login to use the system</h1>
    </template>
    <UButton v-if="!isAuthenticated" to="/login">Proceed to login</UButton>
  </UCard>
</template>
