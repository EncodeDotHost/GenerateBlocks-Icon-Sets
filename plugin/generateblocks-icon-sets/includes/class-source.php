<?php
/**
 * Fetches the gallery manifest and group files from GitHub.
 *
 * @package Icon_Sets_for_GenerateBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Remote data source.
 */
class GBIS_Source {
	const MANIFEST_TRANSIENT = 'gbis_manifest';
	const MANIFEST_TTL       = 12 * HOUR_IN_SECONDS;

	/**
	 * Base URL for raw files in the repository.
	 *
	 * @return string
	 */
	public static function raw_base() {
		return 'https://raw.githubusercontent.com/' . GBIS_REPO . '/' . GBIS_BRANCH . '/';
	}

	/**
	 * URL of the gallery manifest.
	 *
	 * @return string
	 */
	public static function manifest_url() {
		return self::raw_base() . 'docs/sets.json';
	}

	/**
	 * URL of one group file. Each path segment is encoded on its own because
	 * the paths contain spaces, ampersands and parentheses.
	 *
	 * @param string $path Repository-relative path from the manifest.
	 * @return string
	 */
	public static function group_url( $path ) {
		$parts = array_map( 'rawurlencode', explode( '/', $path ) );
		return self::raw_base() . implode( '/', $parts );
	}

