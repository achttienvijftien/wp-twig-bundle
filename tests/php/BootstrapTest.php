<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test;

use AchttienVijftien\Bundle\WpTwigBundle\Test\Support\DummyProjectBundle;
use AchttienVijftien\Bundle\WpTwigBundle\TwigBundle;
use WP_UnitTestCase;

class BootstrapTest extends WP_UnitTestCase {

	public function test_container_booted_with_our_bundle(): void {
		$container = apply_filters( 'achttienvijftien/container', null );

		self::assertNotNull( $container, 'ServiceContainer should have booted on muplugins_loaded.' );

		$bundles = $container->getParameter( 'kernel.bundles' );

		// The 'TwigBundle' key must be OUR class: Bundle::getName() uses the
		// short class name, which is exactly why the class is named TwigBundle
		// (consumers like ux-twig-component check for this key).
		self::assertSame( TwigBundle::class, $bundles['TwigBundle'] ?? null );

		// Proves the regression simulation is active: a project-like bundle
		// registered ahead of this package (see tests/bootstrap.php), the
		// ordering that wiped a parameter-shim approach in production.
		self::assertContains( DummyProjectBundle::class, $bundles );
	}

	public function test_template_namespace_derivation_matches_symfony_twig_bundle(): void {
		// The boot()-time auto-namespace scan (kernel.bundles_metadata =>
		// @<name minus Bundle suffix>) relies on this derivation; the bundles
		// registered in THIS suite ship no templates dirs, so the end-to-end
		// proof lives in wp-turbo-bundle's RenderSmokeTest (@Turbo).
		self::assertSame( 'Turbo', TwigBundle::namespaceForBundle( 'TurboBundle' ) );
		self::assertSame( 'Twig', TwigBundle::namespaceForBundle( 'TwigBundle' ) );
		self::assertSame( 'Acme', TwigBundle::namespaceForBundle( 'Acme' ) );
	}
}
