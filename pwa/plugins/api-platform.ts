import type { FetchOptions, $Fetch } from 'ofetch';
import { parse } from 'set-cookie-parser';
import { appendHeader } from 'h3';

interface TokenResponse {
  token?: string;
  refreshToken?: string;
}

export default defineNuxtPlugin(async () => {
  const event = useRequestEvent();
  const config = useRuntimeConfig(event);
  const cookies = useRequestHeaders(['cookie']);
  const { accessToken, refreshToken } = useTokens();

  const defaultClientOptions: FetchOptions = {
    baseURL: config.public.baseURL,
    mode: 'cors',
    headers: {
      Accept: 'application/ld+json',
    },
    retry: 2,
    retryStatusCodes: [
      HttpResponse.Unauthorized,
      HttpResponse.Forbidden,
      HttpResponse.GatewayTimeout,
      HttpResponse.ServiceUnavailable,
      HttpResponse.ServerError,
    ],
    onRequest: async ({ options }) => {
      if (import.meta.server) {
        // Request from the server should be directed to Docker service
        // instead of public URL. Keep it for SSR to work.
        options.baseURL = ENTRYPOINT; // Entrypoint defaults to `http://php`

        options.headers = {
          Authorization: `Bearer ${accessToken.value ?? ''}`,
          ...(cookies.cookie && cookies),
          ...options.headers,
        };
      }
    },
    onResponse: async ({ response }): Promise<void> => {
      if (import.meta.server) {
        const rawCookies: string[] = response.headers.getSetCookie();
        const cookies = parse(rawCookies);

        if (cookies.length > 0 && event !== undefined) {
          for (const cookie of cookies) {
            appendHeader(event, cookie.name, cookie.value);
          }
        }
      }
    },
    onResponseError: async ({ response }): Promise<void> => {
      if (response.status === HttpResponse.Unauthorized) {
        const { _data: tokens } = await $fetch.raw<TokenResponse>('/api/refresh_token', {
          method: 'GET',
          baseURL: ENTRYPOINT,
          mode: 'cors',
          headers: {
            ...(cookies.cookie && cookies),
          },
        });

        if (!tokens) {
          await navigateTo('/login');
          return;
        }

        accessToken.value = tokens.token;
        refreshToken.value = tokens.refreshToken;

        return;
      }

      const data = response._data;
      throw createError({
        message: data['hydra:description'] || data.message,
        statusCode: response.status,
        statusMessage: response.statusText,
      });
    },
  };

  const apiClient: $Fetch = $fetch.create(defaultClientOptions) as $Fetch;

  return { provide: { apiClient, defaultClientOptions } };
});
