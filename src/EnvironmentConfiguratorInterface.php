<?php
/**
 * Contract for tagged services that contribute host Twig environment setup.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle;

use Twig\Environment;

/**
 * Contributes environment setup when the host hands over its Twig
 * environment. Implementations are autoconfigured with the
 * `wp_twig.configurator` tag and run once per environment instance by
 * the TwigBootstrapper, after extensions and runtime loaders are attached.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
interface EnvironmentConfiguratorInterface {

	/**
	 * Configures the host environment.
	 *
	 * @param Environment $environment The host's Twig environment.
	 *
	 * @return void
	 */
	public function configure( Environment $environment ): void;
}
