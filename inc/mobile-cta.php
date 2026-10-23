<?php
/**
 * Floating "Laske hinta-arvio" button on phones, on every page except the price calculator page.
 *
 * On a page that embeds the calculator block the button scrolls to it; elsewhere it opens
 * /hintalaskuri/. It hides while the calculator or the quote form is on screen.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the floating button is shown on this request.
 *
 * @return bool
 */
function domio_mobile_cta_enabled() {
	if ( is_admin() || is_feed() || is_embed() || wp_doing_ajax() || is_page( 'hintalaskuri' ) ) {
		return false;
	}
	// Elementor's editor preview.
	if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only check.
		return false;
	}
	return (bool) apply_filters( 'domio_mobile_cta_enabled', true );
}

/**
 * Enqueue the button's style and script.
 *
 * @return void
 */
function domio_mobile_cta_enqueue() {
	if ( ! domio_mobile_cta_enabled() ) {
		return;
	}
	wp_enqueue_style( 'domio-mobile-cta', DOMIO_THEME_URI . '/assets/css/domio-mobile-cta.css', array(), DOMIO_THEME_VERSION );
	wp_enqueue_script( 'domio-mobile-cta', DOMIO_THEME_URI . '/assets/js/domio-mobile-cta.js', array(), DOMIO_THEME_VERSION, true );
	wp_script_add_data( 'domio-mobile-cta', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'domio_mobile_cta_enqueue', 30 );

/**
 * Print the button in the footer.
 *
 * @return void
 */
function domio_mobile_cta_render() {
	if ( ! domio_mobile_cta_enabled() ) {
		return;
	}
	$has_calculator = is_singular() && has_block( 'domio/hintalaskuri', get_queried_object_id() );
	$url            = $has_calculator ? '#hintalaskuri' : home_url( '/hintalaskuri/' );
	?>
	<a class="domio-mobile-cta" href="<?php echo esc_url( $url ); ?>" data-domio-mobile-cta>
		<span class="domio-mobile-cta__label"><?php esc_html_e( 'Laske hinta-arvio', 'domio' ); ?></span>
		<span class="domio-mobile-cta__hint"><?php esc_html_e( '30 sekunnissa', 'domio' ); ?></span>
	</a>
	<?php
}
add_action( 'wp_footer', 'domio_mobile_cta_render', 5 );

/**
 * Keep the button script out of WP Rocket's delay, so it hides correctly from the first scroll.
 *
 * @param string[] $exclusions Exclusion patterns.
 * @return string[]
 */
function domio_rocket_exclude_mobile_cta_js( $exclusions ) {
	$exclusions[] = 'domio-mobile-cta.js';
	return $exclusions;
}
add_filter( 'rocket_delay_js_exclusions', 'domio_rocket_exclude_mobile_cta_js' );
