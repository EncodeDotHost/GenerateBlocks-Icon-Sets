# Heroicons

Heroicons is a set of 324 hand-crafted icons by the makers of Tailwind CSS, in four styles: outline at 24x24, and solid at 24x24, 20x20 and 16x16. Version 2.2.0.

Source: [https://github.com/tailwindlabs/heroicons](https://github.com/tailwindlabs/heroicons)
Data used: Iconify JSON for prefix `heroicons` from [https://github.com/iconify/icon-sets](https://github.com/iconify/icon-sets) (version 2.2.0, last modified 2025-09-20)
Categories: none. Heroicons has no category metadata, so each style is one group.
Browse icons: [https://heroicons.com](https://heroicons.com)

## License

COLLECTION: Heroicons
LICENSE: MIT - [https://github.com/tailwindlabs/heroicons/blob/master/LICENSE](https://github.com/tailwindlabs/heroicons/blob/master/LICENSE)
AUTHOR: Refactoring UI Inc (Tailwind Labs, Inc.)

The MIT license asks that the copyright and permission notice is kept with copies of the icons. It does not require attribution in your page output, so no `wp_head` text is needed. The notice is:

```
MIT License

Copyright (c) Tailwind Labs, Inc.

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

- The `<svg>` tag has a `viewBox` of `0 0 24 24`, `0 0 20 20` or `0 0 16 16` to match the style, `preserveAspectRatio="none"`, and no `width` or `height`, so GenerateBlocks controls the size.
- Outline icons are drawn with `stroke="currentColor"` and `fill="none"` (stroke width 1.5, a few 2). Solid icons use `fill="currentColor"`. Both follow the GenerateBlocks colour setting.

## Layout

One file per style, one group each:

- `Outline24/` - `Heroicons (Outline 24)`, 324 icons. Icon names are the Heroicons names (for example `academic-cap`).
- `Solid24/` - `Heroicons (Solid 24)`, 324 icons. Names end in `-solid`.
- `Solid20/` - `Heroicons (Solid 20)`, 324 icons. Names end in `-20-solid`.
- `Solid16/` - `Heroicons (Solid 16)`, 316 icons. Names end in `-16-solid`.

`style-map.yml` (Iconify theme name to folder and group suffix) is the input used to build the files; see `build_iconify.py` in the parent workspace.

Iconify lists 9 aliases (for example `code-solid` for `code-bracket-solid`). They are left out, so there are no duplicates.

## Multisite note

Users without the `unfiltered_html` capability (everyone except super admins on a multisite network) import through GenerateBlocks Pro's SVG allowlist. It removes `preserveAspectRatio` and lowercases `viewBox`. Neither affects the icons: browsers restore `viewBox` for inline SVG, and without `preserveAspectRatio="none"` the icon keeps its proportions instead of stretching. `stroke="currentColor"`, `fill="currentColor"`, `stroke-width`, `stroke-linecap` and `stroke-linejoin` are kept, so the colour setting and line style still work. On a single site, administrators are not affected.
