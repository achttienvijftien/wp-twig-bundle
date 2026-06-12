<?php
/**
 * Wires the container's Twig machinery into the host-owned Twig environment.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle;

use AchttienVijftien\Bundle\WpTwigBundle\RuntimeLoader\ContainerRuntimeLoader;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;

/**
 * Wires the container's Twig machinery into the host-owned Twig environment
 * (Timber's, Tile's, ...) through the TwigAdapter seam.
 *
 * Without Symfony's TwigBundle nobody consumes the twig.extension and
 * twig.runtime tags, so this class does it the moment the host hands over
 * its environment. Bundle-specific environment setup (e.g. the UX component
 * lexer) is contributed through tagged EnvironmentConfiguratorInterface
 * services, keeping this class host- and consumer-agnostic.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class TwigBootstrapper {

	/**
	 * Environments already configured, for idempotency: WP tests and Timber
	 * may push the same environment through the seam more than once.
	 *
	 * @var \WeakMap<Environment, true>
	 */
	private \WeakMap $configured;

	/**
	 * TwigBootstrapper constructor.
	 *
	 * @param iterable<ExtensionInterface>               $extensions    The twig.extension tagged services.
	 * @param ContainerInterface                         $runtimes      The twig.runtime tagged services, keyed by
	 *                                                                  class name (see Compiler\TwigRuntimeLocatorPass).
	 * @param iterable<EnvironmentConfiguratorInterface> $configurators The wp_twig.configurator tagged services.
	 * @param TwigEnvironmentHolder                      $holder        Captures the host environment for the
	 *                                                                  container's `twig` service.
	 */
	public function __construct(
		private readonly iterable $extensions,
		private readonly ContainerInterface $runtimes,
		private readonly iterable $configurators,
		private readonly TwigEnvironmentHolder $holder,
	) {
		$this->configured = new \WeakMap();
	}

	/**
	 * Hooks the Twig machinery into the host's Twig setup.
	 *
	 * @param TwigAdapter $adapter The host's Twig seam.
	 *
	 * @return void
	 */
	public function attach( TwigAdapter $adapter ): void {
		$adapter->onEnvironment( $this->configure( ... ) );
	}

	/**
	 * Configures a host environment, once per environment instance.
	 *
	 * @param Environment $environment The host's Twig environment.
	 *
	 * @return void
	 */
	private function configure( Environment $environment ): void {
		if ( ! isset( $this->configured[ $environment ] ) ) {
			$this->configured[ $environment ] = true;

			foreach ( $this->extensions as $extension ) {
				if ( ! $environment->hasExtension( $extension::class ) ) {
					$environment->addExtension( $extension );
				}
			}

			$environment->addRuntimeLoader( new ContainerRuntimeLoader( $this->runtimes ) );

			foreach ( $this->configurators as $configurator ) {
				$configurator->configure( $environment );
			}
		}

		// Always capture (cheap): the container's `twig` service must follow
		// the environment the host currently renders with.
		$this->holder->set( $environment );
	}
}
