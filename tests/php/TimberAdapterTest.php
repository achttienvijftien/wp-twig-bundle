<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test;

use AchttienVijftien\Bundle\WpTwigBundle\Adapter\TimberAdapter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use WP_UnitTestCase;

class TimberAdapterTest extends WP_UnitTestCase {

	public function test_on_environment_configures_the_env_passed_through_the_timber_filter(): void {
		$adapter  = new TimberAdapter();
		$received = null;

		$adapter->onEnvironment(
			function ( Environment $twig ) use ( &$received ) {
				$received = $twig;
			}
		);

		$env    = new Environment( new FilesystemLoader() );
		$result = apply_filters( 'timber/twig', $env );

		self::assertSame( $env, $received );
		self::assertSame( $env, $result, 'The filter must return the environment for the next callback.' );
	}

	public function test_add_namespace_registers_a_path_on_the_timber_loader(): void {
		$adapter = new TimberAdapter();
		$adapter->addNamespace( 'AdapterTest', __DIR__ );

		$loader = new FilesystemLoader();
		$loader = apply_filters( 'timber/loader/loader', $loader );

		self::assertSame( [ __DIR__ ], $loader->getPaths( 'AdapterTest' ) );
	}

	public function test_adapter_is_the_container_twig_adapter(): void {
		$container = apply_filters( 'achttienvijftien/container', null );

		self::assertInstanceOf(
			TimberAdapter::class,
			$container->get( \AchttienVijftien\Bundle\WpTwigBundle\TwigAdapter::class )
		);
	}
}
