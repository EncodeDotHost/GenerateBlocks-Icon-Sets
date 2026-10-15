<?php
/**
 * Reads and writes the GenerateBlocks Pro icon library option.
 *
 * GenerateBlocks Pro 2.8.0 keeps every icon group in the option
 * generateblocks_svg_icons (autoload off) as a list of
 * { group, group_id, icons: [ { id, name, icon } ] }. The id, group_id and
 * sanitising rules below copy includes/class-rest.php::update_icon_settings().
 *
 * @package Icon_Sets_for_GenerateBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon library access.
 */
class GBIS_Library {
	const OPTION    = 'generateblocks_svg_icons';
	const INSTALLED = 'gbis_installed';

	/**
	 * All icon groups in the Asset Library.
	 *
	 * @return array
	 */
	public static function get_groups() {
		$groups = get_option( self::OPTION, array() );
		return is_array( $groups ) ? array_values( $groups ) : array();
	}

	/**
	 * Groups this plugin installed, keyed by group_id.
	 *
	 * @return array
	 */
	public static function get_installed() {
		$records = get_option( self::INSTALLED, array() );
		return is_array( $records ) ? $records : array();
	}

	/**
	 * Group id the way GenerateBlocks Pro builds it.
	 *
	 * @param string $group Group name.
	 * @return string
	 */
	public static function group_id( $group ) {
		return str_replace( ' ', '-', strtolower( $group ) );
	}

	/**
	 * Map of group name => index in the option.
	 *
	 * @param array $groups Groups from get_groups().
	 * @return array
	 */
	private static function name_index( $groups ) {
		$map = array();
		foreach ( $groups as $i => $g ) {
			if ( isset( $g['group'] ) && is_string( $g['group'] ) ) {
				$map[ $g['group'] ] = $i;
			}
		}
		return $map;
	}

	/**
	 * Status of one manifest file.
	 *
	 * @param array $file      Manifest file from GBIS_Source::files().
	 * @param array $names     Name index from name_index().
	 * @param array $installed Records from get_installed().
	 * @return string not-installed | installed | changed | foreign
	 */
	private static function file_status( $file, $names, $installed ) {
		$gid    = self::group_id( $file['group'] );
		$exists = isset( $names[ $file['group'] ] );

		if ( isset( $installed[ $gid ] ) ) {
			if ( ! $exists ) {
				// Removed on the GenerateBlocks Pro page; the record is stale.
				return 'not-installed';
			}
			$rec = $installed[ $gid ];
			if ( ( isset( $rec['set_name'] ) && $rec['set_name'] !== $file['set_name'] ) || ( isset( $rec['count'] ) && (int) $rec['count'] !== (int) $file['count'] ) ) {
				return 'changed';
			}
			return 'installed';
		}

		return $exists ? 'foreign' : 'not-installed';
	}

	/**
	 * Add status and group_id to every file in the manifest.
	 *
	 * @param array $manifest The manifest.
	 * @return array
	 */
	public static function decorate( $manifest ) {
		$names     = self::name_index( self::get_groups() );
		$installed = self::get_installed();

		foreach ( $manifest['sets'] as &$set ) {
			foreach ( $set['styles'] as &$style ) {
				foreach ( $style['files'] as &$file ) {
					$file['set_name'] = $set['name'];
					$file['group_id'] = self::group_id( $file['group'] );
					$file['status']   = self::file_status( $file, $names, $installed );
					unset( $file['set_name'] );
				}
			}
		}
		unset( $set, $style, $file );

		return $manifest;
	}

	/**
	 * Status of one manifest file (fresh lookup).
	 *
	 * @param array $file Manifest file from GBIS_Source::files().
	 * @return string
	 */
	public static function status( $file ) {
		return self::file_status( $file, self::name_index( self::get_groups() ), self::get_installed() );
	}

	/**
	 * Sanitise an SVG the way GenerateBlocks Pro does on save.
	 *
	 * @param string $svg SVG markup.
	 * @return string
	 */
	public static function sanitize_svg( $svg ) {
		if ( current_user_can( 'unfiltered_html' ) ) {
			return $svg;
		}
		return wp_kses( $svg, generateblocks_pro_kses_svg() );
	}

