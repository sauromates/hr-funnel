import { mountSuspended } from '@nuxt/test-utils/runtime';
import type { VueWrapper } from '@vue/test-utils';
import UButton from '#ui/components/elements/Button.vue';
import NavigationAside from '~/components/NavigationAside.vue';
import NavigationDropdown from '~/components/NavigationDropdown.vue';

let sut: VueWrapper<InstanceType<typeof NavigationAside>>;
beforeEach(async () => {
  sut = await mountSuspended(NavigationAside);
});
afterEach(() => {
  sut.unmount();
});

it('has default navigation links', () => {
  expect(sut.vm.links).toStrictEqual([{ label: 'Vacancies' }, { label: 'Campaigns' }, { label: 'Candidates' }]);
});

it('accepts navigation links as props', async () => {
  const newLinks = [{ label: 'Test 1' }, { label: 'Test 2' }];

  sut = await mountSuspended(NavigationAside, { props: { links: newLinks } });

  expect(sut.vm.links).toStrictEqual(newLinks);
});

it('renders a button for each navigation link', () => {
  const defaultLinks = [{ label: 'Vacancies' }, { label: 'Campaigns' }, { label: 'Candidates' }];
  const buttons = sut.findAllComponents(UButton);

  expect(buttons.length).toEqual(defaultLinks.length);
});

it('has same default links as navigation dropdown', async () => {
  const navigationDropdown = await mountSuspended(NavigationDropdown);

  const dropdownDefaults = navigationDropdown.vm.navItems;
  const navigationDefaults = sut.vm.links;

  expect(navigationDefaults).toStrictEqual(dropdownDefaults);
});
