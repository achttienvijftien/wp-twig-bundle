<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test;

use AchttienVijftien\Bundle\WpTwigBundle\Twig\RoutingExtension;
use WP_UnitTestCase;

/**
 * Covers RegisterRoutingExtensionPass's absent branch (symfony/routing is
 * not in this package's dependency tree, so interface_exists() is false at
 * compile time and the RoutingExtension must stay out of the container);
 * the present branch is covered by wp-turbo-bundle's integration test of a
 * bundle providing UrlGeneratorInterface.
 */
class RegisterRoutingExtensionPassTest extends WP_UnitTestCase {

	public function test_routing_extension_is_absent_without_a_url_generator(): void {
		$container = apply_filters( 'achttienvijftien/container', null );

		self::assertNotNull( $container );
		self::assertFalse( interface_exists( \Symfony\Component\Routing\Generator\UrlGeneratorInterface::class ) );
		self::assertFalse( $container->has( RoutingExtension::class ) );
	}
}
