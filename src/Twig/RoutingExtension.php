<?php
/**
 * Twig path()/url() functions backed by a UrlGeneratorInterface.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Same contract as symfony/twig-bridge's RoutingExtension (not installed in
 * this stack): path() and url() delegate to whatever UrlGeneratorInterface
 * service the container provides, registered conditionally by
 * Compiler\RegisterRoutingExtensionPass (see that pass for why this package
 * never requires symfony/routing itself).
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class RoutingExtension extends AbstractExtension {

	/**
	 * RoutingExtension constructor.
	 *
	 * @param UrlGeneratorInterface $generator The container-provided URL generator.
	 */
	public function __construct( private readonly UrlGeneratorInterface $generator ) {
	}

	/**
	 * Registers the path() and url() functions.
	 *
	 * @return TwigFunction[]
	 */
	public function getFunctions(): array {
		// Unlike twig-bridge these are not marked is_safe; the host environment's escaping policy stays in charge.
		return [
			new TwigFunction(
				'path',
				fn( string $name, array $params = [] ): string => $this->generator->generate( $name, $params, UrlGeneratorInterface::ABSOLUTE_PATH )
			),
			new TwigFunction(
				'url',
				fn( string $name, array $params = [] ): string => $this->generator->generate( $name, $params, UrlGeneratorInterface::ABSOLUTE_URL )
			),
		];
	}
}
