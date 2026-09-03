# Every step that only worked because of something already on the machine

The list this skeleton exists to produce. Each entry is something that never
failed in the CoolMS tree and failed here, in order of discovery.

## 1. `path` repositories with `symlink: true`

The application's `composer.json` carries **17** `path` repository entries
pointing at `./packages/*`, several pinning a version that does not exist
anywhere else (`"versions": {"coolms/core": "2.0.0"}`). Every constraint in
that file resolves against the working copy. Nothing about it is a test of
whether the packages install.

## 2. Three required packages are not on Packagist at all

`coolms/theme-admin`, `coolms/theme-bootstrap` and `coolms/theme-default`
**404**. `coolms/ooxml` is registered with an **empty version list**. See
`four-tags.md`.

## 3. Without a GitHub token, a `vcs` repository falls back to ssh

Composer asks the GitHub API, is rate-limited unauthenticated, and retries
as `git@github.com:` -- `error: cannot run ssh: No such file or directory`.
Fixed here with `"no-api": true`, which clones the https URL directly. The
first install of the day succeeds and the second does not, which is the
worst possible failure shape.

## 4. `coolms/entity-bundle` force-loads every class in the container

`VirtualFieldServicesPass` calls `class_exists()` on the class of **every**
definition. A container holds classes from every installed bundle, and a
bundle may ship a class extending an optional dependency the application
never installed. Force-loading one is a fatal at compile time.

**Two victims in this skeleton's own vendor tree, and finding the second is
what settles the diagnosis:**

1. `doctrine/doctrine-bundle` ships `src/Twig/DoctrineExtension.php`
   extending `Twig\Extension\AbstractExtension`, and declares twig
   **nowhere** -- not in `require`, `require-dev` or `suggest`.
   → `Uncaught Error: Class "Twig\Extension\AbstractExtension" not found`,
   in an application that has never mentioned Twig.
2. Installing `twig/twig` fixed that and immediately produced
   `Uncaught Error: Interface "PhpParser\NodeVisitor" not found` --
   `symfony/translation`'s `Extractor/Visitor/*` classes, wanting the
   optional `nikic/php-parser`.

⚠️ And `symfony/translation` is in this tree only because §5 forced it in.
Two undeclared-dependency defects, one feeding the other.

**So adding the missing libraries is whack-a-mole, and the fix belongs in the
package**: a class that will not load is one the bundle does not own, which
makes it "not ours" and never an error. The skeleton carries
`tools/patch-entity-bundle.php` as a post-install step until a release
carries it -- idempotent, and it refuses rather than guesses if the code has
moved.

Invisible in the monolith, which requires `symfony/twig-bundle` and enough
else that every optional class happens to resolve.

## 5. `coolms/core-bundle` requires translation-contracts, not translation

`ConsoleLocaleListener` autowires `Symfony\Contracts\Translation\TranslatorInterface`.
The manifest requires `symfony/translation-contracts` -- the interface --
and not `symfony/translation`, which is what provides an implementation.
The application supplied it. **The seventh instance of the same defect**
the four Core packages produced across six patch releases, and the first
found by a clean checkout rather than by CI.

## 6. Nine prototype scans that live only in the application

The `coolms/*` packages register almost none of their own services. The
application's `config/services.yaml` carries **9 prototype scans** into
`vendor/coolms/*/src/` plus 3 explicit ids, with **15 exclusion entries**
between them -- each one load-bearing, several documented with the failure
they prevent (an autowired `ChainedConfigWriter` that wins over the
extension's correctly-argued one; an `EntityWalkUserGroupResolver` that
would silently become the slow default). None of it ships. Every consumer
must reproduce it or the container does not compile.

## 7. The Symfony doctrine recipe no longer matches DoctrineBundle 3.x

`auto_generate_proxy_classes`, `enable_lazy_ghost_objects`,
`report_fields_where_declared` and `proxy_dir` are not options any more.
Loose at the `orm` level they are folded into an implicit `default` manager
and rejected, so a skeleton written from the current recipe does not boot.
The application had the same dead block in its `when@prod` section, never
caught because prod had never been built.

## 8. A Flex recipe appends a second `DATABASE_URL`

Installing `doctrine/doctrine-bundle` writes
`DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app..."` to the end
of `.env`. The last line wins, so a correctly configured skeleton fails with
`connection to server at "127.0.0.1" failed` while its database is running.

## 9. `doctrine:migrations:migrate` has nothing to run

> The version "latest" couldn't be reached, there are no registered migrations.

Correct and fatal-looking. No published package ships a migration; the eight
in the application tree build the application's tables. A distribution has
to say `doctrine:schema:create` instead, and nothing says so anywhere.

## 10. Eighteen docker services, four of them needed

The development `docker-compose.yml` runs app, nginx, postgres, redis,
meilisearch, mailhog, greenmail, adminer, gotenberg, centrifugo, coturn,
livekit, egress, asterisk, asterisk-dialer, ami-listener, messenger and a
second database. Every one past the first four belongs to a module that is
not in a package. A newcomer reading that file learns nothing about what is
required.
