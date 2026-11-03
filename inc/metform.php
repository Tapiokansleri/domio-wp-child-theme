<?php
/**
 * MetForm (the contact and quote forms) on Domio pages.
 *
 * Found 2026-11-03: a visitor coming from the price calculator gets the message prefilled, types the e-mail
 * last and presses "Lähetä lomake". MetForm still shows the e-mail field's stale error line until the field
 * loses focus; the press blurs it, the line disappears, the button jumps up and the click is lost, so the
 * calculator lead is never sent and nothing tells the visitor. Enter in the field fails too.
 * assets/js/domio-metform-fix.js lets the field validate first and then presses the button once.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the submit fix wherever MetForm is active.
 *
 * @return void
 */
function domio_metform_fix_enqueue() {
	// The same fix shipped first as mu-plugin domio-metform-fix.php; never run both.
	if ( is_admin() || ! class_exists( '\MetForm\Plugin' ) || defined( 'DOMIO_METFORM_FIX_MU' ) ) {
		return;
	}
	wp_enqueue_script( 'domio-metform-fix', DOMIO_THEME_URI . '/assets/js/domio-metform-fix.js', array(), DOMIO_THEME_VERSION, true );
	wp_script_add_data( 'domio-metform-fix', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'domio_metform_fix_enqueue', 30 );

/**
 * Keep the fix out of WP Rocket's delayed JavaScript, so it is listening before the first press.
 *
 * @param string[] $exclusions Exclusion patterns.
 * @return string[]
 */
function domio_rocket_exclude_metform_fix( $exclusions ) {
	$exclusions[] = 'domio-metform-fix.js';
	return $exclusions;
}
add_filter( 'rocket_delay_js_exclusions', 'domio_rocket_exclude_metform_fix' );
