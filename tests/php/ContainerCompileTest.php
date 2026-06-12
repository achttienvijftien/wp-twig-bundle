<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test;

use AchttienVijftien\Bundle\WpTwigBundle\TwigEnvironmentHolder;
use WP_UnitTestCase;

class ContainerCompileTest extends WP_UnitTestCase {

	public function test_container_compiles_with_the_twig_bridge_services(): void {
		$container = apply_filters( 'achttienvijftien/container', null );

		self::assertNotNull( $container );
		self::assertTrue( $container->has( 'twig' ) );
		self::assertTrue( $container->has( TwigEnvironmentHolder::class ) );
	}
}
