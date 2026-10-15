<?php
/**
 * Plugin Name: Icon Sets for GenerateBlocks
 * Plugin URI: https://github.com/EncodeDotHost/Icon-Sets-for-GenerateBlocks
 * Description: Installs ready-made icon groups from the Icon Sets for GenerateBlocks repository into the GenerateBlocks Pro Asset Library.
 * Version: 1.0.0
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * Author: EncodeDotHost
 * Author URI: https://github.com/EncodeDotHost
 * License: GPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI: https://github.com/EncodeDotHost/Icon-Sets-for-GenerateBlocks
 * Text Domain: generateblocks-icon-sets
 *
 * @package Icon_Sets_for_GenerateBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GBIS_VERSION', '1.0.0' );
define( 'GBIS_FILE', __FILE__ );
define( 'GBIS_DIR', plugin_dir_path( __FILE__ ) );
define( 'GBIS_URL', plugin_dir_url( __FILE__ ) );
define( 'GBIS_REPO', 'EncodeDotHost/Icon-Sets-for-GenerateBlocks' );
define( 'GBIS_BRANCH', 'main' );
define( 'GBIS_SLUG', 'generateblocks-icon-sets' );

// The updater works without GenerateBlocks Pro so the plugin can still be updated.
require_once GBIS_DIR . 'includes/class-updater.php';
GBIS_Updater::init();

add_action( 'plugins_loaded', 'gbis_init', 20 );
/**
 * Load the plugin once GenerateBlocks Pro has loaded.
 *
 * GenerateBlocks Pro defines GENERATEBLOCKS_PRO_VERSION in its main file and
 * loads its functions on plugins_loaded (priority 10) only when the free
 * GenerateBlocks plugin is active. Both checks are needed.
 */
function gbis_init() {
	if ( ! defined( 'GENERATEBLOCKS_PRO_VERSION' ) || ! function_exists( 'generateblocks_pro_kses_svg' ) ) {
		add_action( 'admin_notices', 'gbis_missing_pro_notice' );
		return;
	}

	require_once GBIS_DIR . 'includes/class-source.php';
	require_once GBIS_DIR . 'includes/class-library.php';
	require_once GBIS_DIR . 'includes/class-rest.php';
	require_once GBIS_DIR . 'includes/class-admin-page.php';
	require_once GBIS_DIR . 'includes/class-attribution.php';

	GBIS_Rest::init();
	GBIS_Admin_Page::init();
	GBIS_Attribution::init();

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		require_once GBIS_DIR . 'includes/class-cli.php';
		WP_CLI::add_command( 'gb-icon-sets', 'GBIS_CLI' );
	}
}

/**
 * Admin notice shown when GenerateBlocks Pro is not active.
 */
function gbis_missing_pro_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'Icon Sets for GenerateBlocks needs GenerateBlocks and GenerateBlocks Pro to be active. The Asset Library is a GenerateBlocks Pro feature.', 'generateblocks-icon-sets' );
	echo '</p></div>';
}

/**
 * Plugin settings with defaults.
 *
 * @return array
 */
function gbis_get_settings() {
	$defaults = array( 'print_attribution' => true );
	$saved    = get_option( 'gbis_settings', array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return array_merge( $defaults, $saved );
}
