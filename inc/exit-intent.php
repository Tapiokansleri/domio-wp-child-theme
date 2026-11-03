<?php
/**
 * Exit-intent nudge on desktop: when the pointer leaves the window towards the tab bar, a small panel offers
 * the price calculator and a quote request. Phones have the floating "Laske hinta-arvio" button instead
 * (inc/mobile-cta.php), because exit intent cannot be detected there.
 *
 * Shown at most once per browser session (sessionStorage), not in the first 10 seconds, not after the visitor
 * has used the calculator or a form, and never on the calculator, form, thank-you or privacy pages.
 * Can be switched off in Teeman asetukset → Header.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the nudge is printed on this request.
 *
 * @return bool
 */
function domio_exit_intent_enabled() {
	if ( is_admin() || is_feed() || is_embed() || wp_doing_ajax() || is_404() ) {
		return false;
	}
	$settings = function_exists( 'domio_get_settings' ) ? domio_get_settings() : array();
	if ( empty( $settings['exit_intent'] ) ) {
		return false;
	}
	$excluded = array( 'hintalaskuri', 'ota-yhteytta', 'tarjouspyynto', 'yhteystiedot', 'kiitos-yhteydenotosta', 'kiitos-tarjouspyynnosta', 'rekisteri-ja-tietosuojaseloste' );
	if ( is_page( $excluded ) ) {
		return false;
	}
	return (bool) apply_filters( 'domio_exit_intent_enabled', true );
}

/**
 * Enqueue the nudge's style and script.
 *
 * @return void
 */
function domio_exit_intent_enqueue() {
	if ( ! domio_exit_intent_enabled() ) {
		return;
	}
	wp_enqueue_style( 'domio-exit-intent', DOMIO_THEME_URI . '/assets/css/domio-exit-intent.css', array(), DOMIO_THEME_VERSION );
	wp_enqueue_script( 'domio-exit-intent', DOMIO_THEME_URI . '/assets/js/domio-exit-intent.js', array(), DOMIO_THEME_VERSION, true );
	wp_script_add_data( 'domio-exit-intent', 'strategy', 'defer' );
}
add_action( 'wp_enqueue_scripts', 'domio_exit_intent_enqueue', 30 );

/**
 * Print the panel (hidden until the script opens it).
 *
 * @return void
 */
function domio_exit_intent_render() {
	if ( ! domio_exit_intent_enabled() ) {
		return;
	}
	$has_calculator = is_singular() && has_block( 'domio/hintalaskuri', get_queried_object_id() );
	$calculator_url = $has_calculator ? '#hintalaskuri' : home_url( '/hintalaskuri/' );
	?>
	<div class="domio-exit" data-domio-exit hidden>
		<div class="domio-exit__panel" role="dialog" aria-modal="false" aria-labelledby="domio-exit-title" tabindex="-1">
			<button type="button" class="domio-exit__close" data-domio-exit-close aria-label="<?php echo esc_attr__( 'Sulje', 'domio' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>
			</button>
			<p class="domio-exit__eyebrow"><?php echo esc_html__( 'Ennen kuin lähdet', 'domio' ); ?></p>
			<h2 class="domio-exit__title" id="domio-exit-title"><?php echo esc_html__( 'Mitä kiinteistönne huolto maksaisi?', 'domio' ); ?></h2>
			<p class="domio-exit__text"><?php echo esc_html__( 'Laske suuntaa-antava hinta-arvio 30 sekunnissa pelkällä osoitteella. Ei yhteystietoja, ei sitoumuksia.', 'domio' ); ?></p>
			<div class="domio-exit__actions">
				<a class="domio-exit__primary" href="<?php echo esc_url( $calculator_url ); ?>" data-domio-exit-cta="calculator"><?php echo esc_html__( 'Laske hinta-arvio', 'domio' ); ?></a>
				<a class="domio-exit__secondary" href="<?php echo esc_url( home_url( '/ota-yhteytta/?aihe=tarjous' ) ); ?>" data-domio-exit-cta="quote"><?php echo esc_html__( 'Pyydä tarjous', 'domio' ); ?></a>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'domio_exit_intent_render', 20 );

/**
 * Keep the nudge script out of WP Rocket's delayed JavaScript: a delayed script would only start after the
 * first scroll or click, and the leave gesture is often the visitor's first move.
 *
 * @param string[] $exclusions Exclusion patterns.
 * @return string[]
 */
function domio_rocket_exclude_exit_intent_js( $exclusions ) {
	$exclusions[] = 'domio-exit-intent.js';
	return $exclusions;
}
add_filter( 'rocket_delay_js_exclusions', 'domio_rocket_exclude_exit_intent_js' );
