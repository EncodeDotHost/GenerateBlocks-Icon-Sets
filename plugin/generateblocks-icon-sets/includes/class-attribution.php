<?php
/**
 * Prints the attribution text that some icon licenses require.
 *
 * @package GenerateBlocks_Icon_Sets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end attribution output.
 */
class GBIS_Attribution {
	/**
	 * Hook in.
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'output' ), 99 );
	}

	/**
	 * Distinct attribution texts for the installed sets.
	 *
	 * The text is saved with each installed group, so the front end never
	 * calls GitHub and never loads the large icon option.
	 *
	 * @return string[]
	 */
	public static function texts() {
		$texts = array();
		foreach ( GBIS_Library::get_installed() as $record ) {
			if ( ! empty( $record['attribution'] ) ) {
				$texts[ $record['attribution'] ] = true;
			}
		}
		return array_keys( $texts );
	}

	/**
	 * Output the attribution as a comment and as a meta tag in the head.
	 */
	public static function output() {
		$settings = gbis_get_settings();
		if ( empty( $settings['print_attribution'] ) ) {
			return;
		}

		foreach ( self::texts() as $text ) {
			// "--" would end the HTML comment early.
			echo "\n<!-- " . esc_html( str_replace( '--', '- -', $text ) ) . " -->\n";
			echo '<meta name="icon-attribution" content="' . esc_attr( $text ) . '">' . "\n";
		}
	}
}
