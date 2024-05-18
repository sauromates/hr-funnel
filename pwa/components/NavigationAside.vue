<script setup lang="ts">
import type { VerticalNavigationLink } from '#ui/types';

type NavigationAsideProps = {
  links?: VerticalNavigationLink[];
};

withDefaults(defineProps<NavigationAsideProps>(), {
  links: () => [{ label: 'Vacancies', to: '/vacancies' }, { label: 'Campaigns' }, { label: 'Candidates' }],
});
</script>

<template>
  <aside>
    <nav class="flex flex-col gap-6">
      <UButton
        v-for="(link, idx) in links"
        :key="idx"
        variant="ghost"
        :label="link.label"
        :to="link.to ?? '/'"
        class="transition ease-in-out hover:scale-105 duration-300"
      />
    </nav>
  </aside>
</template>
