export const ENTRYPOINT: string | undefined =
  typeof window === 'undefined'
    ? process.env.NUXT_PUBLIC_ENTRYPOINT
    : window.origin;
