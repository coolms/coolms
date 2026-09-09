# CoolMS skeleton

The smallest tree that installs the published `coolms/*` packages, compiles a
container and answers `coolms:install`. It exists so that the installation path
can be stated from somewhere other than a machine that already has everything.

**Public, but not a release.** There is no tag, so `composer create-project`
does not work and there is deliberately no such line anywhere in this file. What
you can do is clone it and run the steps below -- which is what the rest of this
page is about.

Every one of those steps runs in CI on every change to this repository, and the
checks read response bodies rather than status codes. If this page is wrong, the
build goes red.

## What it does NOT give you

Read this first, because the gap is the point.

A CoolMS *installation* produces a site, a theme, users and groups, a VFS tree,
navigation, taxonomies, field definitions and workflow definitions -- fourteen
kinds of thing in all.

This skeleton reproduces **none of them**. Measured, not assumed: 0 of the 14,
because the code that produces them lives in the CoolMS application and is not
in any package yet. The full table, item by item, is in
[docs/measured-against-the-baseline.md](docs/measured-against-the-baseline.md).

What you get is a booting Symfony application with the platform *foundation*
installed: the entity/extras engine, the RQL query layer, the DTMPL template
engine, the config-override store, the transactional outbox and inbox, and the
secret store. Four database tables.

## The path that works

```bash
cp .env.dist .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php bin/console doctrine:schema:create
docker compose exec app php bin/console coolms:install
```

The first three succeed. `doctrine:schema:create` builds four tables.

!! **`coolms:install` exits 1, on purpose:**

```
[ERROR] Nothing to install: this application registers no structure
        installers and no module installers.
```

That is correct, and it is the shortest statement of what this skeleton is.
`coolms:install` runs what the installed modules contribute, and a distribution
shipping no CoolMS module contributes nothing. The same command once printed two
empty sections and `[OK] Installation complete.`, which is how an installation
that did nothing came to look like one that did everything. It refuses now.

It still provisions `COOLMS_SECRET_MASTER_KEY` into the env file your
environment reads, before it refuses. **Back that file up:** everything sealed
by the secret store is undecryptable without it.

## Four things that will bite you, all of them measured here

**1. `doctrine:migrations:migrate` says there are no registered migrations.**
Correct, not broken: no published package ships one. Use
`doctrine:schema:create`. The migrations in the CoolMS application tree build
that application's tables, not the platform's.

**2. Everything resolves from Packagist.** There are no `repositories` entries
and no path repositories. If you are reading this in a fork that has added
some, deleting them is the test that the platform is installable rather than
merely buildable on the machine it was written on.

**3. `config/services.yaml` must bind `string $projectDir`.**
`coolms/core-bundle` ships console commands taking a plain string argument that
autowiring cannot fill. Without the bind, the container fails on
`RemoveModuleCommand`. One more thing the packages leave to the consumer.

**4. `minimum-stability` is `dev`, with `prefer-stable`.** It has to be: most of
the platform is published as alphas. `composer.lock` is committed, so what you
install is the exact set this repository was last tested against -- read it there
rather than trusting a version written into prose, which is how the previous
version of this section went stale.

## Why the lock files are committed

Without a committed `symfony.lock`, `composer install` re-ran every Flex recipe
on every clone and rewrote five tracked files -- including appending a second
`DATABASE_URL` to `.env.dist` after the correct one, where the last line wins.
A repository you clone must be what it says it is, so both lock files are
committed and CI asserts that `composer install` leaves the working tree clean.

## Most of `config/services.yaml` is not this application

The `coolms/*` packages register almost none of their own services. They alias
interfaces to concrete classes and leave registering those classes to you, so
eight prototype scans pointing into `vendor/coolms/*/src/` -- with an exclusion
list on each, every entry load-bearing and none guessable -- have to be copied
into every consuming application. They are in `config/services.yaml` here, and you can count them yourself:
they are the `resource:` entries pointing into `vendor/coolms`.
**Do not trim them.**

## And `src/` is not empty, though it should be

Four stand-in classes under `src/Platform/`, covering ports that published
packages require and no published package supplies. Each names the module that
should replace it, and each is a file to delete rather than build on. Which of
them actually block a build changes as the published set changes -- see
[docs/what-the-skeleton-cannot-ship.md](docs/what-the-skeleton-cannot-ship.md).

## Licence

MIT.
