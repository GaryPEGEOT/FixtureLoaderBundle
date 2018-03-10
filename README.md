Installation
============

This bundle use [Nelmio/Alice](https://github.com/nelmio/alice) for fixture generation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require --dev miaou-corp/fixture-loader-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require --dev miaou-corp/fixture-loader-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        //...
        
        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            //...
            $bundles[] = new Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle();
            $bundles[] = new MiaouCorp\Bundle\FixtureLoaderBundle\MiaouCorpFixtureLoaderBundle();
        }
    }

    // ...
}
```

### Step 3: Configure the bundle
```yaml
# config_test.yml

# Do not use production database !
# SQLite in memory is recommended for tests speed.
doctrine:
    dbal:
        url: 'sqlite:///:memory:'

miaoucorp_fixture_loader:
    # This is where you fixture files are stored.
    directory: /path/to/directory # Default to: '%kernel.project_dir%/tests/Resources/fixtures'
```

**Important note:** Each time you load a fixture file, the database schema is **drop and rebuilt**, so *do not use in production*...

# Usage

## Inside a `WebTestCase`:
```php
$client = static::createClient();

// You might want to do this if you do multiple request on a single test
// As kernel is rebooted on each request, so your database and fixtures will be lost.
$client->disableReboot();

// Or static::$kernel->getContainer for a KernelTestCase
$client->getContainer()->get('miaoucorp.fixture_loader')->loadFile('my-fixture-file.yaml');

$client->request('GET', '/some-path');

// To keep some fixture in memory for later use:
// Where "user_1" is the fixture key.
$fixtures = $client->getContainer()->get('miaoucorp.fixture_loader')
    ->loadFile('my-fixture-file.yaml', ['user_1']);
    
$userId = $fixtures['user_1']->getId();
```