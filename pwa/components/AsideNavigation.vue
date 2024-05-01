<script setup lang="ts">
import type { VerticalNavigationLink } from '#ui/types';

const links: VerticalNavigationLink[] = [
  {
    label: 'Vacancies',
  },
  {
    label: 'Campaigns',
  },
  {
    label: 'Candidates',
  },
];
</script>

<template>
  <nav class="flex flex-col gap-6">
    <UButton v-for="(link, idx) in links" :key="idx" variant="link" :label="link.label" :padded="false" />
  </nav>
</template>
