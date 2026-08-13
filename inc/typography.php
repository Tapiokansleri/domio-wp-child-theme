<?php
/**
 * Domio landing template typography.
 *
 * Loads Onest and template-scoped type tokens that override Elementor kit
 * typography inside Domio blocks when "Domio sivupohja" is active.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current view uses the Domio landing page template.
 *
 * @return bool
 */
function domio_is_landing_template() {
	$slug = function_exists( 'domio_get_page_template_slug' )
		? domio_get_page_template_slug()
		: 'page-templates/domio-sivupohja.php';

	return is_page_template( $slug );
}

/**
 * Whether the current view is the Domio author archive.
 *
 * @return bool
 */
function domio_is_author_template() {
	return is_author();
}

/**
 * Add body class for Domio landing / author templates.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function domio_landing_body_class( $classes ) {
	if ( domio_is_landing_template() || domio_is_author_template() ) {
		$classes[] = 'domio-template';
	}

	if ( domio_is_author_template() ) {
		$classes[] = 'domio-author';
	}

	return $classes;
}
add_filter( 'body_class', 'domio_landing_body_class' );

/**
 * Enqueue Onest + Domio type stylesheet on landing / author templates (front).
 *
 * @return void
 */
function domio_enqueue_landing_typography() {
	if ( ! domio_is_landing_template() && ! domio_is_author_template() ) {
		return;
	}

	wp_enqueue_style(
		'domio-onest',
		'https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'domio-type',
		DOMIO_THEME_URI . '/assets/css/domio-type.css',
		array( 'domio-onest', 'domio-style' ),
		DOMIO_THEME_VERSION
	);

	if ( domio_is_author_template() ) {
		wp_enqueue_style(
			'domio-author',
			DOMIO_THEME_URI . '/assets/css/domio-author.css',
			array( 'domio-type' ),
			DOMIO_THEME_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'domio_enqueue_landing_typography', 30 );

/**
 * Load Domio typography in the block editor so the canvas matches front.
 *
 * add_editor_style() is required for the iframed canvas. enqueue_block_assets
 * alone does not reliably load Google Fonts inside the iframe.
 *
 * @return void
 */
function domio_setup_editor_styles() {
	add_theme_support( 'editor-styles' );
	add_editor_style(
		array(
			'https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap',
			'assets/css/domio-type.css',
			'assets/css/domio-editor.css',
		)
	);
}
add_action( 'after_setup_theme', 'domio_setup_editor_styles' );

/**
 * Editor chrome styles (header / inserter), outside the canvas iframe.
 *
 * @return void
 */
function domio_enqueue_editor_ui() {
	wp_enqueue_style(
		'domio-editor-ui',
		DOMIO_THEME_URI . '/assets/css/domio-editor-ui.css',
		array(),
		DOMIO_THEME_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'domio_enqueue_editor_ui' );

/**
 * Use Domio-style breadcrumb separator on landing template.
 *
 * @param string $separator Current separator.
 * @return string
 */
function domio_breadcrumb_separator( $separator ) {
	if ( domio_is_landing_template() ) {
		return ' <span class="domio-hero__breadcrumb-sep" aria-hidden="true">→</span> ';
	}

	return $separator;
}
add_filter( 'wpseo_breadcrumb_separator', 'domio_breadcrumb_separator' );
