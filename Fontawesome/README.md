# Fontawesome

## Font Awesome Free License
[https://fontawesome.com/license/free](https://fontawesome.com/license/free)

The SVG icons for Font Awesome are released under the CC BY 4.0 License which means if you use these in Generateblocks you will need to reference this in the html output of the page.
The easiest way to do this is to create a Element and output the text under the wp_head hook. Suggested attribution:


`Font Awesome Free 7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2026 Fonticons, Inc.`


The group imports have been built from the Font Awesome Free 7.3.1 svg icons which have been broken down into their respective categories.

## Layout

- `Solid/` - one file per category. Group name `FontAwesome (Free) (<Category>)`.
- `Regular/` - one file per category that has free regular icons. Group name `FontAwesome (Free) (<Category>) (Regular)`.
- `Brands/` - one file per category that has brand icons. Group name `FontAwesome (Free) (<Category>) (Brands)`. `group-FontAwesome (Free) (Brands).json` holds every free brand icon in one group.

### Brand categories

Font Awesome only places 55 of its 572 free brand icons in a category. The rest were categorised by hand in `brands-categories.yml`, using the same category labels as Font Awesome. The first draft came from each icon's search terms and every entry was then reviewed. A brand can sit in more than one category, for example `tiktok` is in Social and Film + Video. The Creative Commons licence icons and a few brands with no clear fit are in `group-FontAwesome (Free) (Other) (Brands).json`.

The categories are an opinion, not Font Awesome data. To move an icon, edit `brands-categories.yml` and rebuild the groups with the generator.

Font Awesome tags many icons with more than one category, so the same icon can appear in several files.

You can search for icons here: [https://fontawesome.com/search?ic=free&s=solid](https://fontawesome.com/search?ic=free&s=solid) then narrow down using the categories in their navigation. Change `s=solid` to `s=regular` or `s=brands` for the other styles.
