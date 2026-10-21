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
- creates or updates the pages by slug, as ACF block markup holding the right attachment IDs,
- builds the header and footer menus, and sets the front page and permalinks.

It's safe to rerun, and it overwrites page content and Site settings with the seed versions. Use it for local and staging builds; production is a copy of approved staging rather than a fresh seed.

Seeding exists because ACF stores image fields as attachment IDs inside block data, which the WordPress XML importer doesn't remap.

## Theme layout

```
djmichael/
  acf-json/          Field groups (Site settings, page SEO, one per block). Edited in the ACF UI, saved here.
  assets/            tokens.css (design tokens + fonts), site.css, site.js, self-hosted fonts
  blocks/<name>/     block.json + render.php for each section
  inc/               setup, assets, ACF wiring, head output, helpers, seed command
  seed/content.php   Seed copy, options, pages, menus
  header.php footer.php page.php index.php
seed/media/          Media imported by the seed (outside the theme so theme zips stay small)
dev/mu-plugins/      Local-only helpers (mail to Mailpit)
```

### Adding a block

1. Create `djmichael/blocks/<name>/block.json` (name `acf/<name>`, category `djmichael`, `acf.renderTemplate: render.php`) and `render.php`. Port the markup from the prototype's matching `data-part` section.
2. In WP admin, add a field group with location "Block is equal to <title>". It saves to `acf-json/`.
3. Add the block to the right page in `seed/content.php` and reseed.

Pages only accept this theme's blocks (`allowed_block_types_all` in `inc/setup.php`).

## Status

| Done | Next |
|---|---|
| Docker, theme scaffold, assets, Site settings, page SEO fields | Testimonials, Gallery + slideshow, About (short), What to expect, Services |
| Header/footer (menus from WP), structured data, GA4 (off locally) | Contact block (Fluent Forms), Page head, FAQ (+ JSON-LD), Vendors, Contact band |
| Blocks: Hero, Review counts | Seed content for all pages |
| Seed command | |