	/**
	 * Build one option entry from a group file.
	 *
	 * @param string $group  Group name.
	 * @param array  $assets List of { name, icon }.
	 * @return array
	 */
	public static function build_entry( $group, $assets ) {
		$group = sanitize_text_field( $group );
		$icons = array();

		foreach ( $assets as $asset ) {
			$name    = sanitize_text_field( $asset['name'] );
			$icons[] = array(
				'id'   => sanitize_key( str_replace( ' ', '-', strtolower( $name ) ) . '-' . uniqid() ),
				'name' => $name,
				'icon' => self::sanitize_svg( $asset['icon'] ),
			);
		}

		return array(
			'group'    => $group,
			'group_id' => self::group_id( $group ),
			'icons'    => $icons,
		);
	}

	/**
	 * Install or reinstall one group from the repository.
	 *
	 * @param string $path    Repository-relative path from the manifest.
	 * @param bool   $replace Take over a group of the same name that this plugin did not install.
	 * @return array|WP_Error { group, group_id, count, status, replaced }
	 */
	public static function install( $path, $replace = false ) {
		$file = GBIS_Source::find_file( $path );
		if ( is_wp_error( $file ) ) {
			return $file;
		}

		$data = GBIS_Source::fetch_group( $path );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$entry     = self::build_entry( $data['group'], $data['assets'] );
		$gid       = $entry['group_id'];
		$groups    = self::get_groups();
		$installed = self::get_installed();
		$names     = self::name_index( $groups );
		$index     = isset( $names[ $entry['group'] ] ) ? $names[ $entry['group'] ] : null;

		if ( null !== $index && ! isset( $installed[ $gid ] ) && ! $replace ) {
			return new WP_Error(
				'gbis_exists',
				sprintf(
					/* translators: %s: group name */
					__( 'A group named "%s" already exists in the Asset Library and was not installed by this plugin. Use replace to overwrite it.', 'generateblocks-icon-sets' ),
					$entry['group']
				)
			);
		}

		if ( null === $index ) {
			$groups[] = $entry;
		} else {
			$groups[ $index ] = $entry;
		}

		update_option( self::OPTION, array_values( $groups ), false );

		$installed[ $gid ] = array(
			'path'         => $path,
			'set_folder'   => $file['set_folder'],
			'set_name'     => $file['set_name'],
			'style'        => $file['style'],
			'group'        => $entry['group'],
			'count'        => count( $entry['icons'] ),
			'md5'          => $data['md5'],
			'attribution'  => $file['attribution'],
			'installed_at' => time(),
		);
		update_option( self::INSTALLED, $installed, false );

		return array(
			'group'    => $entry['group'],
			'group_id' => $gid,
			'count'    => count( $entry['icons'] ),
			'status'   => 'installed',
			'replaced' => null !== $index,
		);
	}

	/**
	 * Remove a group this plugin installed.
	 *
	 * @param string $gid Group id.
	 * @return array|WP_Error { group, group_id, status }
	 */
	public static function remove( $gid ) {
		$installed = self::get_installed();
		if ( ! isset( $installed[ $gid ] ) ) {
			return new WP_Error(
				'gbis_not_owned',
				sprintf(
					/* translators: %s: group id */
					__( '"%s" was not installed by this plugin, so it stays untouched. Remove it on the GenerateBlocks Pro Asset Library page.', 'generateblocks-icon-sets' ),
					$gid
				)
			);
		}

		$group  = $installed[ $gid ]['group'];
		$groups = self::get_groups();
		$names  = self::name_index( $groups );

		if ( isset( $names[ $group ] ) ) {
			unset( $groups[ $names[ $group ] ] );
			update_option( self::OPTION, array_values( $groups ), false );
		}

		unset( $installed[ $gid ] );
		update_option( self::INSTALLED, $installed, false );

		return array(
			'group'    => $group,
			'group_id' => $gid,
			'status'   => 'not-installed',
		);
	}
}
