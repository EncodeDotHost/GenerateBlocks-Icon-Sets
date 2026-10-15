# Remix Icon

Remix Icon is a set of neutral-style system icons by Remix Design, in line and fill styles.

Source: [https://github.com/Remix-Design/RemixIcon/tree/v4.8.0](https://github.com/Remix-Design/RemixIcon/tree/v4.8.0)
Data used: Iconify JSON for prefix `ri` from [https://github.com/iconify/icon-sets](https://github.com/iconify/icon-sets) (version 4.8.0, last modified 2026-02-10). Iconify builds it from its Apache 2.0 fork [https://github.com/cyberalien/RemixIcon](https://github.com/cyberalien/RemixIcon).
Categories: the `categories` in the Iconify JSON, which match `tags.json` in Remix Icon 4.8.0
Browse icons: [https://remixicon.com](https://remixicon.com)

## License

COLLECTION: Remix Icon 4.8.0
LICENSE: Apache License 2.0 - a full copy is in [LICENSE](LICENSE) in this folder, as the license requires, and at [https://github.com/Remix-Design/RemixIcon/blob/v4.8.0/License](https://github.com/Remix-Design/RemixIcon/blob/v4.8.0/License)
AUTHOR: Remix Design

Copyright Remix Design. Licensed under the Apache License, Version 2.0 (the "License"); you may not use these files except in compliance with the License. Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.

Apache 2.0 does not require attribution in your page output, so no `wp_head` text is needed.

### Version and license change

These files are Remix Icon 4.8.0 (released 2025-12-29), the last release under Apache 2.0. From version 4.9.0 (January 2026), Remix Design uses the "Remix Icon License v1.0", which is not an open-source license. The icons in this folder come only from 4.8.0 and stay under Apache 2.0. Icons added in 4.9.0 and later are not included.

### Modifications

The SVGs are rebuilt from the Iconify icon bodies, as the Apache License asks us to state:

- The `<svg>` tag has `viewBox="0 0 24 24"` and `preserveAspectRatio="none"`, and no `width` or `height`, so GenerateBlocks controls the size.
- Every path uses `fill="currentColor"`, so the icon follows the GenerateBlocks colour setting.

## Layout

- `Line/` - 20 files, one per category, 1,669 icons. Group name `Remix Icon (<Category>) (Line)`.
- `Fill/` - 19 files, one per category, 1,519 icons. Group name `Remix Icon (<Category>) (Fill)`.

Icon names end in `-line` or `-fill`. The 150 Editor icons have no suffix and no fill version (`bold`, `italic`, `h-1`, ...), so they are in `Line/` only. There is no Editor file in `Fill/`.

The categories and their labels come from Remix Icon's own metadata. Each icon is in exactly one category. `suffixes.yml` (name suffix to style) is the input used to build the files; see `build_iconify.py` in the parent workspace.

Iconify hides 41 icon names and lists 15 aliases. Both are left out, so there are no duplicates.

## Multisite note

Users without the `unfiltered_html` capability (everyone except super admins on a multisite network) import through GenerateBlocks Pro's SVG allowlist. It removes `preserveAspectRatio` and lowercases `viewBox`. Neither affects the icons: browsers restore `viewBox` for inline SVG, and without `preserveAspectRatio="none"` the icon keeps its proportions instead of stretching. `fill="currentColor"` is kept, so the colour setting still works. On a single site, administrators are not affected.
