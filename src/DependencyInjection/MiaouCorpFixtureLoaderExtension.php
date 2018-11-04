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

namespace MiaouCorp\Bundle\FixtureLoaderBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class MiaouCorpFixtureLoaderExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $def = $container->findDefinition('miaoucorp.fixture_loader');

        if (\method_exists($def, 'setArgument')) {
            $def->setArgument(2, $config['directory']);

            return;
        }

        $args = $def->getArguments();
        $args[2] = $config['directory'];
        $def->setArguments($args);
    }
}
