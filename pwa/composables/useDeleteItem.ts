import type { Item } from '~/types/item';

export async function useDeleteItem(item: Item) {
  const error: Ref<string | undefined> = ref(undefined);

  if (!item?.['@id']) {
    error.value = 'No item found. Please reload';
    return {
      error,
    };
  }

  const { pending } = await useApi(item['@id'] ?? '', { method: 'DELETE' });

  return {
    isLoading: pending,
    error,
  };
}
