<?php
/**
 * Google Maps Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Google Map
 *
 * Only latitude and longitude are required.
 *
 * @since 0.9
 * @param array $options Options for showing map
 * @return string Google Maps HTML
 */
function ctfw_google_map( $options = false ) {

	$html = '';
	
	if ( ! empty( $options['latitude'] ) && ! empty( $options['longitude'] ) ) {

		// Enqueue map scripts to handle Google Maps init
		// this way the scripts are loaded only when feature is used, not on every page
		wp_enqueue_script( 'google-maps', ctfw_current_protocol() . '://maps.googleapis.com/maps/api/js?sensor=false', false, null ); // no version, generic name to share w/plugins
		wp_enqueue_script( 'ctfw-maps', ctfw_theme_url( CTFW_JS_DIR . '/maps.js' ), array( 'jquery', 'google-maps' ), CTFW_VERSION ); // bust cache on theme update

		// Pass location of map icons to JS
		wp_localize_script( 'ctfw-maps', 'ctfw_maps', array(
			'icon'			=> ctfw_color_url( apply_filters( 'ctfw_maps_icon_color_file', 'images/map-icon.png' ) ),
			'icon_shadow'	=> ctfw_color_url( apply_filters( 'ctfw_maps_icon_shadow_color_file', 'images/map-icon-shadow.png' ) ),
		));

		// Type and zoom are optional
		$options['type'] = isset( $options['type'] ) ? strtoupper( $options['type'] ) : '';
		$options['zoom'] = isset( $options['zoom'] ) ? (int) $options['zoom'] : '';

		// Height percentage of width?
		$map_style = '';
		if ( ! empty( $options['height_percent'] ) ) {
			$options['height_percent'] = str_replace( '%', '', $options['height_percent'] );
			$map_style = ' style="padding-bottom: ' . $options['height_percent'] . '%;"';
		}

		// Unique ID for this map so can have multiple maps on a page
		$google_map_id_num = rand( 1000000, 9999999 );
		$google_map_id = 'ctfw-google-map-' . $google_map_id_num;

		// Data Attributes
		$data_latitude = esc_attr( $options['latitude'] );
		$data_longitude = esc_attr( $options['longitude'] );
		$data_type = esc_attr( $options['type'] );
		$data_zoom = esc_attr( $options['zoom'] );

$html = <<< HTML
<div class="ctfw-google-map-container">
	<div id="$google_map_id" class="ctfw-google-map" data-ctfw-map-lat="$data_latitude" data-ctfw-map-lng="$data_longitude" data-ctfw-map-type="$data_type" data-ctfw-map-zoom="$data_zoom"$map_style></div>
</div>
HTML;

	} else if ( ! empty( $options['show_error'] ) ) {
		$html = __( '<p><b>Google Map Error:</b> <i>latitude</i> and <i>longitude</i> attributes are required. See documentation for help.</p>', 'church-theme-framework' );
	}

	return apply_filters( 'ctfw_google_map', $html, $options );

}

/**
 * Build Google Maps directions URL from address
 *
 * @since 0.9
 * @param string $address Address to get directions URL for
 * @return string URL for directions on Google Maps
 */
function ctfw_directions_url( $address ) {

	$directions_url = '';

	if ( $address ) {

		// Convert address to one line (replace newlines with commas)
		$directions_address = ctfw_address_one_line( $address );

		// Build URL to Google Maps
		$directions_url = ctfw_current_protocol() . '://maps.google.com/maps?f=d&q=' . urlencode( $directions_address );

	}

	return apply_filters( 'ctfw_directions_url', $directions_url, $address );

}
