<?php

declare(strict_types=1);

/*
 * This file is part of the MiaouCorpFixtureLoaderBundle project.
 *
 * (c) Gary PEGEOT <garypegeot@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MiaouCorp\Bundle\FixtureLoaderBundle\Tests\DependencyInjection;

use MiaouCorp\Bundle\FixtureLoaderBundle\DependencyInjection\MiaouCorpFixtureLoaderExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
class MiaouCorpFixtureLoaderExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $dir = __DIR__;
        $container = new ContainerBuilder();
        $ext = new MiaouCorpFixtureLoaderExtension();

        $ext->load([['directory' => $dir]], $container);

        $args = $container->findDefinition('miaoucorp.fixture_loader')->getArguments();

        $this->assertCount(3, $args);
        $this->assertSame($dir, $args[2]);
    }
}
