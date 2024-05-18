<script setup lang="ts">
import type { FormError, FormSubmitEvent } from '#ui/types';
import {
  VacancyStatus,
  vacancyFormSchema as schema,
  type Vacancy,
  type VacancyForm,
  type VacancyFormErrorList,
} from '~/types/vacancy';

const toast = useToast();

const state = ref<Partial<VacancyForm>>({
  status: VacancyStatus.Draft,
});
const errors = ref<VacancyFormErrorList>();

const validate = (state: VacancyForm): FormError[] => {
  const formErrors: FormError[] = [];
  const parsedState = schema.safeParse(state);

  if (parsedState.error) {
    for (const [field, error] of Object.entries(parsedState.error.formErrors.fieldErrors)) {
      formErrors.push({
        path: field,
        message: error[0],
      });
    }
  }

  return formErrors;
};

const onSubmit = async (event: FormSubmitEvent<VacancyForm>): Promise<void> => {
  console.log('submit');
  const validated = schema.safeParse(event.data);
  if (!validated.success) {
    errors.value = validated.error.format();
    toast.add({ title: 'Invalid form' });
  }
  console.log('validated');

  const { error } = await useCreateItem<Vacancy>('/api/vacancies', event.data as Vacancy);
  if (error.value) {
    toast.add({ title: error.value.message });
  }
};
</script>

<template>
  <UForm :state :schema :validate class="space-y-4 w-full lg:w-1/2" @submit="onSubmit">
    <UFormGroup label="Title" name="title" required>
      <UInput v-model="state.title" />
    </UFormGroup>
    <UFormGroup label="Description" name="description">
      <UTextarea v-model="state.description" />
    </UFormGroup>
    <UFormGroup label="Short description" name="shortDescription">
      <UInput v-model="state.shortDescription" />
    </UFormGroup>
    <UFormGroup label="Status" name="status" required>
      <USelectMenu v-model="state.status" :options="Object.values(VacancyStatus)" />
    </UFormGroup>
    <UFormGroup label="Manager" name="manager">
      <USelectMenu v-model="state.manager" :options="[]" />
    </UFormGroup>
    <UFormGroup label="Minimum budget" name="minBudget" required>
      <UInput v-model="state.minBudget" type="number" min="0" />
    </UFormGroup>
    <UFormGroup label="Maximum budget" name="maxBudget">
      <UInput v-model="state.maxBudget" type="number" min="0" />
    </UFormGroup>
    <UFormGroup label="Requirements" name="requirements">
      <USelectMenu v-model="state.requirements" :options="[]" />
    </UFormGroup>

    <UButton type="submit">Save</UButton>
  </UForm>
</template>
