<?php
/**
 * Compiler pass that collects twig.runtime services into a class-keyed locator.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle\Compiler;

use AchttienVijftien\Bundle\WpTwigBundle\TwigBootstrapper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Collects twig.runtime tagged services into a locator keyed by class name
 * and injects it into the TwigBootstrapper.
 *
 * Twig's RuntimeLoaderInterface::load() receives a class name, but the
 * twig.runtime tag carries no index attribute, so a plain !tagged_locator
 * would key by service id. TwigBundle's RuntimeLoaderPass (absent here) keys
 * by definition class for the same reason; this pass mirrors it.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class TwigRuntimeLocatorPass implements CompilerPassInterface {

	/**
	 * Builds the class-keyed runtime locator.
	 *
	 * @param ContainerBuilder $container The container builder.
	 *
	 * @return void
	 */
	public function process( ContainerBuilder $container ): void {
		if ( ! $container->hasDefinition( TwigBootstrapper::class ) ) {
			return;
		}

		$runtimes = [];

		foreach ( $container->findTaggedServiceIds( 'twig.runtime', true ) as $id => $attributes ) {
			$definition = $container->getDefinition( $id );

			// Resolve %parameter% class names, like TwigBundle's pass does.
			$class = $container->getParameterBag()->resolveValue( $definition->getClass() );

			$runtimes[ $class ] = new Reference( $id );
		}

		$container->getDefinition( TwigBootstrapper::class )
			->setArgument( '$runtimes', ServiceLocatorTagPass::register( $container, $runtimes ) );
	}
}
