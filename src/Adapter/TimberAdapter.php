<?php
/**
 * Timber implementation of the Twig seam.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle\Adapter;

use AchttienVijftien\Bundle\WpTwigBundle\TwigAdapter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Timber implementation of the Twig seam.
 *
 * Timber builds its Twig environment lazily and exposes it through the
 * `timber/twig` filter; its loader passes through `timber/loader/loader`.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class TimberAdapter implements TwigAdapter {

	/**
	 * {@inheritDoc}
	 */
	public function onEnvironment( callable $configure ): void {
		add_filter(
			'timber/twig',
			static function ( Environment $twig ) use ( $configure ): Environment {
				$configure( $twig );

				return $twig;
			}
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function addNamespace( string $namespace, string $path ): void {
		add_filter(
			'timber/loader/loader',
			static function ( FilesystemLoader $loader ) use ( $namespace, $path ): FilesystemLoader {
				$loader->addPath( $path, $namespace );

				return $loader;
			}
		);
	}
}
