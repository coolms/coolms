# The skeleton against `docs/baseline-installation.md`

Run 2026-09-03. A clean checkout of this skeleton, in a container built
from `docker/php/Dockerfile`, against a database created empty.

```bash
composer install
php bin/console doctrine:schema:create
php bin/console coolms:install
```

## Result: 0 of 14

| baseline item | document | skeleton |
| --- | --- | --- |
| Sites | 1 | no table |
| Themes (active) | 1 | no table |
| System users | 7 | no table |
| Login accounts | 1 | no table |
| Groups | 29 | no table |
| VFS roots | 11 | no table |
| Navigation trees | 1 | no table |
| Navigation nodes | 5 | no table |
| Taxonomy trees | 2 | no table |
| Dynamic entity types | 0 | no table |
| Field definitions | 30 | no table |
| Workflow definitions | 5 | no table |
| Decision definitions | 0 | no table |
| Content packages | 1 | no table |

⚠️ **The two zero-valued rows disagree as loudly as the others.** "Dynamic
entity types -- 0" is a statement that the module ships and creates none.
"No table" is a statement that there is no module. Same number, opposite
meaning, and only one of them is a working installation.

The whole database the skeleton can build is four tables, all from
`coolms/core-doctrine`:

```
coolms_config_overrides   coolms_inbox   coolms_outbox   coolms_sync_changes
```

## So which of the two is wrong?

The task that produced this run framed it as a binary: either the installer
does not produce what it claims, or the document describes a machine rather
than a product.

**Neither.** `docs/baseline-installation.md` is accurate -- it is generated
by `tools/report-baseline-install.php` against a scratch database, and the
installer really does produce all fourteen items. The third possibility is
the true one:

> Everything that produces the baseline lives in the CoolMS application's
> `src/`, and none of it is in a package. The document describes a PRODUCT
> that has no DISTRIBUTION.

Two facts make that concrete:

- `coolms:install` ships in `coolms/core-bundle`, so the skeleton has it --
  but the command is a loop over tagged installers, and the tagged
  installers are the application's 64 modules.
- `coolms:fixtures:baseline`, which creates most of the fourteen items, is
  `src/Web/Infrastructure/Console/SeedBaselineCommand.php`. It is not in any
  package and the skeleton does not have the command at all.

## ⚠️ And `coolms:install` reports success having created nothing

The run above printed two empty sections -- "VFS structure", "Module data" --
and then:

```
 [OK] Installation complete.
```

An installer that installed nothing produces the identical green to one
that installed everything. It should state its denominator: how many
structure installers and module installers it found, and refuse (or at
minimum warn) when both are zero, because zero means the application
registered no modules and that is never what the operator wanted.

**Fixed and released the same day** as `coolms/core-bundle` v2.0.0-alpha2.
The command now names its counts per section
(`VFS structure -- 7 installer(s)`) and refuses, non-zero, when both sets
are empty. Verified both ways: the CoolMS application still installs and
reports 7 structure and 22 module installers; this skeleton exits 1.

So the last line of the run above is no longer a false green. It is the
measurement this whole page is about, printed by the installer itself.
