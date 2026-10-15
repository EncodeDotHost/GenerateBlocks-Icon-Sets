<?php
/**
 * WP-CLI command: wp gb-icon-sets.
 *
 * @package Icon_Sets_for_GenerateBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Install icon groups from the Icon Sets for GenerateBlocks repository into the
 * GenerateBlocks Pro Asset Library.
 *
 * Run as an administrator (--user=<admin>) so the SVGs are stored unchanged.
 * Without a user, WP-CLI has no unfiltered_html capability and GenerateBlocks
 * Pro's SVG allowlist is applied, as for a non-admin import.
 */
class GBIS_CLI {

	/**
	 * Load the manifest or stop.
	 *
	 * @param bool $force Refetch.
	 * @return array
	 */
	private function manifest( $force = false ) {
		$manifest = GBIS_Source::get_manifest( $force );
		if ( is_wp_error( $manifest ) ) {
			WP_CLI::error( $manifest );
		}
		return $manifest;
	}

	/**
	 * Manifest files filtered by --set and --style, with status.
	 *
	 * @param array $assoc_args Arguments.
	 * @return array
	 */
	private function filtered_files( $assoc_args ) {
		$files = GBIS_Source::files( $this->manifest() );
		$set   = isset( $assoc_args['set'] ) ? strtolower( $assoc_args['set'] ) : null;
		$style = isset( $assoc_args['style'] ) ? strtolower( $assoc_args['style'] ) : null;
		$out   = array();

		foreach ( $files as $file ) {
			if ( null !== $set && strtolower( $file['set_folder'] ) !== $set ) {
				continue;
			}
			if ( null !== $style && strtolower( $file['style'] ) !== $style ) {
				continue;
			}
			$file['status']   = GBIS_Library::status( $file );
			$file['group_id'] = GBIS_Library::group_id( $file['group'] );
			$out[]            = $file;
		}
		return $out;
	}

