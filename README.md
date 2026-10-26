# djmichael.com WordPress theme

Classic PHP theme (`djmichael/`) whose pages are built from ACF Pro blocks, one block per page section. The approved static prototype lives in [`djmichael-com`](https://github.com/abeuscher/djmichael-com).

## Local setup (Docker in WSL2)

Keep the repo inside the WSL filesystem (e.g. `~/code/djmichael-wp-theme`), not under `/mnt/c`, or Docker file access is very slow.

1. Unzip ACF Pro into `plugins/` so `plugins/advanced-custom-fields-pro/acf.php` exists.
2. Optional: `cp .env.example .env` to change ports.
3. Run `bin/setup.sh`.

That starts the containers, installs WordPress, activates ACF Pro, Fluent Forms and the theme, and runs the seed.

- Site: http://localhost:8080 (admin / admin)
- Mail catcher: http://localhost:8025 (all local mail goes here)
- WP-CLI: `docker compose run --rm cli wp <command>`
- Reset everything: `docker compose down -v`, then `bin/setup.sh`

## Content: `wp djm seed`

`docker compose run --rm cli wp djm seed` builds the site's content from `djmichael/seed/content.php` and the files in `seed/media/`:

- imports media (matched by file name, so reruns don't duplicate),
- writes Site settings,
- builds the Fluent Forms contact form (matched by a seed key in its meta),
- creates or updates the pages by slug, as ACF block markup holding the right attachment IDs,
- builds the header and footer menus, and sets the front page and permalinks.

Field values can point at the things the seed creates: `@media:<key>` becomes an
attachment ID, `@page:<slug>` a page's permalink and `@form:<key>` a form ID.

It's safe to rerun, and it overwrites page content and Site settings with the seed
versions — so an edit made in Site settings is lost on the next run. Pass
`--skip-options` to refresh the pages and leave Site settings alone. Use it for local and staging builds; production is a copy of approved staging rather than a fresh seed.

Seeding exists because ACF stores image fields as attachment IDs inside block data, which the WordPress XML importer doesn't remap.

## Theme layout

```
djmichael/
  acf-json/          Field groups (Site settings, page SEO, one per block). Edited in the ACF UI, saved here.
  assets/            tokens.css (design tokens + fonts), site.css, fluent-forms.css, site.js, self-hosted fonts
  blocks/<name>/     block.json + render.php for each section
  inc/               setup, assets, ACF wiring, head output, Fluent Forms, helpers, seed command and form builder
  seed/content.php   Seed copy, options, forms, pages, menus
  header.php footer.php page.php index.php
seed/media/          Media imported by the seed (outside the theme so theme zips stay small)
dev/mu-plugins/      Local-only helpers (mail to Mailpit)
```

### Adding a block

1. Create `djmichael/blocks/<name>/block.json` (name `acf/<name>`, category `djmichael`, `acf.renderTemplate: render.php`) and `render.php`. Port the markup from the prototype's matching `data-part` section.
2. In WP admin, add a field group with location "Block is equal to <title>". It saves to `acf-json/`.
3. Add the block to the right page in `seed/content.php` and reseed.

Pages only accept this theme's blocks (`allowed_block_types_all` in `inc/setup.php`).

## Production (SiteGround)

The live site is `~/www/djmichael.com/public_html` on SiteGround (SSH port 18765, WP-CLI available). It was switched over from the old `dj` theme in place on 2026-10-26 with `bin/cutover.sh`: database dump, maintenance mode, https URLs, plugin and theme swap, seed, cache purge. It took about 30 seconds. The cutover only adds files, so `bin/rollback.sh <dump>` undoes it by restoring that dump.

`rehearsal/` runs a SiteGround backup (`djm-db.sql.gz` + `djm-files.tar.gz`) locally on http://localhost:8091, so a script can be tried against a copy of live first: `rehearsal/load.sh`, then the commands in each script's header.

SiteGround's page cache isn't purged when Site settings are saved. After changing them, run `wp sg purge` or use "Purge SG Cache" in the admin bar.

## Status

All of the prototype's sections are ported. Each is a block in `djmichael/blocks/`, with its field group in `djmichael/acf-json/` and its content in `djmichael/seed/content.php`.

| Page | Blocks, in order |
|---|---|
| Home | Hero, Review counts, Testimonials, Gallery and slideshow, About (short), What to expect, Services, Contact block |
| About | Page head, About (biography) |
| FAQ | Page head, FAQ, Contact band |
| Contact | Page head, Contact block |
| Preferred vendors | Page head, Vendors, Contact band |

Review counts and the Contact band have no fields of their own: they read Site settings.

Still open:
- Review quotes in Testimonials are the prototype's placeholders, pending Michael's picks.
- Zola is no longer listed in the review counts. To bring it back, add a row in Site settings → Reviews, and to the seed if it should survive a reseed.
- The About photo is an FPO image.
- Fluent Forms' own "Contact Form Demo" and "Subscription Form" are still in the admin; delete them when convenient.
