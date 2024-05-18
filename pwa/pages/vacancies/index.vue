<script setup lang="ts">
import type { Vacancy } from '~/types/vacancy';

useHead({ title: 'Vacancies' });
definePageMeta({ middleware: 'auth' });

const { items: vacancies } = await useFetchList<Vacancy>('/api/vacancies');
</script>

<template>
  <div>
    <UButton variant="link" to="/vacancies/create">Create new</UButton>
    <UCard v-for="vacancy in vacancies" :key="vacancy.id">
      <template #header>
        {{ vacancy.title }}
      </template>
      {{ vacancy.description ?? '' }}
    </UCard>
  </div>
</template>
