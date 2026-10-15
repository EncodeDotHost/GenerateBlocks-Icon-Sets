# Bootstrap Icons

Bootstrap Icons is the official open-source icon set of the Bootstrap project: 2,078 icons on a 16x16 grid, by The Bootstrap Authors. Outline and filled icons share one set; filled icons carry `fill` in their name (for example `alarm-fill`, `person-fill-add`).

Source: [https://github.com/twbs/icons](https://github.com/twbs/icons)
Data used: Iconify JSON for prefix `bi` from [https://github.com/iconify/icon-sets](https://github.com/iconify/icon-sets) (version 1.13.2, last modified 2025-12-04)
Categories: `docs/content/icons/<name>.md` front matter from the Bootstrap Icons 1.13.2 source archive
Browse icons: [https://icons.getbootstrap.com](https://icons.getbootstrap.com)

## License

COLLECTION: Bootstrap Icons
LICENSE: MIT - [https://github.com/twbs/icons/blob/main/LICENSE.md](https://github.com/twbs/icons/blob/main/LICENSE.md)
AUTHOR: The Bootstrap Authors

The MIT license asks that the copyright and permission notice is kept with copies of the icons. It does not require attribution in your page output, so no `wp_head` text is needed. The notice is:

```
The MIT License (MIT)

Copyright (c) 2019-2024 The Bootstrap Authors

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```

### Modifications

The SVGs are rebuilt from the Iconify icon bodies:

- The `<svg>` tag has `viewBox="0 0 16 16"` and `preserveAspectRatio="none"`, and no `width` or `height`, so GenerateBlocks controls the size.
- Paths use `fill="currentColor"`, so the icon follows the GenerateBlocks colour setting. Outline icons are drawn as filled outlines, not strokes.

## Layout

One folder, `Bootstrap Icons/`, with 29 files, one per category, 2,078 icons. The group name is `Bootstrap Icons (<Category>)`. Icon names are the Bootstrap names (for example `house`, `arrow-left-right`, `house-fill`).

The categories are Bootstrap's own category labels from each icon's documentation page. Upstream lists exactly one category per icon, so each icon is in exactly one group. Outline and filled icons are not split into styles, because 35 filled icons carry `fill` in the middle of the name (for example `person-fill-add`) and upstream treats them all as one set. `categories.yml` (category to icon names) is the input used to build the files; see `bootstrap_categories.py` and `build_iconify.py` in the parent workspace.

Iconify hides 6 misspelled icon names and lists 6 aliases (for example `people-circle` for `person-circle`). Both are left out, so there are no duplicates.

## Multisite note

Users without the `unfiltered_html` capability (everyone except super admins on a multisite network) import through GenerateBlocks Pro's SVG allowlist. It removes `preserveAspectRatio` and lowercases `viewBox`. Neither affects the icons: browsers restore `viewBox` for inline SVG, and without `preserveAspectRatio="none"` the icon keeps its proportions instead of stretching. `fill="currentColor"` is kept, so the colour setting still works. On a single site, administrators are not affected.
