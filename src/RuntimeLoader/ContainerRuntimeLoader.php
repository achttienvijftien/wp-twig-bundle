<?php
/**
 * Twig runtime loader backed by the container's twig.runtime locator.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle\RuntimeLoader;

use Psr\Container\ContainerInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Exposes the container's twig.runtime services (keyed by class name in
 * Compiler\TwigRuntimeLocatorPass) to the host Twig environment.
 *
 * Same idea as twig-bridge's ContainerRuntimeLoader, which TwigBundle would
 * normally wire; this package runs without Symfony's TwigBundle.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class ContainerRuntimeLoader implements RuntimeLoaderInterface {

	/**
	 * ContainerRuntimeLoader constructor.
	 *
	 * @param ContainerInterface $container Service locator keyed by runtime class name.
	 */
	public function __construct( private readonly ContainerInterface $container ) {
	}

	/**
	 * Loads the runtime implementation for a Twig runtime class.
	 *
	 * @param string $class The runtime class name.
	 *
	 * @return object|null
	 */
	public function load( string $class ) {
		return $this->container->has( $class ) ? $this->container->get( $class ) : null;
	}
}
