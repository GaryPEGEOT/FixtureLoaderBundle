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

namespace MiaouCorp\Bundle\FixtureLoaderBundle\Tests;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\EntityManagerInterface;
use MiaouCorp\Bundle\FixtureLoaderBundle\FixtureLoader;
use Nelmio\Alice\FileLoaderInterface;
use Nelmio\Alice\Loader\NativeLoader;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class FixtureLoaderTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var FileLoaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileLoader;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var FixtureLoader
     */
    private $loader;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('getMetadataFactory')->willReturn($this->createMock(ClassMetadataFactory::class));

        $this->fileLoader = new NativeLoader();
        $this->dir = realpath(\dirname(__DIR__).'/src/Resources/fixtures');

        $this->loader = new FixtureLoader(
            $this->em,
            $this->fileLoader,
            $this->dir
        );
    }

    public function testLoadFile(): void
    {
        $this->em->expects($this->exactly(2))->method('persist');
        $this->em->expects($this->once())->method('flush');

        $fixtures = $this->loader->loadFile('dummy.yaml', ['fixture_1']);

        $this->assertCount(1, $fixtures);
        $this->assertArrayHasKey('fixture_1', $fixtures);
        $this->assertInstanceOf('stdClass', $fixtures['fixture_1']);
        $this->assertSame('bar', $fixtures['fixture_1']->foo);
    }

    public function testBadFilename(): void
    {
        $filename = '404_not-found.yaml';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot find any file matching \"$filename\" in \"{$this->dir}\" directory.");

        $this->loader->loadFile($filename);
    }

    public function testBadFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown formatter "lol"');

        $this->loader->loadFile('crappy.yaml');
    }
}
