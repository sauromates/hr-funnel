import type { User } from "~/types/user";

export const useAuthStore = defineStore('auth', () => {
  const {token} = useJwt();
  const user = ref<User|null>(null);

  const login = async (credentials: Pick<User, 'email'|'password'>): Promise<void> => {
    const { data } = await useApi<{token: string}>('/api/login_check', {
      method: 'POST',
      body: credentials,
    });

    if (data.value) {
      token.value = data.value.token;
    }
  }

  const fetchProfile = async (): Promise<void> => {
    const { retrieved } = await useFetchItem<User>('/api/users/me');
    if (retrieved.value) {
      user.value = retrieved.value;
    }
  }

  const logout = (): void => {
    token.value = null;
    user.value = null;
  }

  return {
    token,
    user,
    login,
    logout,
    fetchProfile,
  }
});
