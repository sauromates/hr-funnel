export async function navigateHome(): Promise<void> {
  const { isAuthenticated } = useAuthStore();
  const homePath: string = isAuthenticated ? '/dashboard' : '/';

  await navigateTo(homePath);
}
