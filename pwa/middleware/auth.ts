// eslint-disable-next-line @typescript-eslint/no-unused-vars
export default defineNuxtRouteMiddleware((to, from) => {
  const { isAuthenticated } = useAuthStore();

  if (!isAuthenticated) {
    return navigateTo('/login');
  }
});
