<?php
/**
 * REST routes used by the admin page.
 *
 * @package GenerateBlocks_Icon_Sets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST controller, namespace gb-icon-sets/v1.
 */
class GBIS_Rest {
	const NS = 'gb-icon-sets/v1';

	/**
	 * Hook in.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Same capability GenerateBlocks Pro asks for to save icons.
	 *
	 * @return bool
	 */
	public static function permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register routes.
	 */
	public static function register_routes() {
		register_rest_route(
			self::NS,
			'/manifest',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'manifest' ),
				'permission_callback' => array( __CLASS__, 'permission' ),
				'args'                => array(
					'refresh' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/install',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'install' ),
				'permission_callback' => array( __CLASS__, 'permission' ),
				'args'                => array(
					'path'    => array(
						'type'     => 'string',
						'required' => true,
					),
					'replace' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/remove',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'remove' ),
				'permission_callback' => array( __CLASS__, 'permission' ),
				'args'                => array(
					'group_id' => array(
						'type'     => 'string',
						'required' => true,
					),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/settings',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'settings' ),
				'permission_callback' => array( __CLASS__, 'permission' ),
				'args'                => array(
					'print_attribution' => array(
						'type'     => 'boolean',
						'required' => true,
					),
				),
			)
		);
	}

	/**
	 * Turn a WP_Error into a REST error with status 400 or 502.
	 *
	 * @param WP_Error $error Error.
	 * @return WP_Error
	 */
	private static function error( $error ) {
		$remote = in_array( $error->get_error_code(), array( 'gbis_http', 'http_request_failed', 'gbis_invalid_manifest', 'gbis_invalid_group' ), true );
		$error->add_data( array( 'status' => $remote ? 502 : 400 ) );
		return $error;
	}

	/**
	 * GET /manifest.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function manifest( WP_REST_Request $request ) {
		$manifest = GBIS_Source::get_manifest( (bool) $request->get_param( 'refresh' ) );
		if ( is_wp_error( $manifest ) ) {
			return self::error( $manifest );
		}
		return rest_ensure_response( GBIS_Library::decorate( $manifest ) );
	}

	/**
	 * POST /install.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function install( WP_REST_Request $request ) {
		$result = GBIS_Library::install( (string) $request->get_param( 'path' ), (bool) $request->get_param( 'replace' ) );
		if ( is_wp_error( $result ) ) {
			return self::error( $result );
		}
		return rest_ensure_response( $result );
	}

	/**
	 * POST /remove.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function remove( WP_REST_Request $request ) {
		$result = GBIS_Library::remove( (string) $request->get_param( 'group_id' ) );
		if ( is_wp_error( $result ) ) {
			return self::error( $result );
		}
		return rest_ensure_response( $result );
	}

	/**
	 * POST /settings.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return WP_REST_Response
	 */
	public static function settings( WP_REST_Request $request ) {
		$settings                      = gbis_get_settings();
		$settings['print_attribution'] = (bool) $request->get_param( 'print_attribution' );
		update_option( 'gbis_settings', $settings, false );
		return rest_ensure_response( $settings );
	}
}
