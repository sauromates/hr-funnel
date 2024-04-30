import type { SubmissionErrors } from '~/types/error';
import type { Item } from '~/types/item';

export async function useUpdateItem<T>(item: Item, payload: Item) {
  const updated: Ref<T | undefined> = ref(undefined);
  const violations: Ref<SubmissionErrors | undefined> = ref(undefined);

  const { data, pending, error } = await useApi(item['@id'] ?? '', {
    method: 'PUT',
    body: payload,
    headers: {
      Accept: 'application/ld+json',
      'Content-Type': 'application/ld+json',
    },

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

  updated.value = data.value as T;

  return {
    updated,
    isLoading: pending,
    error,
    violations,
  };
}
