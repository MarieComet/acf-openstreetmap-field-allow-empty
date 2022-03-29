<?php
/**
 * Plugin Name:       ACF Open Street Map Field : Allow empty
 * Description:       Disable default map markers (lat/lng) saved when there is no markers on map
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Marie Comet
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'plugins_loaded', 'acf_osm_allow_empty_load' );
function acf_osm_allow_empty_load() {
    if ( ! class_exists( 'acf' ) ) {
        return;
    }
    add_filter('acf/update_value/type=open_street_map', 'acf_osm_delete_empty_markers', 15, 4);
}

if ( ! function_exists( 'acf_osm_delete_empty_markers' ) ) {
    /**
     * Delete field values and don't save field if markers address are empty.
     * Hooked on acf/update_value
     * 
     *  @param	$value (mixed) the value found in the database
	 *  @param	$post_id (mixed) the $post_id from which the value was loaded
	 *  @param	$field (array) the field array holding all the field options
	 *  @return	$value
     */
    function acf_osm_delete_empty_markers( $value, $post_id, $field, $original ) {
        $address = $value;
        if ( is_string( $value ) ) {
			$address = json_decode( stripslashes( $value ), true );
		}
        if ( is_array( $address ) ) {
            // if there is no markers, delete field values and don't return value (prevent field creation)
            if ( isset( $address[ 'markers' ] ) && empty( $address[ 'markers' ] ) || ! isset ( $address[ 'markers' ] ) ) {
                delete_field( $field[ 'key' ], $post_id );
                return;
            }
        }
        return $value;
    }
}