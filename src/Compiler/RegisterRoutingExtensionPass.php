<?php
/**
 * Compiler pass that conditionally registers the Twig RoutingExtension.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle\Compiler;

use AchttienVijftien\Bundle\WpTwigBundle\Twig\RoutingExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Registers Twig\RoutingExtension only when routing is actually available:
 * this package does not depend on symfony/routing, so the extension lights
 * up when (a) the interface is installed and (b) some other bundle provides
 * a UrlGeneratorInterface service (e.g. wp-turbo-bundle's RouteRegistry),
 * and any future routing provider plugs in by exposing that same service id
 * without this package knowing about it.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class RegisterRoutingExtensionPass implements CompilerPassInterface {

	/**
	 * Registers the RoutingExtension when a URL generator is available.
	 *
	 * @param ContainerBuilder $container The container builder.
	 *
	 * @return void
	 */
	public function process( ContainerBuilder $container ): void {
		if ( ! interface_exists( UrlGeneratorInterface::class ) || ! $container->has( UrlGeneratorInterface::class ) ) {
			return;
		}

		$container->register( RoutingExtension::class, RoutingExtension::class )
			->setArguments( [ new Reference( UrlGeneratorInterface::class ) ] )
			->addTag( 'twig.extension' )
			->setPublic( true );
	}
}
