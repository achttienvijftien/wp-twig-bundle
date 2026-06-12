<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test\Support;

use AchttienVijftien\Bundle\WpTwigBundle\EnvironmentConfiguratorInterface;
use Twig\Environment;

/**
 * Records every configure() call, so tests can assert the bootstrapper runs
 * wp_twig.configurator services exactly once per environment instance.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class RecordingConfigurator implements EnvironmentConfiguratorInterface {

	/**
	 * The environments configure() was called with, in order.
	 *
	 * @var Environment[]
	 */
	public array $configured = [];

	/**
	 * {@inheritDoc}
	 */
	public function configure( Environment $environment ): void {
		$this->configured[] = $environment;
	}
}
