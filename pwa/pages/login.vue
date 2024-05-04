<script setup lang="ts">
useHead({ title: 'Log in' });
definePageMeta({ layout: 'guest' });
</script>

<template>
  <UCard class="w-full md:w-1/2 lg:w-1/3">
    <template #header>
      <h1>Please login to use the system</h1>
    </template>
    <LoginForm />
  </UCard>
</template>
