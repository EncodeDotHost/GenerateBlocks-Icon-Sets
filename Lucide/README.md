# Lucide

Lucide is a community-run fork of Feather Icons: 1,890 outline icons on a 24x24 grid with a 2px stroke, by Lucide Contributors.

Source: [https://github.com/lucide-icons/lucide](https://github.com/lucide-icons/lucide)
Data used: Iconify JSON for prefix `lucide` from [https://github.com/iconify/icon-sets](https://github.com/iconify/icon-sets) (last modified 2026-10-14, matching the Lucide 1.57.0 release)
Categories: `icons/<name>.json` and `categories/<slug>.json` from the Lucide 1.57.0 source archive
Browse icons: [https://lucide.dev/icons](https://lucide.dev/icons)

## License

COLLECTION: Lucide
LICENSE: ISC - [https://github.com/lucide-icons/lucide/blob/main/LICENSE](https://github.com/lucide-icons/lucide/blob/main/LICENSE)
AUTHOR: Lucide Contributors

The ISC license asks that the copyright and permission notice is kept with copies of the icons. It does not require attribution in your page output, so no `wp_head` text is needed. The notice is:

```
ISC License

Copyright (c) 2026 Lucide Icons and Contributors

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
```

Some Lucide icons are derived from the Feather project, which is MIT licensed (Copyright (c) 2013-present Cole Bemis). The Lucide LICENSE file lists those icons and carries the MIT notice for them. The MIT license also needs no page attribution.

### Modifications

The SVGs are rebuilt from the Iconify icon bodies:

- The `<svg>` tag has `viewBox="0 0 24 24"` and `preserveAspectRatio="none"`, and no `width` or `height`, so GenerateBlocks controls the size.
- Paths are drawn with `stroke="currentColor"` and `fill="none"`, so the icon follows the GenerateBlocks colour setting. The stroke width is 2.

## Layout

One folder, `Lucide/`, with 40 files, one per category, 1,890 icons. The group name is `Lucide (<Category>)`. Icon names are the Lucide names (for example `house`, `arrow-left-right`).

The categories are Lucide's own category titles. Upstream lists one or more categories per icon with no primary field; the first listed category is used, so each icon is in exactly one group. Two upstream categories (People and Seasons) are never listed first and so have no file. `categories.yml` (category to icon names) is the input used to build the files; see `lucide_categories.py` and `build_iconify.py` in the parent workspace.

Iconify hides 72 renamed icons and lists 219 aliases (for example `home` for `house`). Both are left out, so there are no duplicates.

## Multisite note

Users without the `unfiltered_html` capability (everyone except super admins on a multisite network) import through GenerateBlocks Pro's SVG allowlist. It removes `preserveAspectRatio` and lowercases `viewBox`. Neither affects the icons: browsers restore `viewBox` for inline SVG, and without `preserveAspectRatio="none"` the icon keeps its proportions instead of stretching. `stroke="currentColor"`, `stroke-width`, `stroke-linecap` and `stroke-linejoin` are kept, so the colour setting and line style still work. On a single site, administrators are not affected.
