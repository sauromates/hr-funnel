interface TokenStorage {
  accessToken: Ref<string | undefined>;
  refreshToken: Ref<string | undefined>;
  removeTokens: () => void;
}

export const useTokens = (): TokenStorage => {
  const accessToken = useState<string | undefined>('jwt', () => undefined);
  const refreshToken = useState<string | undefined>('jwt-refresh', () => undefined);

  const removeTokens = (): void => {
    accessToken.value = refreshToken.value = undefined;
  };

  return {
    accessToken,
    refreshToken,
    removeTokens,
  };
};
