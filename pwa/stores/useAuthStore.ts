import type { User } from '~/types/user';

export const useAuthStore = defineStore(
  'auth',
  () => {
    const user = ref<User | null>(null);

    const isAuthenticated = computed<boolean>(() => user.value !== null);

    const login = async (credentials: Pick<User, 'email' | 'password'>): Promise<void> => {
      const { data } = await useApi<{ token: string }>('/api/login_check', {
        method: 'POST',
        body: credentials,
      });

      if (data.value) {
        await fetchProfile().then(() => reloadNuxtApp({ path: '/' }));
      }
    };

    const fetchProfile = async (): Promise<void> => {
      const { retrieved } = await useFetchItem<User>('/api/users/me');
      if (retrieved.value) {
        user.value = retrieved.value;
      }
    };

    const logout = async (): Promise<void> => {
      await useApi('/api/logout', { method: 'GET' }).then(() => {
        user.value = null;
        reloadNuxtApp();
      });
    };

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
