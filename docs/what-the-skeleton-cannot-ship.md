# What is application code, and what is a platform module not yet extracted

Measured 2026-09-03 against the CoolMS application tree.

## The application tree is almost entirely platform

`src/` holds **64 module directories and 3,424 PHP files**. Swept for the
tells of code that belongs to coolms.dev rather than to the product --
`coolms.dev`, `coolms-site`, `CoolmsSite`, `altgraphic` -- **5 files**
match, and only **one** of them matches in code rather than in a comment:

- `src/Web/Infrastructure/Console/InstallCoolmsSiteCommand.php` -- already
  recorded in `docs/investigations/temporary-passengers.md`.
- `src/Decision/Application/Service/DecisionDraftProvisioner.php` emits
  `namespace="https://coolms.dev/dmn"` in generated DMN. That is a product
  namespace URI, the same kind of thing as `https://symfony.com/schema` --
  not a passenger, but worth knowing it is there before a stranger opens a
  generated file.
- The other three (`PublicSeoController`, `SiteSectionApplyResource`,
  `ThemesOptionSource`) name our own deployment only in docblocks, as the
  worked example for routing precedence.

**So the answer is: essentially none of it is application code.** `src/` is
the platform, and the extraction backlog is not a shortlist -- it is the
tree.

## The part that is not merely pending, but load-bearing today

Four contracts are REQUIRED constructor arguments of published packages and
are implemented only inside the application:

| port | declared in | consumed by | implemented in |
| --- | --- | --- | --- |
| `FieldSchemaSourceInterface` | `coolms/entity` | `coolms/entity` | `src/Field` |
| `FieldMetadataSourceInterface` | `coolms/entity` | `coolms/entity-doctrine` | `src/Field` |
| `EntityTypeSchemaContributorInterface` | `coolms/entity` | `coolms/entity` | `src/DynamicEntity` |
| `VisitorReferenceGeneratorInterface` | `coolms/core` | `coolms/core-bundle` | `src/Analytics` |

Method: every interface declared in the 14 installed packages (**135**),
narrowed to those consumed as a constructor argument (**27**), narrowed to
those with no implementing class anywhere in the installed set. Four. None
is optional and none has a null default.

⚠️ **Measured against `vendor/`, not against `backend/packages/`.** The first
run of this sweep used the working tree and reported **five**, adding a
`ModuleNavigationRemoverInterface` implemented by `src/Navi`. That interface
does not exist in the published `coolms/core` at all, and the published
`coolms/core-bundle` never asks for it -- both are part of unreleased work.
A question about what a CONSUMER gets has exactly one correct denominator,
and it is `vendor/`.

## The registry is behind the working tree

```
coolms/core          published 204 php files, working tree 207  (+3)
coolms/core-bundle   published  63 php files, working tree  70  (+7)
```

Ten files of unreleased work, which is ordinary. What is not ordinary is
that the application resolves those packages through `path` repositories,
so it has never once run against the versions its own `composer.json`
names. Nothing here would have surfaced any other way.

⚠️ **This means `coolms/entity` v2.0.0-alpha1 cannot compile a container in
any application that is not CoolMS.** It was published, it installs, and it
does not work -- the difference the npm session named for the Angular
packages, in the PHP set.

The skeleton papers over it with three stand-in classes under
`src/Platform/`, each naming the module that should replace it. Deleting
those three files is the acceptance test for the extraction.

## Priority, if the backlog needs an order

By this measurement rather than by size: `Field` (2 ports, blocks the whole
extras engine), then `DynamicEntity` and `Analytics` (1 each). Those three
are what a package consumer is missing before any question of features
arises.
