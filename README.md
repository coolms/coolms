# CoolMS skeleton

A minimal CoolMS application: the smallest tree that installs the published
`coolms/*` packages, compiles a container and answers `coolms:install`.

**This is a repository, not a release.** It is not tagged, not published,
and there is deliberately no `composer create-project` line anywhere in it.
It exists so that the installation path can be stated from somewhere other
than a machine that already has everything.

## What it does NOT give you

Read this first, because the gap is the point.

A CoolMS *installation* produces a site, a theme, users and groups, a VFS
tree, navigation, taxonomies, field definitions and workflow definitions --
all of it written down in the application tree as `docs/baseline-installation.md`.

This skeleton reproduces **none of it**. Measured, not assumed: 0 of the 14
items in that document, because the code that produces them lives in the
CoolMS application under `src/` and is not in any package yet. See
[docs/measured-against-the-baseline.md](docs/measured-against-the-baseline.md).

What you get is a booting Symfony application with the platform *foundation*
installed: the entity/extras engine, the RQL query layer, the DTMPL template
engine, the config-override store, the transactional outbox and inbox, and
the secret store. Four database tables.

## The path that works

```bash
cp .env.dist .env          # BEFORE composer install, not after -- see below
docker compose up -d
docker compose exec app composer install
docker compose exec app php bin/console doctrine:schema:create
docker compose exec app php bin/console coolms:install
```

`coolms:install` writes a `COOLMS_SECRET_MASTER_KEY` into `.env.local`.
Back it up: everything sealed by the secret store is undecryptable without it.

## Four things that will bite you, all of them measured here

**1. A Flex recipe appends a second `DATABASE_URL` to `.env`.** Installing
`doctrine/doctrine-bundle` writes `DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1..."`
to the end of `.env`, and the LAST line wins. If you created `.env` before
running `composer install`, check it afterwards and delete the duplicate.
The symptom is `connection to server at "127.0.0.1" failed` while a database
is plainly running.

**2. `doctrine:migrations:migrate` fails with "there are no registered
migrations".** That is correct, not broken: no published package ships a
migration. Use `doctrine:schema:create`. The eight migrations in the CoolMS
application tree build the application's tables, not the platform's.

**3. The two theme packages are not on Packagist.** `coolms/theme-default`
and `coolms/theme-bootstrap` are public GitHub repositories with no tags,
so `composer.json` carries a `vcs` repository entry for each. They are marked `"no-api": true` on purpose: without it
composer asks the GitHub API, gets rate-limited without a token, and falls
back to `git@github.com:` -- which fails with "cannot run ssh" on a machine
that has no key. Delete both entries the day the themes are tagged.

**4. `composer install` patches a package, on purpose.**
`tools/patch-entity-bundle.php` runs as a post-install step and edits one
file in `vendor/coolms/entity-bundle`. Without it the container does not
compile at all: `VirtualFieldServicesPass` force-loads every service class
in the container, and two classes in this very vendor tree extend an
optional dependency that is not installed --
`doctrine/doctrine-bundle`'s Twig extension, and `symfony/translation`'s
AST extractor, which wants `nikic/php-parser`. Installing the missing
libraries is whack-a-mole; installing twig only revealed the second one.
The patch is idempotent, refuses to run if the code has changed, and
should be deleted the day a release of `coolms/entity-bundle` carries the
fix. See [docs/only-works-here.md](docs/only-works-here.md) §4.

**5. `minimum-stability` is `dev`.** It has to be: the themes resolve through
`branch-alias dev-develop => 1.0.x-dev`, and `coolms/core` and the other
seven platform packages are published only as `v2.0.0-alpha1`.

## Most of `config/services.yaml` is not this application

The `coolms/*` packages register almost none of their own services. They
alias interfaces to concrete classes and leave registering those classes to
you, so nine prototype scans pointing into `vendor/coolms/*/src/` -- with
fifteen exclusions between them, each one load-bearing and none guessable --
have to be copied into every consuming application. They are in
`config/services.yaml` here. Do not trim them.

## And `src/` is not empty, though it should be

Three classes, implementing four ports that published packages REQUIRE as
constructor arguments and that no published package supplies. Each one names
the CoolMS module that should replace it. They are the extraction backlog in
executable form, and every one is a file to delete rather than to build on.

## Licence

MIT.
