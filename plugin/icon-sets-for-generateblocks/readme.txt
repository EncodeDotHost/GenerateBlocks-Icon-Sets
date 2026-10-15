=== Icon Sets for GenerateBlocks ===
Contributors: encodedothost
Tags: generateblocks, icons, svg, asset library
Requires at least: 6.7
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

Installs icon groups from the Icon Sets for GenerateBlocks repository into the GenerateBlocks Pro Asset Library.

== Description ==

The Icon Sets for GenerateBlocks repository converts open-source icon sets into GenerateBlocks Pro group files. This plugin installs those groups for you. You do not have to download and import each file by hand.

* Browse every set, style and group with its license and icon count.
* Install, reinstall, update and remove groups one at a time or in bulk.
* See which groups are installed and which have a newer version in the repository.
* Print the attribution text that Font Awesome's CC BY 4.0 license requires.
* WP-CLI command: `wp gb-icon-sets`.

The plugin needs GenerateBlocks and GenerateBlocks Pro. The Asset Library is a GenerateBlocks Pro feature.

Preview the icons: https://encodedothost.github.io/Icon-Sets-for-GenerateBlocks/

= Where the data comes from =

The plugin reads `docs/sets.json` and the `group-*.json` files from https://github.com/EncodeDotHost/Icon-Sets-for-GenerateBlocks over raw.githubusercontent.com. It caches the set list for 12 hours. It checks api.github.com for plugin updates twice a day. It sends no other data.

= Licenses =

The plugin is GPL-3.0-or-later. Each icon set keeps its own license. The admin page shows the license for each set. Font Awesome Free icons are CC BY 4.0 and need attribution. Keep "Print the attribution text" on if you use them.

== Installation ==

1. Download `icon-sets-for-generateblocks.zip` from https://github.com/EncodeDotHost/Icon-Sets-for-GenerateBlocks/releases/latest
2. Go to Plugins > Add New > Upload Plugin and upload the zip. Activate it.
3. Go to GenerateBlocks > Icon Sets.
4. Open a set, select the groups you want and click Install selected.

The new groups appear in GenerateBlocks > Asset Library > Icons and in the icon picker in the editor.

== Frequently Asked Questions ==

= Do not keep the Asset Library page open =

GenerateBlocks Pro saves the whole icon library from its Asset Library page. If that page is open while you install here, and you then click Save on it without a reload, it overwrites the library and the new groups are lost. Reload the Asset Library page after you install.

= What does "Exists, not from this plugin" mean? =

A group with the same name is already in the Asset Library, for example from a manual import. The plugin does not change it unless you click Replace.

= What happens on multisite? =

Each site has its own Asset Library. Install on each site. Users without the unfiltered_html capability get the GenerateBlocks Pro SVG allowlist, which removes preserveAspectRatio. The icons still work.

= What happens when I uninstall? =

The plugin deletes its own settings. The installed icon groups stay in the Asset Library. Remove them first on the Icon Sets page if you do not want them.

== WP-CLI ==

    wp gb-icon-sets list [--set=<folder>] [--style=<folder>] [--status=<status>] [--format=<format>]
    wp gb-icon-sets install [<path>...] [--set=<folder>] [--style=<folder>] [--all] [--replace]
    wp gb-icon-sets update [--set=<folder>] [--changed]
    wp gb-icon-sets remove [<group_id>...] [--set=<folder>] [--all]
    wp gb-icon-sets refresh

Add `--user=<admin>` so the SVGs are stored unchanged.

== Changelog ==

= 1.1.0 =
* Rename the plugin to Icon Sets for GenerateBlocks. The folder, main file, text domain, admin page slug and release asset are now icon-sets-for-generateblocks. Options, constants, the WP-CLI command and the REST namespace are unchanged.
* WordPress treats this as a new plugin. Delete GenerateBlocks Icon Sets 1.0.0 and install this zip. The icon groups stay in the GenerateBlocks Pro Asset Library, but deleting the old plugin removes its install records and settings. On the Icon Sets page the groups then show as not installed by the plugin. Install them again to put them back under the plugin's control.

= 1.0.0 =
* First release.
