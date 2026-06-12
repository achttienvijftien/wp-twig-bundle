<?php
/**
 * Bridges a host-owned Twig environment into the container as the twig service.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle;

use AchttienVijftien\Bundle\WpTwigBundle\Compiler\RegisterRoutingExtensionPass;
use AchttienVijftien\Bundle\WpTwigBundle\Compiler\TwigRuntimeLocatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Twig for WordPress: bridges a host-owned Twig environment (Timber now,
 * Tile or plain Twig later via TwigAdapter implementations) into the
 * Symfony container as the `twig` service, and consumes the
 * twig.extension/twig.runtime tags that TwigBundle normally would.
 *
 * The class name is LOAD-BEARING: Bundle::getName() returns the short class
 * name, and ux-twig-component's TwigComponentExtension refuses to load
 * unless kernel.bundles contains a 'TwigBundle' key. Registering this bundle
 * satisfies that check honestly: this IS the bundle that provides the twig
 * service in this stack. Consequence: registering Symfony's own TwigBundle
 * alongside it makes the kernel throw on the duplicate name, which is
 * correct, since the two architectures are mutually exclusive.
 *
 * Deliberately host- and consumer-agnostic: bundle-specific environment
 * setup (e.g. ux-twig-component's lexer, escaper safe-classes, template
 * namespaces) is contributed by other bundles through the
 * `wp_twig.configurator` tag.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class TwigBundle extends AbstractBundle {

	/**
	 * Builds the bundle.
	 *
	 * @param ContainerBuilder $container The container builder.
	 *
	 * @return void
	 */
	public function build( ContainerBuilder $container ): void {
		parent::build( $container );

		$container->registerForAutoconfiguration( EnvironmentConfiguratorInterface::class )
			->addTag( 'wp_twig.configurator' );

		$container->addCompilerPass( new TwigRuntimeLocatorPass() );
		$container->addCompilerPass( new RegisterRoutingExtensionPass() );
	}

	/**
	 * Boots the bundle: hooks the Twig machinery into the host's Twig setup
	 * (runs on muplugins_loaded when the kernel boots the bundles).
	 *
	 * @return void
	 */
	public function boot(): void {
		$adapter = $this->container->get( TwigAdapter::class );

		$this->container->get( TwigBootstrapper::class )->attach( $adapter );

		// Deliberate parity with symfony/twig-bundle's convention: every
		// registered bundle's <path>/templates directory is exposed on the
		// host loader as @<BundleName minus the Bundle suffix> (e.g.
		// ux-turbo's templates become @Turbo), so templates and component
		// resolution written against the native TwigBundle work unchanged.
		foreach ( $this->container->getParameter( 'kernel.bundles_metadata' ) as $name => $metadata ) {
			$templates = $metadata['path'] . '/templates';

			if ( is_dir( $templates ) ) {
				$adapter->addNamespace( self::namespaceForBundle( $name ), $templates );
			}
		}
	}

	/**
	 * Derives the Twig template namespace for a bundle name, the way
	 * symfony/twig-bundle's extension does: FooBundle => Foo.
	 *
	 * @param string $bundle_name The bundle name (Bundle::getName()).
	 *
	 * @return string
	 */
	public static function namespaceForBundle( string $bundle_name ): string {
		return preg_replace( '/Bundle$/', '', $bundle_name );
	}

	/**
	 * Loads the bundle's service definitions.
	 *
	 * @param array                 $config    The bundle configuration.
	 * @param ContainerConfigurator $container The container configurator.
	 * @param ContainerBuilder      $builder   The container builder.
	 *
	 * @return void
	 */
	public function loadExtension( array $config, ContainerConfigurator $container, ContainerBuilder $builder ): void {
		$container->import( '../config/services.yaml' );
	}
}
