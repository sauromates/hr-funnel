import { mountSuspended } from '@nuxt/test-utils/runtime';
import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import type { DropdownItem } from '#ui/types';
import UDropdown from '#ui/components/elements/Dropdown.vue';
import UButton from '#ui/components/elements/Button.vue';
import NavigationDropdown from '~/components/NavigationDropdown.vue';

it('can be mounted', async () => {
  const component = mount(NavigationDropdown);
  expect(component.exists()).toBeTruthy();
});

it('has default navigation items', async () => {
  const component = await mountSuspended(NavigationDropdown);
  const defaults = [{ label: 'Vacancies' }, { label: 'Campaigns' }, { label: 'Candidates' }];

  expect(component.vm.navItems).toStrictEqual(defaults);

  component.vm.navItems.forEach((item) => {
    expectTypeOf(item).toEqualTypeOf<DropdownItem>();
  });
});

it('includes auth buttons for guest user', async () => {
  const component = await mountSuspended(NavigationDropdown);
  const dropdown = component.getComponent(UDropdown);

  expect(dropdown.vm.items).toContainEqual([
    { label: 'Log in', to: '/login' },
    { label: 'Sign up', to: '/' },
  ]);
});

it('includes profile actions for logged in user', async () => {
  const component = mount(NavigationDropdown, {
    global: {
      plugins: [
        createTestingPinia({
          initialState: { auth: { user: { name: 'Test' } } },
        }),
      ],
    },
  });
  const { user } = useAuthStore();
  if (!user?.name) {
    throw new Error('Failed to mock Pinia store');
  }

  const dropdownItems = component.getComponent(UDropdown).vm.items;
  expect(dropdownItems).toHaveLength(2);

  const profileActions = dropdownItems[1];
  expect(profileActions).toHaveLength(2);

  expect(profileActions[0]).toHaveProperty('label', 'Log out');
  expect(profileActions[1]).toEqual({ label: user.name });
});

it('has correct open button', () => {
  const component = mount(NavigationDropdown);
  const button = component.getComponent(UButton);

  expect(button.isVisible()).toBeTruthy();
  expect(button.vm.variant).toBe('ghost');
  expect(button.vm.color).toBe('gray');
  expect(button.vm.icon).toBe('i-heroicons-bars-3');
});
