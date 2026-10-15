<?php
/**
 * Uninstall: delete this plugin's own options and transients.
 *
 * The icon groups stay in the GenerateBlocks Pro Asset Library
 * (option generateblocks_svg_icons). Remove them on the Icon Sets page
 * before you uninstall if you do not want them.
 *
 * @package Icon_Sets_for_GenerateBlocks
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'gbis_installed' );
delete_option( 'gbis_settings' );
delete_transient( 'gbis_manifest' );
delete_transient( 'gbis_release' );
