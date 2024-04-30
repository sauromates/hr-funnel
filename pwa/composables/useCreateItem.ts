import type { SubmissionErrors } from '~/types/error';
import type { Item } from '~/types/item';

export async function useCreateItem<T>(resource: string, payload: Item) {
  const created: Ref<T | undefined> = ref(undefined);
  const violations: Ref<SubmissionErrors | undefined> = ref(undefined);

  const { data, pending, error } = await useApi(resource, {
    method: 'POST',
    body: payload,

    onResponseError({ response }) {
      const data = response._data;
      const error = data['hydra:description'] || response.statusText;

      if (!data.violations) throw new Error(error);

      const errors: SubmissionErrors = { _error: error };
      data.violations.forEach((violation: { propertyPath: string; message: string }) => {
        errors[violation.propertyPath] = violation.message;
      });

      violations.value = errors;

      throw new SubmissionError(errors);
    },
  });

  created.value = data.value as T;

  return {
    created,
    isLoading: pending,
    error,
    violations,
  };
}
