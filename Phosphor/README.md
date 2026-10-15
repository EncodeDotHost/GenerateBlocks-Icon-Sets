# Phosphor Icons

Phosphor is a flexible icon family of 1,512 icons in six weights by Phosphor Icons (Tobias Fried and Helena Zhang).

Source: [https://github.com/phosphor-icons/core](https://github.com/phosphor-icons/core)
Data used: Iconify JSON for prefix `ph` from [https://github.com/iconify/icon-sets](https://github.com/iconify/icon-sets) (version 2.1.1, last modified 2024-05-06)
Categories: `dist/index.mjs` from the `@phosphor-icons/core` 2.1.1 npm package (the `figma_category` field)
Browse icons: [https://phosphoricons.com](https://phosphoricons.com)

## License

COLLECTION: Phosphor Icons
LICENSE: MIT - [https://github.com/phosphor-icons/core/blob/main/LICENSE](https://github.com/phosphor-icons/core/blob/main/LICENSE)
AUTHOR: Phosphor Icons

The MIT license asks that the copyright and permission notice is kept with copies of the icons. It does not require attribution in your page output, so no `wp_head` text is needed. The notice is:

```
MIT License

Copyright (c) 2023 Phosphor Icons

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

- The `<svg>` tag has `viewBox="0 0 256 256"` and `preserveAspectRatio="none"`, and no `width` or `height`, so GenerateBlocks controls the size.
- All paths use `fill="currentColor"`, so the icon follows the GenerateBlocks colour setting. Duotone icons draw the second tone with `opacity=".2"` on the same colour.

## Layout

Six folders, one per weight. Each has 18 files, one per category, 1,512 icons. The group name is `Phosphor (<Category>) (<Weight>)`.

| Folder | Icon name | Example |
|---|---|---|
| `Thin/` | `<name>-thin` | `heart-thin` |
| `Light/` | `<name>-light` | `heart-light` |
| `Regular/` | `<name>` | `heart` |
| `Bold/` | `<name>-bold` | `heart-bold` |
| `Fill/` | `<name>-fill` | `heart-fill` |
| `Duotone/` | `<name>-duotone` | `heart-duotone` |

The categories are Phosphor's own `figma_category` labels (Arrows, Brands, Commerce, Communication, Design, Education, Games, Health & Wellness, Maps & Travel, Math & Finance, Media, Office & Editing, People, Security & Warnings, System & Devices, Technology & Development, Time, Weather & Nature). Each icon is in exactly one category. Phosphor's website also lists a wider `categories` field where an icon may appear under several headings; that field is not used, so no icon is duplicated across groups. `categories.yml` (category to icon names) is the input used to build the files; see `phosphor_categories.py` and `build_iconify.py` in the parent workspace.

Iconify hides 89 renamed icons and lists 37 aliases (for example `archive-box` for `box-arrow-down`). Both are left out, so there are no duplicates.

## Multisite note

Users without the `unfiltered_html` capability (everyone except super admins on a multisite network) import through GenerateBlocks Pro's SVG allowlist. It removes `preserveAspectRatio` and lowercases `viewBox`. Neither affects the icons: browsers restore `viewBox` for inline SVG, and without `preserveAspectRatio="none"` the icon keeps its proportions instead of stretching. `fill="currentColor"` and `opacity` are kept, so the colour setting and the duotone tint still work. On a single site, administrators are not affected.
