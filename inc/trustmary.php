<?php
/**
 * Trustmary reviews read on the server, so the reviews are part of the page text
 * (search engines and AI tools read them) and no Trustmary script runs in the browser.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Score and reviews of a Trustmary widget, cached for 12 hours. The last successful
 * copy is kept in an option, so a Trustmary outage never empties the page.
 *
 * @param string $widget_src Widget script address, https://widget.trustmary.com/<id>.
 * @return array|null array( 'average' => float, 'count' => int, 'sources' => string[], 'reviews' => array[] ), or null.
 */
function domio_trustmary_reviews( $widget_src ) {
	$key    = 'domio_trustmary_' . md5( $widget_src );
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached ? $cached : null;
	}

	$data = domio_trustmary_fetch( $widget_src );
	if ( $data ) {
		set_transient( $key, $data, 12 * HOUR_IN_SECONDS );
		update_option( $key, $data, false );
		return $data;
	}

	// Try again in an hour; until then use the last good copy.
	$last = get_option( $key );
	$last = is_array( $last ) ? $last : array();
	set_transient( $key, $last, HOUR_IN_SECONDS );
	return $last ? $last : null;
}

/**
 * Read the widgetData object that the Trustmary widget script carries.
 *
 * @param string $widget_src Widget script address.
 * @return array|null Normalised data, or null when the script could not be read.
 */
function domio_trustmary_fetch( $widget_src ) {
	$response = wp_remote_get( $widget_src, array( 'timeout' => 8 ) );
	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}

	// The object is printed on one line as "var widgetData = {...};".
	if ( ! preg_match( '/var\s+widgetData\s*=\s*(\{.*?\});\s*\n/s', wp_remote_retrieve_body( $response ), $match ) ) {
		return null;
	}

	$widget = json_decode( $match[1], true );
	if ( ! is_array( $widget ) || empty( $widget['testimonial']['items'] ) || ! is_array( $widget['testimonial']['items'] ) ) {
		return null;
	}

	$text = static function ( $item, $key ) {
		return isset( $item[ $key ] ) && is_scalar( $item[ $key ] ) ? trim( wp_strip_all_tags( (string) $item[ $key ] ) ) : '';
	};

	$reviews = array();
	foreach ( $widget['testimonial']['items'] as $item ) {
		if ( ! is_array( $item ) || '' === $text( $item, 'commentText' ) ) {
			continue; // A star rating without words says nothing on the page.
		}
		$reviews[] = array(
			'text'         => $text( $item, 'commentText' ),
			'name'         => $text( $item, 'personName' ),
			'organization' => $text( $item, 'personOrganization' ),
			'location'     => $text( $item, 'personLocation' ),
			'rating'       => isset( $item['reviewStarRating'] ) ? (float) $item['reviewStarRating'] : 0,
			'date'         => substr( $text( $item, 'createdAt' ), 0, 10 ),
			'source'       => $text( $item, 'source' ),
		);
	}

	$overall = isset( $widget['overallScore'] ) && is_array( $widget['overallScore'] ) ? $widget['overallScore'] : array();

	return array(
		'average' => isset( $overall['average'] ) ? round( (float) $overall['average'], 1 ) : 0,
		'count'   => isset( $overall['totalCount'] ) ? (int) $overall['totalCount'] : count( $reviews ),
		'sources' => isset( $overall['sources'] ) ? array_values( array_filter( (array) $overall['sources'], 'is_string' ) ) : array(),
		'reviews' => $reviews,
	);
}

/**
 * Display name of a review source.
 *
 * @param string $source Source key from Trustmary, e.g. "google".
 * @return string
 */
function domio_trustmary_source_label( $source ) {
	$labels = array(
		'google'    => 'Google',
		'trustmary' => 'Trustmary',
		'facebook'  => 'Facebook',
	);
	return isset( $labels[ $source ] ) ? $labels[ $source ] : ucfirst( $source );
}
