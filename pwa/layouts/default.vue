<template>
  <div class="flex flex-col gap-4 min-h-screen">
    <BaseHeader />
    <div class="flex gap-12 container mx-auto my-6 flex-1">
      <aside class="flex flex-col gap-4 basis-1/6">
        <AsideNavigation />
      </aside>
      <main class="basis-5/6 flex flex-col items-start">
        <slot />
      </main>
    </div>
  </div>
</template>

<style>
body {
  @apply bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 antialiased;
}
</style>
