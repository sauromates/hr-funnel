import type { UseFetchOptions } from '#app';
import { defu } from 'defu';

export async function useApi<T>(path: string, options?: UseFetchOptions<T>) {
  const { $apiClient, $defaultClientOptions } = useNuxtApp();
  const params = defu($defaultClientOptions, options) as UseFetchOptions<T>;

  return useFetch(path, { ...params, $fetch: $apiClient });
}
