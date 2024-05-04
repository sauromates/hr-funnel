<script setup lang="ts">
import type { DropdownItem } from '#ui/types';

type NavigationDropdownProps = {
  /** Passed items will replace default navigation items but not authentication buttons */
  navItems?: DropdownItem[];
};

const props = withDefaults(defineProps<NavigationDropdownProps>(), {
  navItems: () => [{ label: 'Vacancies' }, { label: 'Campaigns' }, { label: 'Candidates' }],
});

const authStore = useAuthStore();
const { logout } = authStore;
const { user } = storeToRefs(authStore);

const items = computed<DropdownItem[][]>(() => {
  const navItems: DropdownItem[][] = [props.navItems];

  if (user.value) {
    navItems.push([{ label: 'Log out', click: () => logout() }, { label: user.value?.name }]);
  } else {
    navItems.push([
      { label: 'Log in', to: '/login' },
      { label: 'Sign up', to: '/' },
    ]);
  }

  return navItems;
});
</script>

<template>
  <UDropdown :items>
    <UButton variant="ghost" color="gray" aria-label="Menu" icon="i-heroicons-bars-3" />
  </UDropdown>
</template>
