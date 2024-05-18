<script setup lang="ts">
useHead({ title: 'New vacancy' });
definePageMeta({ middleware: 'auth' });
</script>

<template>
  <VacancyCreateForm />
</template>
