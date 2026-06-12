<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test\Support;

use AchttienVijftien\Bundle\WpTwigBundle\TwigAdapter;
use Twig\Environment;

/**
 * Test double for the Twig seam: records namespace registrations and lets a
 * test deliver a host environment on demand.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class FakeAdapter implements TwigAdapter {

	/**
	 * Recorded addNamespace() calls as [ namespace, path ] pairs.
	 *
	 * @var array<int, array{string, string}>
	 */
	public array $namespaces = [];

	/**
	 * Stored onEnvironment() callbacks.
	 *
	 * @var callable[]
	 */
	public array $callbacks = [];

	/**
	 * {@inheritDoc}
	 */
	public function onEnvironment( callable $configure ): void {
		$this->callbacks[] = $configure;
	}

	/**
	 * {@inheritDoc}
	 */
	public function addNamespace( string $namespace, string $path ): void {
		$this->namespaces[] = [ $namespace, $path ];
	}

	/**
	 * Pretends the host built its Twig environment: runs every stored
	 * callback with it, like TimberAdapter's filter would.
	 *
	 * @param Environment $environment The host environment.
	 *
	 * @return void
	 */
	public function simulateHostEnv( Environment $environment ): void {
		foreach ( $this->callbacks as $callback ) {
			$callback( $environment );
		}
	}
}
