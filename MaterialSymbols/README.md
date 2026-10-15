# Material Symbols

Material Symbols is the official icon set from Google.

Source: [https://github.com/google/material-design-icons](https://github.com/google/material-design-icons) (`symbols/web`, commit `49d4db35df873165d6bd6ba09b063c7dafbac2f4`)
Categories: [https://fonts.google.com/metadata/icons?key=material_symbols](https://fonts.google.com/metadata/icons?key=material_symbols)
Browse icons: [https://fonts.google.com/icons](https://fonts.google.com/icons)

## License

COLLECTION: Material Symbols
LICENSE: Apache License 2.0 - a full copy is in [LICENSE](LICENSE) in this folder, as the license requires, and at [https://www.apache.org/licenses/LICENSE-2.0.txt](https://www.apache.org/licenses/LICENSE-2.0.txt)
AUTHOR: Google LLC

Copyright Google LLC. Licensed under the Apache License, Version 2.0 (the "License"); you may not use these files except in compliance with the License. Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.

Apache 2.0 does not require attribution in your page output, so no `wp_head` text is needed. Google asks that the icons are not re-sold on their own.

### Modifications

The SVGs in these group files are modified from the originals, as the Apache License asks us to state:

- `width` and `height` attributes removed so GenerateBlocks controls the size.
- `preserveAspectRatio="none"` added to the `<svg>` tag.
- `fill="currentColor"` added to every `<path>` so the icon follows the GenerateBlocks colour setting.

## Layout

The groups use the default variant of each style: weight 400, fill 0, grade 0, optical size 24.

- `Outlined/` - one file per category. Group name `Material Symbols (<Category>)`.
- `Rounded/` - one file per category. Group name `Material Symbols (<Category>) (Rounded)`.
- `Sharp/` - one file per category. Group name `Material Symbols (<Category>) (Sharp)`.

The categories and their labels come from the Google Fonts metadata. Each icon is in exactly one category.

## Multisite note

Users without the `unfiltered_html` capability (everyone except super admins on a multisite network) import through GenerateBlocks Pro's SVG allowlist. It removes `preserveAspectRatio` and lowercases `viewBox`. Neither affects the icons: browsers restore `viewBox` for inline SVG, and without `preserveAspectRatio="none"` the icon keeps its proportions instead of stretching. `fill="currentColor"` is kept, so the colour setting still works. On a single site, administrators are not affected.
