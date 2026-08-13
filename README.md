# Domio WordPress child theme

Child theme of [Hello Elementor](https://wordpress.org/themes/hello-elementor/) for Domio: Gutenberg blocks, landing patterns, header, and schema.

Repository: [Tapiokansleri/domio-wp-child-theme](https://github.com/Tapiokansleri/domio-wp-child-theme)

## Install on a site

1. Install and activate **Hello Elementor** (parent theme).
2. Install this theme into `wp-content/themes/domio/` (zip from a [GitHub Release](https://github.com/Tapiokansleri/domio-wp-child-theme/releases), or clone and run `npm install && npm run build`).
3. Activate **Domio**.

## Automatic updates

The theme uses [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) against this repo’s **GitHub Releases**.

When you publish a new release:

1. Bump `Version` in `style.css` (e.g. `1.0.35`).
2. Commit and push to `main`.
3. Create a GitHub Release with tag matching that version (`1.0.35` or `v1.0.35`).
4. The [release workflow](.github/workflows/release-zip.yml) attaches `domio.zip` (folder name `domio/`).
5. Sites with Domio installed see the update under **Appearance → Themes** (or Dashboard updates).

No need to open each site manually after a release — WordPress will offer the update on its next check (usually within 12 hours; “Check again” on the Updates screen forces it sooner).

## Develop

```bash
npm install
npm run build   # compile blocks into build/
npm start       # watch mode
```

`node_modules/` is not required on production; only `build/` is used by WordPress.

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Parent theme: Hello Elementor
