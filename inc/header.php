<?php
/**
 * Domio custom header integration.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Domio header menu location.
 *
 * @return void
 */
function domio_register_header_menus() {
	register_nav_menus(
		array(
			'domio-primary' => __( 'Domio päävalikko', 'domio' ),
		)
	);
}
add_action( 'after_setup_theme', 'domio_register_header_menus' );

/**
 * Enqueue Domio header assets when enabled.
 *
 * @return void
 */
function domio_enqueue_header_assets() {
	if ( is_admin() || ! domio_use_custom_header() ) {
		return;
	}

	wp_enqueue_style(
		'domio-onest',
		'https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'domio-header',
		DOMIO_THEME_URI . '/assets/css/domio-header.css',
		array( 'domio-style', 'domio-onest' ),
		DOMIO_THEME_VERSION
	);

	wp_enqueue_script(
		'domio-header',
		DOMIO_THEME_URI . '/assets/js/domio-header.js',
		array(),
		DOMIO_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'domio_enqueue_header_assets', 25 );

/**
 * Add body class when Domio header is active.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function domio_header_body_class( $classes ) {
	if ( domio_use_custom_header() ) {
		$classes[] = 'domio-has-header';
	}

	return $classes;
}
add_filter( 'body_class', 'domio_header_body_class' );

/**
 * Stop Elementor Theme Builder from replacing/printing the theme header.
 *
 * @return void
 */
function domio_detach_elementor_header() {
	if ( is_admin() || ! domio_use_custom_header() ) {
		return;
	}

	// Elementor Pro Theme Support hooks get_header and discards theme header.php.
	remove_all_actions( 'get_header' );
}
add_action( 'template_redirect', 'domio_detach_elementor_header', 1 );

/**
 * Do not return Elementor header documents when Domio header is forced.
 *
 * @param array  $documents Documents for location.
 * @param string $location  Location name.
 * @return array
 */
function domio_empty_elementor_header_documents( $documents, $location = '' ) {
	if ( 'header' === $location && domio_use_custom_header() ) {
		return array();
	}

	return $documents;
}
add_filter( 'elementor/theme/get_location_templates/documents_for_location', 'domio_empty_elementor_header_documents', 999, 2 );

/**
 * Stop Elementor Theme Builder from printing a header template.
 *
 * @param bool $should_do Whether Elementor should render the location.
 * @return bool
 */
function domio_prevent_elementor_header( $should_do ) {
	if ( domio_use_custom_header() ) {
		return false;
	}

	return $should_do;
}
add_filter( 'elementor/theme/do_location/header', 'domio_prevent_elementor_header' );

/**
 * Auto-assign Finnish main menu to Domio location when empty.
 *
 * @return void
 */
function domio_maybe_assign_primary_menu() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( ! empty( $locations['domio-primary'] ) ) {
		return;
	}

	$menu = wp_get_nav_menu_object( 'main-menu-finnish' );
	if ( ! $menu ) {
		return;
	}

	$locations['domio-primary'] = (int) $menu->term_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
add_action( 'after_setup_theme', 'domio_maybe_assign_primary_menu', 20 );

/**
 * Enable custom logo from Site Identity / site settings.
 *
 * @return void
 */
function domio_setup_theme_supports() {
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
}
add_action( 'after_setup_theme', 'domio_setup_theme_supports' );

/**
 * Resolve logo HTML from site settings (custom_logo / site_logo).
 *
 * @return string
 */
function domio_get_header_logo_html() {
	$logo_id = (int) get_theme_mod( 'custom_logo' );

	if ( ! $logo_id ) {
		$logo_id = (int) get_option( 'site_logo' );
	}

	if ( $logo_id ) {
		$logo = wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class'    => 'domio-header__logo-img',
				'alt'      => get_bloginfo( 'name' ),
				'loading'  => 'eager',
				'decoding' => 'async',
			)
		);

		if ( $logo ) {
			return $logo;
		}
	}

	return '<span class="domio-header__logo-text-only">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
}

/**
 * Get assigned Domio menu ID, falling back to Main Menu - Finnish.
 *
 * @return int
 */
function domio_get_primary_menu_id() {
	$locations = get_nav_menu_locations();

	if ( ! empty( $locations['domio-primary'] ) ) {
		return (int) $locations['domio-primary'];
	}

	$menu = wp_get_nav_menu_object( 'main-menu-finnish' );
	if ( $menu ) {
		return (int) $menu->term_id;
	}

	return 0;
}

/**
 * Normalize phone number for tel: links.
 *
 * @param string $phone Display phone.
 * @return string
 */
function domio_phone_href( $phone ) {
	$digits = preg_replace( '/[^\d+]/', '', $phone );
	if ( is_string( $digits ) && 0 === strpos( $digits, '040' ) ) {
		$digits = '+358' . substr( $digits, 1 );
	}
	return $digits ? 'tel:' . $digits : '';
}
