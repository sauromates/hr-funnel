import type { FetchItemData } from "~/types/api";

export async function useFetchItem<T>(path: string): Promise<FetchItemData<T>> {
  const retrieved: Ref<T | undefined> = ref(undefined);
  const hubUrl: Ref<URL | undefined> = ref(undefined);

  const { data, pending, error } = await useApi<T>(path, {
    onResponse({ response }) {
      retrieved.value = response._data;
      hubUrl.value = extractHubURL(response);
    },
  });

  retrieved.value = data.value as T;

  return {
    retrieved,
    isLoading: pending,
    error,
    hubUrl,
  };
}
