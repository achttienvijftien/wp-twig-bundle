<?php

namespace AchttienVijftien\Bundle\WpTwigBundle\Test\Support;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Simulates a real project's own bundle (Stud, theme, plugin bundles).
 *
 * Registered BEFORE the package's bundle in tests/bootstrap.php so that its
 * extension loads first. MergeExtensionConfigurationPass restores its
 * pre-prepend parameter snapshot after EVERY extension load, so any
 * kernel.bundles shim that is not part of that snapshot is wiped here,
 * before a consumer (like ux-twig-component) runs its TwigBundle check.
 * Registering a REAL bundle named TwigBundle (this package's approach) is
 * immune to that ordering; this dummy keeps the suite honest about it.
 *
 * @package AchttienVijftien\Bundle\WpTwigBundle
 */
class DummyProjectBundle extends AbstractBundle {
}
