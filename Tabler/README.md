# Tabler Icons

Tabler Icons is a free set of over 5,000 outline and filled icons by Paweł Kuna.

Source: [https://github.com/tabler/tabler-icons](https://github.com/tabler/tabler-icons)
Data used: Iconify JSON for prefix `tabler` from [https://github.com/iconify/icon-sets](https://github.com/iconify/icon-sets) (version 3.49.0, last modified 2026-10-07)
Categories: `icons.json` from the `@tabler/icons` 3.49.0 npm package
Browse icons: [https://tabler.io/icons](https://tabler.io/icons)

## License

COLLECTION: Tabler Icons
LICENSE: MIT - [https://github.com/tabler/tabler-icons/blob/main/LICENSE](https://github.com/tabler/tabler-icons/blob/main/LICENSE)
AUTHOR: Paweł Kuna

The MIT license asks that the copyright and permission notice is kept with copies of the icons. It does not require attribution in your page output, so no `wp_head` text is needed. The notice is:

```
MIT License

Copyright (c) 2020-2026 Paweł Kuna

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
- Outline icons use `stroke="currentColor"`, filled icons use `fill="currentColor"`, so the icon follows the GenerateBlocks colour setting.

## Layout

- `Outline/` - 41 files, one per category, 5,184 icons. Group name `Tabler Icons (<Category>) (Outline)`.
- `Filled/` - 33 files, one per category, 1,054 icons. Group name `Tabler Icons (<Category>) (Filled)`. Filled icon names end in `-filled`.

The categories and their labels come from Tabler's own metadata. Each icon is in exactly one category. `categories.yml` (category to icon names) and `suffixes.yml` (name suffix to style) are the inputs used to build the files; see `build_iconify.py` in the parent workspace.

Iconify hides 48 deprecated icon names and lists 194 aliases. Both are left out, so there are no duplicates.

## Multisite note

Users without the `unfiltered_html` capability (everyone except super admins on a multisite network) import through GenerateBlocks Pro's SVG allowlist. It removes `preserveAspectRatio` and lowercases `viewBox`. Neither affects the icons: browsers restore `viewBox` for inline SVG, and without `preserveAspectRatio="none"` the icon keeps its proportions instead of stretching. `stroke="currentColor"` and `fill="currentColor"` are kept, so the colour setting still works. On a single site, administrators are not affected.
