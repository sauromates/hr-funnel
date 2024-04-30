import type { UseFetchOptions } from '#app';
import setCookie from 'set-cookie-parser';

export async function useApi<T>(path: string, options: UseFetchOptions<T>) {
  const event = useRequestEvent();
  const config = useRuntimeConfig(event);

  const response = await useFetch(path, {
    baseURL: config.public.baseURL,

    mode: 'cors',

    headers: {
      Accept: 'application/ld+json',
    },

    onRequest({ options }) {
      if (import.meta.server) {
        // Request from the server should be directed to Docker service
        // instead of public URL. Keep it for SSR to work.
        options.baseURL = ENTRYPOINT; // Entrypoint defaults to `http://php`

        const headers = options.headers;
        const clientCookies = useRequestHeaders(['cookie']);

        options.headers = {
          ...headers,
          ...(clientCookies.cookie && clientCookies),
        };
      }
    },

    onResponse({ response }): void {
      if (import.meta.server) {
        const rawCookies: string[] = response.headers.getSetCookie();
        const cookies = setCookie.parse(rawCookies);

        if (cookies.length > 0 && event !== undefined) {
          for (const cookie of cookies) {
            appendHeader(event, cookie.name, cookie.value);
          }
        }
      }
    },

    async onResponseError({ response }) {
      const data = response._data;
      throw createError({
        message: data['hydra:description'] || data.message,
        statusCode: response.status,
        statusMessage: response.statusText,
      });
    },

    ...options,
  });

  return response;
}
