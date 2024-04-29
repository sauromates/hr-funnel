import type { UseFetchOptions } from "#app";

export async function useApi<T>(path: string, options: UseFetchOptions<T>) {
  const response = await useFetch(path, {
    baseURL: ENTRYPOINT,
    mode: 'cors',
    headers: {
      Accept: 'application/ld+json',
    },

    onResponseError({ response }) {
      const data = response._data;
      const error = data['hydra:description'] || response.statusText;

      throw new Error(error);
    },

    ...options,
  });

  return response;
}
