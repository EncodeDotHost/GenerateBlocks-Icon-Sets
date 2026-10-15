<?php
/**
 * Offers plugin updates from GitHub Releases.
 *
 * The plugin header sets "Update URI: https://github.com/...", so WordPress
 * (5.8+) skips wordpress.org for this plugin and asks the
 * update_plugins_github.com filter instead.
 *
 * @package Icon_Sets_for_GenerateBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GitHub Releases updater.
 */
class GBIS_Updater {
	const TRANSIENT = 'gbis_release';
	const ASSET     = 'icon-sets-for-generateblocks.zip';

	/**
	 * Hook in.
	 */
	public static function init() {
		add_filter( 'update_plugins_github.com', array( __CLASS__, 'check' ), 10, 3 );
		add_filter( 'plugins_api', array( __CLASS__, 'info' ), 20, 3 );
		add_action( 'upgrader_process_complete', array( __CLASS__, 'clear' ), 10, 0 );
	}

	/**
	 * Plugin basename.
	 *
	 * @return string
	 */
	private static function basename() {
		return plugin_basename( GBIS_FILE );
	}

	/**
	 * Drop the cached release.
	 */
	public static function clear() {
		delete_transient( self::TRANSIENT );
	}

	/**
	 * Latest published release that carries the plugin zip.
	 *
	 * @param bool $force Skip the cache.
	 * @return array|WP_Error { version, package, url, body, published }
	 */
	public static function latest( $force = false ) {
		if ( ! $force ) {
			$cached = get_transient( self::TRANSIENT );
			if ( is_array( $cached ) ) {
				return $cached;
			}
		}

		$url      = 'https://api.github.com/repos/' . GBIS_REPO . '/releases/latest';
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 10,
				'headers' => array(
					'Accept'               => 'application/vnd.github+json',
					'X-GitHub-Api-Version' => '2022-11-28',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			$error = new WP_Error( 'gbis_release_http', sprintf( 'GitHub API answered with HTTP %d for %s.', $code, $url ) );
			// Cache "no release" for an hour so the API rate limit is not used up.
			set_transient( self::TRANSIENT, array( 'version' => '' ), HOUR_IN_SECONDS );
			return $error;
		}

		$data    = json_decode( wp_remote_retrieve_body( $response ), true );
		$release = array( 'version' => '' );

		if ( is_array( $data ) && ! empty( $data['tag_name'] ) && ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
			foreach ( $data['assets'] as $asset ) {
				if ( isset( $asset['name'], $asset['browser_download_url'] ) && self::ASSET === $asset['name'] ) {
					$release = array(
						'version'   => ltrim( (string) $data['tag_name'], 'vV' ),
						'package'   => esc_url_raw( $asset['browser_download_url'] ),
						'url'       => isset( $data['html_url'] ) ? esc_url_raw( $data['html_url'] ) : '',
						'body'      => isset( $data['body'] ) ? (string) $data['body'] : '',
						'published' => isset( $data['published_at'] ) ? (string) $data['published_at'] : '',
					);
					break;
				}
			}
		}

		set_transient( self::TRANSIENT, $release, 12 * HOUR_IN_SECONDS );
		return $release;
	}

	/**
	 * Filter update_plugins_github.com.
	 *
	 * @param array|false $update      Update data from earlier filters.
	 * @param array       $plugin_data Plugin header data.
	 * @param string      $plugin_file Plugin basename.
	 * @return array|false
	 */
	public static function check( $update, $plugin_data, $plugin_file ) {
		if ( self::basename() !== $plugin_file ) {
			return $update;
		}

		$release = self::latest();
		if ( is_wp_error( $release ) || empty( $release['version'] ) ) {
			return $update;
		}

		return array(
			'id'           => $plugin_data['UpdateURI'],
			'slug'         => GBIS_SLUG,
			'version'      => $release['version'],
			'url'          => $release['url'],
			'package'      => $release['package'],
			'requires'     => '6.7',
			'requires_php' => '7.4',
		);
	}

	/**
	 * Filter plugins_api for the "View details" modal.
	 *
	 * @param false|object|array $result Result.
	 * @param string             $action Action.
	 * @param object             $args   Arguments.
	 * @return false|object|array
	 */
	public static function info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || ! isset( $args->slug ) || GBIS_SLUG !== $args->slug ) {
			return $result;
		}

		$release = self::latest();
		if ( is_wp_error( $release ) || empty( $release['version'] ) ) {
			return $result;
		}

		return (object) array(
			'name'          => 'Icon Sets for GenerateBlocks',
			'slug'          => GBIS_SLUG,
			'version'       => $release['version'],
			'author'        => '<a href="https://github.com/EncodeDotHost">EncodeDotHost</a>',
			'homepage'      => 'https://github.com/' . GBIS_REPO,
			'requires'      => '6.7',
			'requires_php'  => '7.4',
			'last_updated'  => $release['published'],
			'download_link' => $release['package'],
			'sections'      => array(
				'changelog' => wpautop( esc_html( $release['body'] ) ),
			),
		);
	}
}
