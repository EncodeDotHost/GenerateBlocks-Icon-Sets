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

Some Lucide icons are derived from the Feather project, which is MIT licensed. The Lucide LICENSE file lists those icons and carries the MIT notice for them, reproduced below. The MIT license also needs no page attribution.

```
The following Lucide icons are derived from the Feather project:

airplay, alert-circle, alert-octagon, alert-triangle, aperture, arrow-down-circle, arrow-down-left, arrow-down-right, arrow-down, arrow-left-circle, arrow-left, arrow-right-circle, arrow-right, arrow-up-circle, arrow-up-left, arrow-up-right, arrow-up, at-sign, calendar, cast, check, chevron-down, chevron-left, chevron-right, chevron-up, chevrons-down, chevrons-left, chevrons-right, chevrons-up, circle, clipboard, clock, code, columns, command, compass, corner-down-left, corner-down-right, corner-left-down, corner-left-up, corner-right-down, corner-right-up, corner-up-left, corner-up-right, crosshair, database, divide-circle, divide-square, dollar-sign, download, external-link, feather, frown, hash, headphones, help-circle, info, italic, key, layout, life-buoy, link-2, link, loader, lock, log-in, log-out, maximize, meh, minimize, minimize-2, minus-circle, minus-square, minus, monitor, moon, more-horizontal, more-vertical, move, music, navigation-2, navigation, octagon, pause-circle, percent, plus-circle, plus-square, plus, power, radio, rss, search, server, share, shopping-bag, sidebar, smartphone, smile, square, table-2, tablet, target, terminal, trash-2, trash, triangle, tv, type, upload, x-circle, x-octagon, x-square, x, zoom-in, zoom-out

The MIT License (MIT) (for the icons listed above)

Copyright (c) 2013-present Cole Bemis

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

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
