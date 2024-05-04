import type { AuthStore } from '~/types/stores/auth-store';
import type { User, LoginCredentials } from '~/types/user';

export const useAuthStore = defineStore(
  'auth',
  (): AuthStore => {
    const user = ref<User | null>(null);
    const isAuthenticated = computed<boolean>(() => user.value !== null);

    async function login(credentials: LoginCredentials): Promise<void> {
      await useApi('/api/login_check', { method: 'POST', body: credentials, credentials: 'include' })
        .then(() => fetchProfile())
        .then(() => reloadNuxtApp({ path: '/' }));
    }

    async function fetchProfile(): Promise<User | undefined> {
      const { retrieved } = await useFetchItem<User>('/api/users/me');
      if (retrieved.value) {
        user.value = retrieved.value;
      }

      return retrieved.value;
    }

    async function logout(): Promise<void> {
      await useApi('/api/logout')
        .then(() => (user.value = null))
        .then(() => reloadNuxtApp());
    }

    return {
      user,
      isAuthenticated,
      login,
      logout,
      fetchProfile,
    };
  },
  { persist: true }
);
