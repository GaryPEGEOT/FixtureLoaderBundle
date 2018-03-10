<?php
/**
 * This file is part of the FixtureLoaderBundle package.
 * (c) Gary PEGEOT <garypegeot@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MiaouCorp\Bundle\FixtureLoaderBundle\Tests\DependencyInjection;

use MiaouCorp\Bundle\FixtureLoaderBundle\DependencyInjection\MiaouCorpFixtureLoaderExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MiaouCorpFixtureLoaderExtensionTest extends TestCase
{
    public function testLoad()
    {
        $dir = dirname(__FILE__);
        $container = new ContainerBuilder();
        $ext = new MiaouCorpFixtureLoaderExtension();

        $ext->load([['directory' => $dir]], $container);

        $args = $container->findDefinition('miaoucorp.fixture_loader')->getArguments();

        $this->assertCount(3, $args);
        $this->assertEquals($dir, $args[2]);
    }
}
