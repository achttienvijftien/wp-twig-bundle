# wp-twig-bundle

Bridges a host-owned Twig environment (Timber, ...) into the
[1815 service container](https://packagist.org/packages/achttienvijftien/service-container)
as the `twig` service.

## What it does

- **The `twig` service**: the container exposes the host's Twig environment
  (captured via a `TwigEnvironmentHolder`) as `twig` / `Twig\Environment`, so
  container services render with the SAME instance the host renders with.
- **A LOAD-BEARING bundle name**: the bundle class is named `TwigBundle` on
  purpose. `Bundle::getName()` returns the short class name, and packages such
  as ux-twig-component refuse to load unless `kernel.bundles` contains a
  `'TwigBundle'` key. Registering this bundle satisfies that check honestly:
  this IS the bundle that provides the `twig` service in this stack. Symfony's
  own TwigBundle cannot be registered alongside it (duplicate name), which is
  correct: the two architectures are mutually exclusive.
- **The adapter seam**: `TwigAdapter` abstracts who owns the environment. A
  Timber adapter is included; for Tile or plain Twig, implement `TwigAdapter`
  and alias it in your container.
- **The configurator tag**: other bundles contribute environment setup
  (lexers, escaper safe-classes, ...) by implementing
  `EnvironmentConfiguratorInterface`, autoconfigured with the
  `wp_twig.configurator` tag. The bundle also consumes the standard
  `twig.extension` and `twig.runtime` tags, like Symfony's TwigBundle would.
- **Auto `@BundleName` template namespaces**: on boot, every registered
  bundle's `<path>/templates` directory is registered on the host loader as
  `@<BundleName minus the Bundle suffix>` (e.g. ux-turbo's templates become
  `@Turbo`), mirroring symfony/twig-bundle's convention so templates and
  component resolution written against the native bundle work unchanged.

## Usage

The package self-registers its bundle through composer `autoload.files`
(filter `achttienvijftien/container_bundles`). Once the container boots:

```php
$twig = apply_filters( 'achttienvijftien/container', null )->get( 'twig' );
// Same environment Timber renders with, after Timber built it.
```

Contribute environment setup from another bundle:

```php
class MyConfigurator implements \AchttienVijftien\Bundle\WpTwigBundle\EnvironmentConfiguratorInterface {
	public function configure( \Twig\Environment $environment ): void {
		// runs once per host environment instance
	}
}
```

Timber adapter included; Tile/other hosts: implement `TwigAdapter`.

## Compatibility

This package is **service-id + tag level compatible** with code written
against symfony/twig-bundle:

- the `twig` service / `Twig\Environment` alias and the lazy `twig.loader`
  service exist with the expected semantics;
- the `twig.extension` and `twig.runtime` tags are consumed;
- `@Bundle` template namespaces auto-register per the same convention.

It is **NOT FQCN-level compatible**: `class_exists` gates on
`Symfony\Bundle\TwigBundle\...` classes cannot be satisfied, because that
package is deliberately not installed.

## Future: native symfony/twig-bundle

The door stays open, but environment OWNERSHIP is the blocker: Timber owns
its Twig environment (and its autoescape defaults differ from the native
bundle's), so the container cannot simply build a second, authoritative
environment without splitting rendering in two. Adoption is viable per-host
through the `TwigAdapter` seam: a container-first host could invert the
bridge and hand the container's environment to the host instead, at which
point the native bundle could own the `twig` service.
