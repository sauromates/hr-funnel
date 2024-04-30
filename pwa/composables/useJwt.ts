export const useJwt = () => {
  const token = useState<string|null>('jwt', () => null);
  const refreshToken = useState<string|null>('jwt-refresh', () => null);

  return { token, refreshToken };
}
