import type { FetchAllData } from "~/types/api";
import type { PagedCollection } from "~/types/collection";
import type { View } from "~/types/view";

export async function useFetchList<T>(resource: string): Promise<FetchAllData<T>> {
  const route = useRoute();

  const items: Ref<T[]> = ref([]);
  const view: Ref<View | undefined> = ref(undefined);
  const hubUrl: Ref<URL | undefined> = ref(undefined);

  const page = ref(route.params.page);

  const { data, pending, error } = await useApi<T>(resource, {
    params: { page },

    onResponse({ response }) {
      hubUrl.value = extractHubURL(response);
    },
  });

  const value = data.value as PagedCollection<T>;
  items.value = value['hydra:member'];
  view.value = value['hydra:view'];

  return {
    items,
    view,
    isLoading: pending,
    error,
    hubUrl,
  };
}
