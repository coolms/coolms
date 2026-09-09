# How the admin ships -- unresolved, with the three options costed

Measured 2026-09-03 against the CoolMS admin theme.

## The state

The Angular admin imports **14** `@coolms/*` specifiers at **475 import
sites across 301 files**, and declares **zero** of them:

```
dependencies      @coolms/*: 0
peerDependencies  @coolms/*: 0
devDependencies   @coolms/*: 0
```

They resolve through `tsconfig` `paths` into sibling source trees
(`../../ui-angular/src/public-api.ts` and so on). That works only where those
siblings are checked out next to each other, which a consumer installing from
Packagist will not have.

The build output is `theme-admin/public/`, gitignored. Of 513 files tracked
in the repository, **0** are build output (the 25 matching `public` are
`angular/public/assets/document-fonts/*`, which are source assets).

### What the megabytes actually are

**39.29 MB across 682 files.** (The 42M quoted earlier was `du` block usage,
not bytes.)

| kind | size | share |
| --- | --- | --- |
| pdf.js viewer/worker build variants (7 files) | 14.02 MB | 36% |
| fonts (`.ttf`/`.woff`/`.woff2`) | 9.55 MB | 24% |
| the application bundle `main-*.js` | 3.79 MB | 10% |
| pdf.js locale files (225 `.ftl`) | 3.59 MB | 9% |
| pdf.js character maps (168 `.bcmap`) | 1.11 MB | 3% |
| wasm | 0.87 MB | 2% |
| the stylesheet (1 file) | 0.39 MB | 1% |

!! **Source maps: 0.00 MB.** The usual "more than half a build is source
maps" does not apply -- this build ships none at all. Byte-identical
duplicate files come to **0.33 MB**, so there is no easy win there either.

!! **The 14 MB of pdf.js variants is cold weight, not dead weight.** The
seven files are `pdf.worker` and `viewer` in plain, `-es5` and `.min` forms,
and a browser loads one worker and one viewer per session. A first pass said
"referenced by nothing, 14 MB removable" and that was an artefact of
searching for literal filenames: the shipped bundle builds the path at
runtime --

```js
workerSrc: function(){ return Fa(he.needsES5 ? `${Hi(he.assetsFolder)}/pdf.worker-...
```

-- so which variants are reachable depends on the `needsES5` branch and the
minification flag, and that is a viewer-configuration question rather than
something a byte scan settles. **Do not delete any of them on the strength
of this page.**

What the breakdown does settle: the artefact is 39 MB because it carries
every build variant and every pdf.js locale, while the application's own
code is under 4 MB.

So the admin is **neither shipped built nor buildable outside this tree**.

!! **Publishing the nine npm peers did not change this.** The admin does not
ask npm for them -- it declares nothing, so nothing resolves from a
registry.

Per-specifier weight, for whoever picks:

```
ui-angular 227 | core-angular 189 | editor-angular 30 | designer 6
document-angular 6 | image-editor-angular 4 | document-viewer-angular 4
pdf-angular 3 | document-engine 2 | sheet-editor-angular 1
```

## The three options

### A. Commit the build as a distribution artefact

Track `theme-admin/public/` and ship it in the composer package.

- **Cost:** 39 MB of generated files in git, re-churned every release; 121
  hashed bundle names change on every build, so the diff is a full replace
  each time.
- **Buys:** works today, needs no npm publishing, no build tooling for the
  consumer, and nothing else has to be decided first.

### B. Make the dependencies real

Declare the 14 specifiers at published versions, delete the `tsconfig`
paths, consume built output.

- !! **Blocked, not merely expensive.** `@coolms/document-angular` is
  imported at **6 sites** and is deliberately private -- it 404s on npm. B
  cannot be completed without either publishing it or removing those six
  imports.
- !! **Blocked a second time, measured.** The published `@coolms/designer`
  `0.1.0-alpha.1` declares an `exports` map with **three** subpaths -- `.`,
  `./global`, `./styles`. The admin imports **five** designer specifiers:
  `@coolms/designer` plus `/bpmn-lite`, `/dmn-drd`, `/dmn-table` and
  `/state-machine`. Four of the five cannot be resolved from the registry
  today at all.
- Further cost: `packages/*-angular/node_modules` is a symlink to the
  admin's single Angular tree, so "npm install in the admin" is not a local
  operation.

!! Separately, and in `designer`'s favour: its absent `dependencies` field
is **correct**. Swept its 98 source `.ts` files for bare runtime specifiers
and found **zero** -- every import is relative. A package declaring nothing
can be either genuinely self-contained or broken from a clean checkout, and
this one is the first.

### C. Ship the theme prebuilt from its own repository

Build in `theme-admin`'s own CI and commit or attach the artefact there.

- **Cost:** CI that builds Angular with the sibling sources available --
  which today means checking out the other repositories, so it inherits
  most of B's problem.
- **Buys:** the 39 MB lands in one repository rather than in every consumer,
  and the artefact is reproducible from a pipeline rather than a laptop.

## The recommendation, not a decision

**A is the cheapest, and B is blocked.** A can be done this week and does
not foreclose anything: C is A plus a pipeline, and B stays available once
`document-angular` is resolved either way.

But A commits 39 MB of generated output to version control, which is a
standing cost paid by everyone who clones, and reversing it later means
rewriting history or living with the objects. **That is a call for the
project owner, and this document exists so it is not made silently.**

## The skeleton does not require `coolms/theme-admin`

Deliberately. The composer package would install, and the admin it is
supposed to serve is 39 MB of gitignored build output that is not in it --
so requiring it would give a newcomer a bundle registration, a route,
and a blank page. It goes in the day one of the three options above is
chosen and implemented, and not before.
