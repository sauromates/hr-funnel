interface TokenStorage {
  token: Ref<string | null>;
  refreshToken: Ref<string | null>;
}

export const useJwt = (): TokenStorage => {
  const token = useState<string | null>('jwt', () => null);
  const refreshToken = useState<string | null>('jwt-refresh', () => null);

  return { token, refreshToken };
};
