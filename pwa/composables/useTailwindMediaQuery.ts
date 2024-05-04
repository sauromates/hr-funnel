export type TailwindMediaQuery = {
  mediaQuery: (breakpoint: TailwindBreakpoint) => string;
  determine: (breakpoint: TailwindBreakpoint) => Ref<boolean>;
  isSmallScreen: Ref<boolean>;
  isMediumScreen: Ref<boolean>;
  isLargeScreen: Ref<boolean>;
  isXlScreen: Ref<boolean>;
  is2XlScreen: Ref<boolean>;
};

export enum TailwindBreakpoint {
  Small = '640px',
  Medium = '768px',
  Large = '1024px',
  ExtraLarge = '1280px',
  ExtraLargeX2 = '1536px',
}

/**
 * Wrapper around `@vueuse/core useMediaQuery()` with predefined default Tailwind breakpoints.
 */
export function useTailwindMediaQuery(): TailwindMediaQuery {
  const mediaQuery = (breakpoint: TailwindBreakpoint): string => `(min-width: ${breakpoint})`;
  const determine = (breakpoint: TailwindBreakpoint): Ref<boolean> => useMediaQuery(mediaQuery(breakpoint));

  const isSmallScreen = determine(TailwindBreakpoint.Small);
  const isMediumScreen = determine(TailwindBreakpoint.Medium);
  const isLargeScreen = determine(TailwindBreakpoint.Large);
  const isXlScreen = determine(TailwindBreakpoint.ExtraLarge);
  const is2XlScreen = determine(TailwindBreakpoint.ExtraLargeX2);

  return {
    mediaQuery,
    determine,
    isSmallScreen,
    isMediumScreen,
    isLargeScreen,
    isXlScreen,
    is2XlScreen,
  };
}
