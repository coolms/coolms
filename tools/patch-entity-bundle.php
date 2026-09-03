<?php

declare(strict_types=1);

/*
 * Patch coolms/entity-bundle v2.0.0-alpha1 in place, idempotently.
 *
 * VirtualFieldServicesPass calls class_exists() on the class of EVERY
 * definition in the container. A container holds classes from every
 * installed bundle, and a bundle may ship a class that extends an optional
 * dependency the application never installed. Force-loading one is a fatal
 * at compile time. Two such classes are in this skeleton's own vendor tree:
 *
 *   doctrine/doctrine-bundle  src/Twig/DoctrineExtension.php
 *     extends Twig\\Extension\\AbstractExtension; doctrine-bundle declares twig nowhere.
 *
 *   symfony/translation  Extractor/Visitor/*.php
 *     implement PhpParser\\NodeVisitor; nikic/php-parser is optional and absent.
 *
 * Adding the missing libraries is whack-a-mole -- installing twig only
 * revealed the second one. The fix belongs in the package: a class that will
 * not load is not ours, and is never an error.
 *
 * DELETE THIS FILE, and its composer script hook, once a release of
 * coolms/entity-bundle carries the fix.
 */

$file = __DIR__ . '/../vendor/coolms/entity-bundle/src/DependencyInjection/Compiler/VirtualFieldServicesPass.php';

if (!is_file($file)) {
    fwrite(STDERR, 'patch-entity-bundle: nothing to patch at ' . $file . PHP_EOL);

    exit(0);
}

$source = file_get_contents($file);

if (str_contains($source, 'catch (\\\\Throwable)')) {
    echo 'patch-entity-bundle: already patched' . PHP_EOL;

    exit(0);
}

$before = <<<'PHP'
            $class = $definition->getClass();
            if (null === $class || !class_exists($class)) {
                continue;
            }
PHP;

$after = <<<'PHP'
            $class = $definition->getClass();
            if (null === $class) {
                continue;
            }
            try {
                if (!class_exists($class)) {
                    continue;
                }
            } catch (\Throwable) {
                continue;
            }
PHP;

if (!str_contains($source, $before)) {
    fwrite(STDERR, 'patch-entity-bundle: the expected code is not there -- the package has changed, review before trusting this.' . PHP_EOL);

    exit(1);
}

file_put_contents($file, str_replace($before, $after, $source));
echo 'patch-entity-bundle: applied' . PHP_EOL;
