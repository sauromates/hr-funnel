/**
 * Configuration file is created as CommonJS since VSCode Prettier plugin
 * has some conflicts with ESM.
 *
 * @type {import("prettier").Config}
 */
const config = {
  trailingComma: 'es5',
  tabWidth: 2,
  semi: true,
  singleQuote: true,
  printWidth: 120,
};

module.exports = config;
