<template>
  <UContainer :ui="{ base: 'flex flex-col gap-4 min-h-screen antialiased', constrained: '!px-0' }">
    <DefaultHeader />
    <UContainer id="container" :ui="{ base: 'w-full grow' }">
      <main class="flex flex-col items-center grow p-4">
        <slot />
      </main>
    </UContainer>
    <DefaultFooter />
  </UContainer>
</template>

<style>
body {
  @apply bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300;
}
</style>
