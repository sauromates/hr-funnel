import type { UseFetchOptions } from '#app';
import { defu } from 'defu';

/**
 * Custom `useFetch` composable utilizing API Platform plugin.
 *
 * @param {string} path API endpoint to call
 * @param {UseFetchOptions<T>} options Options from `FetchOptions` will be merged with defaults using `defu`
 *
 * @returns Preconfigured `useFetch` instance
 */
export async function useApi<T>(path: string, options?: UseFetchOptions<T>): Promise<ReturnType<typeof useFetch<T>>> {
  const { $apiClient, $defaultClientOptions } = useNuxtApp();
  const params = defu($defaultClientOptions, options) as UseFetchOptions<T>;

  return useFetch(path, { ...params, $fetch: $apiClient }) as ReturnType<typeof useFetch<T>>;
}
