<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test;

use AchttienVijftien\Bundle\WpTwigBundle\Test\Support\DummyExtension;
use AchttienVijftien\Bundle\WpTwigBundle\Test\Support\FakeAdapter;
use AchttienVijftien\Bundle\WpTwigBundle\Test\Support\RecordingConfigurator;
use AchttienVijftien\Bundle\WpTwigBundle\TwigBootstrapper;
use AchttienVijftien\Bundle\WpTwigBundle\TwigEnvironmentHolder;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use WP_UnitTestCase;

class TwigBootstrapperTest extends WP_UnitTestCase {

	private function container() {
		return apply_filters( 'achttienvijftien/container', null );
	}

	/**
	 * An empty PSR locator: this package itself ships no twig.runtime
	 * services, only the wiring that exposes whatever consumers tag.
	 */
	private function empty_runtimes(): ContainerInterface {
		return new class() implements ContainerInterface {
			public function get( string $id ) {
				throw new \LogicException( "No runtime '$id' registered." );
			}

			public function has( string $id ): bool {
				return false;
			}
		};
	}

	public function test_delivered_environment_reaches_the_holder(): void {
		$container = $this->container();
		$adapter   = new FakeAdapter();
		$container->get( TwigBootstrapper::class )->attach( $adapter );

		$env = new Environment( new FilesystemLoader() );
		$adapter->simulateHostEnv( $env );

		self::assertSame( $env, $container->get( TwigEnvironmentHolder::class )->get() );
	}

	public function test_the_same_environment_arriving_twice_is_harmless(): void {
		$container = $this->container();
		$adapter   = new FakeAdapter();
		$container->get( TwigBootstrapper::class )->attach( $adapter );

		$env = new Environment( new FilesystemLoader() );
		$adapter->simulateHostEnv( $env );

		// Rendering initializes the extension set; an unguarded second
		// configure() of an initialized environment is the failure mode the
		// per-environment idempotency guard (WeakMap) exists for. Keep the
		// render-between-deliveries shape to pin the no-fatal contract and
		// the holder re-set.
		$rendered = $env->createTemplate( 'ok' )->render();
		self::assertSame( 'ok', $rendered );

		$adapter->simulateHostEnv( $env );

		self::assertSame( $env, $container->get( TwigEnvironmentHolder::class )->get() );
	}

	public function test_extensions_and_configurators_run_exactly_once_per_environment(): void {
		$configurator = new RecordingConfigurator();
		$bootstrapper = new TwigBootstrapper(
			[ new DummyExtension() ],
			$this->empty_runtimes(),
			[ $configurator ],
			new TwigEnvironmentHolder()
		);

		$adapter = new FakeAdapter();
		$bootstrapper->attach( $adapter );

		$first = new Environment( new FilesystemLoader() );
		$adapter->simulateHostEnv( $first );
		$adapter->simulateHostEnv( $first );

		$second = new Environment( new FilesystemLoader() );
		$adapter->simulateHostEnv( $second );

		self::assertTrue( $first->hasExtension( DummyExtension::class ) );
		self::assertTrue( $second->hasExtension( DummyExtension::class ) );
		self::assertSame( [ $first, $second ], $configurator->configured );
	}

	public function test_bundle_boot_wired_the_real_timber_adapter(): void {
		// TwigBundle::boot() attached the TimberAdapter when the kernel
		// booted, so the timber/twig filter must feed any env passed through
		// it into the holder, without Timber being installed.
		$env = apply_filters( 'timber/twig', new Environment( new FilesystemLoader() ) );

		self::assertSame( $env, $this->container()->get( TwigEnvironmentHolder::class )->get() );
	}
}
