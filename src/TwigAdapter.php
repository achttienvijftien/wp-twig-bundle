<?php
/**
 * Seam between the bundle and the package that owns the Twig environment.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle;

/**
 * Seam between the bundle and whichever package owns the Twig environment
 * (Timber now; Tile or plain Twig are drop-in implementations later).
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
interface TwigAdapter {

	/**
	 * Runs $configure with the host's Twig\Environment once it is built.
	 * May be called multiple times; every callback must run.
	 *
	 * @param callable(\Twig\Environment): void $configure Configuration callback.
	 *
	 * @return void
	 */
	public function onEnvironment( callable $configure ): void;

	/**
	 * Registers a template namespace on the host's loader.
	 *
	 * @param string $namespace Twig namespace (without the @).
	 * @param string $path      Filesystem path holding the templates.
	 *
	 * @return void
	 */
	public function addNamespace( string $namespace, string $path ): void;
}
