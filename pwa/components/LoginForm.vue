<script setup lang="ts">
import { z } from 'zod';
import type { FormSubmitEvent } from '#ui/types';

const schema = z.object({
  email: z.string().email(),
  password: z.string(),
});

type LoginFormSchema = z.output<typeof schema>;

const state: { email?: string; password?: string } = reactive({
  email: undefined,
  password: undefined,
});

const { login } = useAuthStore();
const onSubmit = async (event: FormSubmitEvent<LoginFormSchema>): Promise<void> => {
  await login(event.data);
};
</script>

<template>
  <UForm :schema :state class="my-auto space-y-4" @submit="onSubmit">
    <UFormGroup label="Email" name="email" required>
      <UInput v-model="state.email" />
    </UFormGroup>
    <UFormGroup label="Password" name="password" required>
      <UInput v-model="state.password" type="password" />
    </UFormGroup>
    <UButton type="submit">Login!</UButton>
  </UForm>
</template>
