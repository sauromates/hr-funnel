# API

## Testing

API is tested using PHPUnit 9.5 with the following Symfony-specific tools:

- [Doctrine Test Bundle](https://github.com/dmaicher/doctrine-test-bundle)
- [Zenstruck Foundry](https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html)

**Do NOT use Zenstruck's `ResetDatabase` trait in tests since the DB state is controlled by Doctrine Test Bundle.**
