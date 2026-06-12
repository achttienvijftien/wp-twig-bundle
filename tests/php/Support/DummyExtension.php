<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test\Support;

use Twig\Extension\AbstractExtension;

/**
 * Stand-in for a twig.extension tagged service: this package itself ships no
 * Twig extensions, so unit tests inject one manually to prove the
 * bootstrapper attaches whatever the container would have collected.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class DummyExtension extends AbstractExtension {
}
