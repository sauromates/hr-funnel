import type { UseFetchOptions } from '#app';

// const HttpUnauthorized: number = 401;

export async function useApi<T>(path: string, options: UseFetchOptions<T>) {
  const { token } = useJwt();
  const config = useRuntimeConfig();

  const response = await useFetch(path, {
    baseURL: config.public.baseURL,

    mode: 'cors',

    headers: {
      Accept: 'application/ld+json',
      Authorization: `Bearer ${token.value ?? ''}`,
    },

    onRequest({ options }) {
      // Request from the server should be directed to Docker service
      // instead of public URL. Keep it for SSR to work.
      if (import.meta.server) {
        options.baseURL = ENTRYPOINT; // Entrypoint defaults to `http://php`
      }
    },

    onResponseError({ response }) {
      // if (response.status === HttpUnauthorized) {

      // }

      const data = response._data;
      const error = data['hydra:description'] || response.statusText;

      throw new Error(error);
    },

    ...options,
  });

  return response;
}
