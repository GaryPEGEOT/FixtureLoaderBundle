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

namespace MiaouCorp\Bundle\FixtureLoaderBundle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Nelmio\Alice\FileLoaderInterface;
use Nelmio\Alice\Throwable\LoadingThrowable;
use Symfony\Component\Finder\Finder;

/**
 * Class MiaouCorp\Bundle\FixtureLoaderBundle\FixtureLoader.
 *
 * @author Gary PEGEOT <garypegeot@gmail.com>
 */
class FixtureLoader
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FileLoaderInterface
     */
    private $loader;

    /**
     * @var string
     */
    private $dir;

    /**
     * FixtureLoader constructor.
     *
     * @param EntityManagerInterface $em
     * @param FileLoaderInterface    $loader
     * @param string                 $dir
     */
    public function __construct(EntityManagerInterface $em, FileLoaderInterface $loader, string $dir)
    {
        $this->em = $em;
        $this->loader = $loader;
        $this->dir = $dir;
    }

    /**
     * @param string $filename Filename to load
     * @param array  $keep     fixture(s) name to keep in memory for later use
     *
     * @throws \InvalidArgumentException
     *
     * @return object[] the generated fixtures matching `$keep` list
     */
    public function loadFile(string $filename, array $keep = []): array
    {
        $this->buildSchema();

        /** @var \Symfony\Component\Finder\SplFileInfo[] $files */
        $files = (new Finder())->in($this->dir)->name($filename);
        $fixtures = [];

        if (!\count($files)) {
            throw new \InvalidArgumentException("Cannot find any file matching \"$filename\" in \"{$this->dir}\" directory.");
        }

        foreach ($files as $file) {
            try {
                foreach ($this->loader->loadFile($file->getRealPath())->getObjects() as $id => $object) {
                    if (\in_array($id, $keep, true)) {
                        $fixtures[$id] = $object;
                    }

                    // Is object managed by Doctrine
                    if (!$this->em->getMetadataFactory()->isTransient(\get_class($object))) {
                        $this->em->persist($object);
                    }
                }
                $this->em->flush();
                $this->em->clear();
            } catch (LoadingThrowable | \LogicException $e) {
                throw new \InvalidArgumentException("Fixture file \"$file\" isn't loadable: {$e->getMessage()}", $e->getCode(), $e);
            }
        }

        return $fixtures;
    }

    /**
     * Erase and recreate database schema. (All data will be lost!).
     *
     * @throws \InvalidArgumentException
     */
    public function buildSchema(): void
    {
        $meta = $this->em->getMetadataFactory()->getAllMetadata();

        if (!empty($meta)) {
            $tool = new SchemaTool($this->em);
            $tool->dropSchema($meta);
            try {
                $tool->createSchema($meta);
            } catch (ToolsException $e) {
                throw new \InvalidArgumentException("Schema is not buildable: {$e->getMessage()}", $e->getCode(), $e);
            }
        }
    }
}
