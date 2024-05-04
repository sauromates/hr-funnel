<template>
  <footer class="w-full px-4 sm:px-8 lg:px-12 py-4">
    <slot>
      <UButton
        variant="link"
        color="gray"
        size="xs"
        icon="i-mdi-github"
        to="https://github.com/sauromates/hr-funnel"
        target="_blank"
        :padded="false"
      >
        {{ `Published under MIT License &copy; ${new Date().getFullYear()}` }}
      </UButton>
    </slot>
  </footer>
</template>
