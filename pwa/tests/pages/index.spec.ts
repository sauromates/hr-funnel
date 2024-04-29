// @vitest-environment nuxt
import { it, expect } from 'vitest';
import { setup, $fetch } from '@nuxt/test-utils';

await setup();

it('renders homepage', async () => {
  const page: string = await $fetch('/');
  expect(page).toContain('<div id="__nuxt">');
});
