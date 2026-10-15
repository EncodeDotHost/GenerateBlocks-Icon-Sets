<?php
/**
 * Admin page under the GenerateBlocks menu.
 *
 * @package GenerateBlocks_Icon_Sets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin page.
 */
class GBIS_Admin_Page {
	const PAGE   = 'generateblocks-icon-sets';
	const SCREEN = 'generateblocks_page_generateblocks-icon-sets';

	/**
	 * Hook in. The two generateblocks_dashboard_* filters are the ones
	 * GenerateBlocks Pro's Asset Library uses to join the dashboard header.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_filter( 'generateblocks_dashboard_tabs', array( __CLASS__, 'add_tab' ) );
		add_filter( 'generateblocks_dashboard_screens', array( __CLASS__, 'add_screen' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( GBIS_FILE ), array( __CLASS__, 'action_links' ) );
	}

	/**
	 * Page URL.
	 *
	 * @return string
	 */
	public static function url() {
		return admin_url( 'admin.php?page=' . self::PAGE );
	}

	/**
	 * Add the submenu item.
	 */
	public static function add_menu() {
		add_submenu_page(
			'generateblocks',
			__( 'Icon Sets', 'generateblocks-icon-sets' ),
			__( 'Icon Sets', 'generateblocks-icon-sets' ),
			'manage_options',
			self::PAGE,
			array( __CLASS__, 'render' ),
			6
		);
	}

	/**
	 * Add a dashboard tab.
	 *
	 * @param array $tabs Tabs.
	 * @return array
	 */
	public static function add_tab( $tabs ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		$tabs['icon-sets'] = array(
			'name'  => __( 'Icon Sets', 'generateblocks-icon-sets' ),
			'url'   => self::url(),
			'class' => ( $screen && self::SCREEN === $screen->id ) ? 'active' : '',
		);

		return $tabs;
	}

	/**
	 * Register the screen as a dashboard page.
	 *
	 * @param array $screens Screen ids.
	 * @return array
	 */
	public static function add_screen( $screens ) {
		$screens[] = self::SCREEN;
		return $screens;
	}

	/**
	 * Settings link on the Plugins screen.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Install icons', 'generateblocks-icon-sets' ) . '</a>' );
		return $links;
	}

	/**
	 * Enqueue the page script and style.
	 *
	 * @param string $hook Page hook.
	 */
	public static function enqueue( $hook ) {
		if ( self::SCREEN !== $hook ) {
			return;
		}

		wp_enqueue_style( 'gbis-admin', GBIS_URL . 'assets/admin.css', array(), GBIS_VERSION );
		wp_enqueue_script( 'gbis-admin', GBIS_URL . 'assets/admin.js', array( 'wp-api-fetch', 'wp-i18n' ), GBIS_VERSION, true );
		wp_set_script_translations( 'gbis-admin', 'generateblocks-icon-sets' );

		wp_localize_script(
			'gbis-admin',
			'gbisData',
			array(
				'namespace'       => GBIS_Rest::NS,
				'settings'        => gbis_get_settings(),
				'assetLibraryUrl' => admin_url( 'admin.php?page=generateblocks-asset-library' ),
				'galleryUrl'      => 'https://encodedothost.github.io/GenerateBlocks-Icon-Sets/',
				'unfilteredHtml'  => current_user_can( 'unfiltered_html' ),
			)
		);
	}

	/**
	 * Render the page. The set list is built by assets/admin.js.
	 */
	public static function render() {
		$settings = gbis_get_settings();
		?>
		<div class="wrap gblocks-dashboard-wrap gbis-wrap">
			<h1><?php esc_html_e( 'Icon Sets', 'generateblocks-icon-sets' ); ?></h1>
			<p>
				<?php esc_html_e( 'Install icon groups from the GenerateBlocks Icon Sets repository into the GenerateBlocks Pro Asset Library.', 'generateblocks-icon-sets' ); ?>
				<a href="https://encodedothost.github.io/GenerateBlocks-Icon-Sets/" target="_blank" rel="noopener"><?php esc_html_e( 'Preview the icons in the gallery', 'generateblocks-icon-sets' ); ?></a>.
			</p>

			<div class="notice notice-warning inline">
				<p>
					<?php esc_html_e( 'Do not keep the Asset Library page open while you install here. If you save on that page afterwards without reloading it, it overwrites the icon library and the new groups are lost.', 'generateblocks-icon-sets' ); ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=generateblocks-asset-library' ) ); ?>"><?php esc_html_e( 'Open the Asset Library', 'generateblocks-icon-sets' ); ?></a>
				</p>
			</div>

			<?php if ( ! current_user_can( 'unfiltered_html' ) ) : ?>
				<div class="notice notice-info inline">
					<p><?php esc_html_e( 'Your account does not have the unfiltered_html capability. GenerateBlocks Pro\'s SVG allowlist is applied to each icon, the same as when you import a file by hand. It removes preserveAspectRatio. The icons still work.', 'generateblocks-icon-sets' ); ?></p>
				</div>
			<?php endif; ?>

			<div class="gbis-settings">
				<label>
					<input type="checkbox" id="gbis-print-attribution" <?php checked( ! empty( $settings['print_attribution'] ) ); ?>>
					<?php esc_html_e( 'Print the attribution text that some licenses require (Font Awesome: CC BY 4.0) in the page head.', 'generateblocks-icon-sets' ); ?>
				</label>
				<span class="gbis-settings-status" aria-live="polite"></span>
			</div>

			<div class="gbis-toolbar">
				<button type="button" class="button button-primary" data-action="install" disabled><?php esc_html_e( 'Install selected', 'generateblocks-icon-sets' ); ?></button>
				<button type="button" class="button" data-action="reinstall" disabled><?php esc_html_e( 'Reinstall selected', 'generateblocks-icon-sets' ); ?></button>
				<button type="button" class="button" data-action="remove" disabled><?php esc_html_e( 'Remove selected', 'generateblocks-icon-sets' ); ?></button>
				<button type="button" class="button" data-action="update-all" disabled><?php esc_html_e( 'Update all changed', 'generateblocks-icon-sets' ); ?></button>
				<button type="button" class="button-link" data-action="refresh"><?php esc_html_e( 'Refresh from GitHub', 'generateblocks-icon-sets' ); ?></button>
				<span class="gbis-selected-count" aria-live="polite"></span>
			</div>

			<div class="gbis-progress" hidden>
				<progress max="1" value="0"></progress>
				<span class="gbis-progress-text" aria-live="polite"></span>
			</div>
			<ul class="gbis-log" aria-live="polite"></ul>

			<div id="gbis-app">
				<p class="gbis-loading"><?php esc_html_e( 'Loading the set list from GitHub…', 'generateblocks-icon-sets' ); ?></p>
				<noscript><p><?php esc_html_e( 'This page needs JavaScript.', 'generateblocks-icon-sets' ); ?></p></noscript>
			</div>
		</div>
		<?php
	}
}