	/**
	 * Return the manifest, from the transient cache unless forced.
	 *
	 * @param bool $force Fetch again even if cached.
	 * @return array|WP_Error
	 */
	public static function get_manifest( $force = false ) {
		if ( ! $force ) {
			$cached = get_transient( self::MANIFEST_TRANSIENT );
			if ( is_array( $cached ) ) {
				return $cached;
			}
		}

		$response = wp_remote_get(
			self::manifest_url(),
			array(
				'timeout' => 15,
				'headers' => array( 'Accept' => 'application/json' ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $code ) {
			return new WP_Error(
				'gbis_http',
				sprintf(
					/* translators: 1: HTTP status code, 2: URL */
					__( 'GitHub answered with HTTP %1$d for %2$s.', 'generateblocks-icon-sets' ),
					$code,
					self::manifest_url()
				)
			);
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		$data = self::validate_manifest( $data );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$data['fetched_at'] = time();
		set_transient( self::MANIFEST_TRANSIENT, $data, self::MANIFEST_TTL );

		return $data;
	}

	/**
	 * Drop the cached manifest.
	 */
	public static function clear_cache() {
		delete_transient( self::MANIFEST_TRANSIENT );
	}

	/**
	 * Check the manifest structure and keep only the fields the plugin uses.
	 *
	 * @param mixed $data Decoded JSON.
	 * @return array|WP_Error
	 */
	private static function validate_manifest( $data ) {
		$invalid = new WP_Error( 'gbis_invalid_manifest', __( 'The manifest from GitHub is not in the expected format.', 'generateblocks-icon-sets' ) );

		if ( ! is_array( $data ) || empty( $data['sets'] ) || ! is_array( $data['sets'] ) ) {
			return $invalid;
		}

		$clean = array(
			'repo'   => isset( $data['repo'] ) ? sanitize_text_field( $data['repo'] ) : GBIS_REPO,
			'branch' => isset( $data['branch'] ) ? sanitize_text_field( $data['branch'] ) : GBIS_BRANCH,
			'total'  => isset( $data['total'] ) ? (int) $data['total'] : 0,
			'sets'   => array(),
		);

		foreach ( $data['sets'] as $set ) {
			if ( ! is_array( $set ) || empty( $set['folder'] ) || empty( $set['name'] ) || ! isset( $set['styles'] ) || ! is_array( $set['styles'] ) ) {
				return $invalid;
			}

			$clean_set = array(
				'folder'      => sanitize_text_field( $set['folder'] ),
				'name'        => sanitize_text_field( $set['name'] ),
				'author'      => isset( $set['author'] ) ? sanitize_text_field( $set['author'] ) : '',
				'license'     => isset( $set['license'] ) ? sanitize_text_field( $set['license'] ) : '',
				'licenseUrl'  => isset( $set['licenseUrl'] ) ? esc_url_raw( $set['licenseUrl'] ) : '',
				'attribution' => isset( $set['attribution'] ) ? sanitize_text_field( $set['attribution'] ) : '',
				'note'        => isset( $set['note'] ) ? sanitize_text_field( $set['note'] ) : '',
				'readme'      => isset( $set['readme'] ) ? sanitize_text_field( $set['readme'] ) : '',
				'links'       => array(),
				'styles'      => array(),
			);

			if ( ! empty( $set['links'] ) && is_array( $set['links'] ) ) {
				foreach ( $set['links'] as $link ) {
					if ( is_array( $link ) && ! empty( $link['label'] ) && ! empty( $link['url'] ) ) {
						$clean_set['links'][] = array(
							'label' => sanitize_text_field( $link['label'] ),
							'url'   => esc_url_raw( $link['url'] ),
						);
					}
				}
			}

			foreach ( $set['styles'] as $style ) {
				if ( ! is_array( $style ) || ! isset( $style['name'] ) || ! isset( $style['files'] ) || ! is_array( $style['files'] ) ) {
					return $invalid;
				}

				$clean_style = array(
					'name'   => sanitize_text_field( $style['name'] ),
					'folder' => isset( $style['folder'] ) ? sanitize_text_field( $style['folder'] ) : '',
					'count'  => isset( $style['count'] ) ? (int) $style['count'] : 0,
					'files'  => array(),
				);

				foreach ( $style['files'] as $file ) {
					if ( ! is_array( $file ) || empty( $file['path'] ) || empty( $file['group'] ) || ! is_string( $file['path'] ) || ! is_string( $file['group'] ) ) {
						return $invalid;
					}
					if ( false !== strpos( $file['path'], '..' ) || '/' === substr( $file['path'], 0, 1 ) ) {
						return $invalid;
					}
					$clean_style['files'][] = array(
						'path'  => $file['path'],
						'group' => sanitize_text_field( $file['group'] ),
						'count' => isset( $file['count'] ) ? (int) $file['count'] : 0,
					);
				}

				$clean_set['styles'][] = $clean_style;
			}

			$clean['sets'][] = $clean_set;
		}

		return $clean;
	}

	/**
	 * Flat list of every group file in the manifest, keyed by path.
	 *
	 * @param array $manifest The manifest.
	 * @return array path => { path, group, count, set_folder, set_name, style, style_name, attribution }
	 */
	public static function files( $manifest ) {
		$out = array();
		foreach ( $manifest['sets'] as $set ) {
			foreach ( $set['styles'] as $style ) {
				foreach ( $style['files'] as $file ) {
					$out[ $file['path'] ] = array(
						'path'        => $file['path'],
						'group'       => $file['group'],
						'count'       => $file['count'],
						'set_folder'  => $set['folder'],
						'set_name'    => $set['name'],
						'style'       => $style['folder'],
						'style_name'  => $style['name'],
						'attribution' => $set['attribution'],
					);
				}
			}
		}
		return $out;
	}

	/**
	 * Look up one manifest file by path.
	 *
	 * @param string $path  Repository-relative path.
	 * @param bool   $force Refetch the manifest first.
	 * @return array|WP_Error
	 */
	public static function find_file( $path, $force = false ) {
		$manifest = self::get_manifest( $force );
		if ( is_wp_error( $manifest ) ) {
			return $manifest;
		}
		$files = self::files( $manifest );
		if ( ! isset( $files[ $path ] ) ) {
			return new WP_Error(
				'gbis_unknown_path',
				sprintf(
					/* translators: %s: file path */
					__( '%s is not a group file listed in the manifest. Refresh the manifest and try again.', 'generateblocks-icon-sets' ),
					$path
				)
			);
		}
		return $files[ $path ];
	}

	/**
	 * Download and validate one group file.
	 *
	 * @param string $path Repository-relative path, must be in the manifest.
	 * @return array|WP_Error { group, assets, md5 }
	 */
	public static function fetch_group( $path ) {
		$file = self::find_file( $path );
		if ( is_wp_error( $file ) ) {
			return $file;
		}

		$url      = self::group_url( $path );
		$response = wp_remote_get( $url, array( 'timeout' => 30 ) );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $code ) {
			return new WP_Error(
				'gbis_http',
				sprintf(
					/* translators: 1: HTTP status code, 2: URL */
					__( 'GitHub answered with HTTP %1$d for %2$s.', 'generateblocks-icon-sets' ),
					$code,
					$url
				)
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		$invalid = new WP_Error(
			'gbis_invalid_group',
			sprintf(
				/* translators: %s: file path */
				__( '%s is not a valid icon group file.', 'generateblocks-icon-sets' ),
				$path
			)
		);

		if ( ! is_array( $data ) || ! isset( $data['type'], $data['group'], $data['assets'] ) ) {
			return $invalid;
		}
		if ( 'icons' !== $data['type'] || ! is_string( $data['group'] ) || '' === trim( $data['group'] ) || ! is_array( $data['assets'] ) ) {
			return $invalid;
		}
		if ( $data['group'] !== $file['group'] ) {
			return new WP_Error(
				'gbis_group_mismatch',
				sprintf(
					/* translators: 1: group name in the file, 2: group name in the manifest */
					__( 'The file names its group "%1$s" but the manifest says "%2$s". Refresh the manifest and try again.', 'generateblocks-icon-sets' ),
					$data['group'],
					$file['group']
				)
			);
		}

		$assets = array();
		foreach ( $data['assets'] as $asset ) {
			// Names can be "0" (Font Awesome Numbers), so empty() is not usable here.
			if ( ! is_array( $asset ) || ! isset( $asset['name'], $asset['icon'] ) || ! is_string( $asset['name'] ) || ! is_string( $asset['icon'] ) || '' === trim( $asset['name'] ) ) {
				return $invalid;
			}
			if ( 0 !== strpos( ltrim( $asset['icon'] ), '<svg' ) ) {
				return $invalid;
			}
			$assets[] = array(
				'name' => $asset['name'],
				'icon' => $asset['icon'],
			);
		}

		if ( empty( $assets ) ) {
			return $invalid;
		}

		return array(
			'group'  => $data['group'],
			'assets' => $assets,
			'md5'    => md5( $body ),
		);
	}
}
