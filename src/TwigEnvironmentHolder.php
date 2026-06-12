<?php
/**
 * Holds the host-owned Twig environment for the container's twig service.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */

namespace AchttienVijftien\Bundle\WpTwigBundle;

use Twig\Environment;

/**
 * Holds the host-owned Twig environment (Timber's, Tile's, ...).
 *
 * The container's `twig` service delegates here, so bundle services that
 * inject Twig\Environment receive the SAME instance the host renders with.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class TwigEnvironmentHolder {

	/**
	 * The captured host environment, if any.
	 *
	 * @var Environment|null
	 */
	private ?Environment $environment = null;

	/**
	 * Captures the host environment.
	 *
	 * @param Environment $environment The host's Twig environment.
	 *
	 * @return void
	 */
	public function set( Environment $environment ): void {
		$this->environment = $environment;
	}

	/**
	 * Returns the captured host environment.
	 *
	 * @return Environment
	 *
	 * @throws \LogicException When no environment has been captured yet.
	 */
	public function get(): Environment {
		if ( null === $this->environment ) {
			throw new \LogicException(
				'No Twig environment captured yet. The twig service may only be used after the host ' .
				'(Timber/Tile) built its environment.'
			);
		}

		return $this->environment;
	}

	/**
	 * Whether an environment has been captured.
	 *
	 * @return bool
	 */
	public function has(): bool {
		return null !== $this->environment;
	}
}
