import type { LoginCredentials, User } from '~/types/user';

/**
 * Provides general authentication features.
 */
export interface AuthStore {
  user: Ref<User | null>;
  isAuthenticated: ComputedRef<boolean>;
  login: (credentials: LoginCredentials) => Promise<void>;
  logout: () => Promise<void>;
  fetchProfile: () => Promise<User | undefined>;
}