	/**
	 * Warn when the SVG allowlist will be applied.
	 */
	private function kses_notice() {
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			WP_CLI::warning( 'No user with unfiltered_html is set, so the GenerateBlocks Pro SVG allowlist is applied and preserveAspectRatio is removed. Add --user=<admin> to store the SVGs unchanged.' );
		}
	}

	/**
	 * Lists the group files in the repository and their status on this site.
	 *
	 * ## OPTIONS
	 *
	 * [--set=<folder>]
	 * : Only this set, by folder name (for example Tabler, Fontawesome).
	 *
	 * [--style=<folder>]
	 * : Only this style, by folder name (for example Outline, Solid).
	 *
	 * [--status=<status>]
	 * : Only this status.
	 * ---
	 * options:
	 *   - not-installed
	 *   - installed
	 *   - changed
	 *   - foreign
	 * ---
	 *
	 * [--format=<format>]
	 * : Output format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - json
	 *   - csv
	 *   - count
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp gb-icon-sets list --set=Tabler --style=Outline
	 *     wp gb-icon-sets list --status=installed
	 *
	 * @subcommand list
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function list_( $args, $assoc_args ) {
		$files = $this->filtered_files( $assoc_args );
		if ( isset( $assoc_args['status'] ) ) {
			$want  = $assoc_args['status'];
			$files = array_filter(
				$files,
				function ( $f ) use ( $want ) {
					return $f['status'] === $want;
				}
			);
		}
		$rows = array_map(
			function ( $f ) {
				return array(
					'set'    => $f['set_folder'],
					'style'  => $f['style'],
					'group'  => $f['group'],
					'count'  => $f['count'],
					'status' => $f['status'],
					'path'   => $f['path'],
				);
			},
			$files
		);
		WP_CLI\Utils\format_items( isset( $assoc_args['format'] ) ? $assoc_args['format'] : 'table', $rows, array( 'set', 'style', 'group', 'count', 'status', 'path' ) );
	}

	/**
	 * Installs group files into the Asset Library.
	 *
	 * Groups that are already installed by this plugin are reinstalled.
	 * Groups with the same name that this plugin did not install are skipped
	 * unless --replace is given.
	 *
	 * ## OPTIONS
	 *
	 * [<path>...]
	 * : Repository paths, as shown by the list command.
	 *
	 * [--set=<folder>]
	 * : Install every group in this set (with --style, only that style).
	 *
	 * [--style=<folder>]
	 * : Limit --set to this style.
	 *
	 * [--all]
	 * : Install every group in the repository.
	 *
	 * [--replace]
	 * : Replace groups with the same name that this plugin did not install.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gb-icon-sets install "Tabler/Outline/group-Tabler Icons (Animals) (Outline).json" --user=admin
	 *     wp gb-icon-sets install --set=Heroicons --user=admin
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function install( $args, $assoc_args ) {
		$replace = WP_CLI\Utils\get_flag_value( $assoc_args, 'replace', false );

		if ( $args ) {
			$paths = $args;
		} elseif ( isset( $assoc_args['set'] ) || WP_CLI\Utils\get_flag_value( $assoc_args, 'all', false ) ) {
			$paths = wp_list_pluck( $this->filtered_files( $assoc_args ), 'path' );
		} else {
			WP_CLI::error( 'Give one or more paths, --set=<folder> or --all.' );
		}

		if ( ! $paths ) {
			WP_CLI::error( 'No group files match.' );
		}

		$this->kses_notice();
		$this->run_installs( $paths, $replace );
	}

	/**
	 * Install a list of paths and report.
	 *
	 * @param string[] $paths   Paths.
	 * @param bool     $replace Replace foreign groups.
	 */
	private function run_installs( $paths, $replace ) {
		$ok   = 0;
		$fail = 0;
		foreach ( $paths as $path ) {
			$result = GBIS_Library::install( $path, $replace );
			if ( is_wp_error( $result ) ) {
				++$fail;
				WP_CLI::warning( $path . ': ' . $result->get_error_message() );
				continue;
			}
			++$ok;
			WP_CLI::log( sprintf( '%s %s (%d icons)', $result['replaced'] ? 'Replaced' : 'Installed', $result['group'], $result['count'] ) );
		}
		$msg = sprintf( '%d installed, %d failed.', $ok, $fail );
		if ( $fail ) {
			WP_CLI::error( $msg );
		}
		WP_CLI::success( $msg );
	}

	/**
	 * Reinstalls groups this plugin installed, from the current repository files.
	 *
	 * ## OPTIONS
	 *
	 * [--set=<folder>]
	 * : Only groups from this set.
	 *
	 * [--changed]
	 * : Only groups whose set version or icon count changed in the repository.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gb-icon-sets update --user=admin
	 *     wp gb-icon-sets update --set=Tabler --changed --user=admin
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function update( $args, $assoc_args ) {
		$only_changed = WP_CLI\Utils\get_flag_value( $assoc_args, 'changed', false );
		$set          = isset( $assoc_args['set'] ) ? strtolower( $assoc_args['set'] ) : null;
		$files        = GBIS_Source::files( $this->manifest() );
		$by_group     = array();
		foreach ( $files as $file ) {
			$by_group[ $file['group'] ] = $file;
		}

		$paths = array();
		foreach ( GBIS_Library::get_installed() as $record ) {
			if ( null !== $set && strtolower( $record['set_folder'] ) !== $set ) {
				continue;
			}
			// Prefer the recorded path; fall back to the group name if the file moved.
			if ( isset( $files[ $record['path'] ] ) ) {
				$file = $files[ $record['path'] ];
			} elseif ( isset( $by_group[ $record['group'] ] ) ) {
				$file = $by_group[ $record['group'] ];
			} else {
				WP_CLI::warning( $record['group'] . ' is no longer in the repository. Skipped.' );
				continue;
			}
			if ( $only_changed && 'changed' !== GBIS_Library::status( $file ) ) {
				continue;
			}
			$paths[] = $file['path'];
		}

		if ( ! $paths ) {
			WP_CLI::success( 'Nothing to update.' );
			return;
		}

		$this->kses_notice();
		$this->run_installs( $paths, false );
	}

	/**
	 * Removes groups this plugin installed.
	 *
	 * ## OPTIONS
	 *
	 * [<group_id>...]
	 * : Group ids, for example tabler-icons-(animals)-(outline).
	 *
	 * [--set=<folder>]
	 * : Remove every installed group from this set.
	 *
	 * [--all]
	 * : Remove every group this plugin installed.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gb-icon-sets remove --set=Heroicons
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function remove( $args, $assoc_args ) {
		$installed = GBIS_Library::get_installed();

		if ( $args ) {
			$ids = $args;
		} elseif ( isset( $assoc_args['set'] ) ) {
			$set = strtolower( $assoc_args['set'] );
			$ids = array();
			foreach ( $installed as $gid => $record ) {
				if ( strtolower( $record['set_folder'] ) === $set ) {
					$ids[] = $gid;
				}
			}
		} elseif ( WP_CLI\Utils\get_flag_value( $assoc_args, 'all', false ) ) {
			$ids = array_keys( $installed );
		} else {
			WP_CLI::error( 'Give one or more group ids, --set=<folder> or --all.' );
		}

		if ( ! $ids ) {
			WP_CLI::success( 'Nothing to remove.' );
			return;
		}

		$fail = 0;
		foreach ( $ids as $gid ) {
			$result = GBIS_Library::remove( $gid );
			if ( is_wp_error( $result ) ) {
				++$fail;
				WP_CLI::warning( $result->get_error_message() );
				continue;
			}
			WP_CLI::log( 'Removed ' . $result['group'] );
		}
		$msg = sprintf( '%d removed, %d failed.', count( $ids ) - $fail, $fail );
		if ( $fail ) {
			WP_CLI::error( $msg );
		}
		WP_CLI::success( $msg );
	}

	/**
	 * Fetches the manifest from GitHub again.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gb-icon-sets refresh
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function refresh( $args, $assoc_args ) {
		$manifest = $this->manifest( true );
		WP_CLI::success( sprintf( 'Manifest loaded: %d sets, %d group files, %d icons.', count( $manifest['sets'] ), count( GBIS_Source::files( $manifest ) ), $manifest['total'] ) );
	}
}
