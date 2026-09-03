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
 * Whether the current view is a job archive or single.
 *
 * @return bool
 */
function domio_is_jobs_template() {
	return function_exists( 'domio_is_tyopaikka_template' ) && domio_is_tyopaikka_template();
}

/**
 * Add body class for Domio landing / author / jobs templates.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function domio_landing_body_class( $classes ) {
	if ( domio_is_landing_template() || domio_is_author_template() || domio_is_jobs_template() ) {
		$classes[] = 'domio-template';
	}

	if ( domio_is_author_template() ) {
		$classes[] = 'domio-author';
	}

	if ( domio_is_jobs_template() ) {
		$classes[] = 'domio-jobs';
	}

	if ( domio_is_landing_template() ) {
		$classes[] = 'domio-overlay-header';
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
	if ( ! domio_is_landing_template() && ! domio_is_author_template() && ! domio_is_jobs_template() ) {
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

	if ( domio_is_jobs_template() ) {
		wp_enqueue_style(
			'domio-jobs',
			DOMIO_THEME_URI . '/assets/css/domio-jobs.css',
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
 * Whether the current admin screen is the tyopaikka block editor.
 *
 * @return bool
 */
function domio_is_tyopaikka_editor() {
	if ( ! is_admin() ) {
		return false;
	}

	if ( defined( 'DOMIO_JOB_POST_TYPE' ) ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && ! empty( $screen->post_type ) ) {
			return DOMIO_JOB_POST_TYPE === $screen->post_type;
		}

		if ( isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return DOMIO_JOB_POST_TYPE === sanitize_key( wp_unslash( $_GET['post_type'] ) );
		}

		if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return DOMIO_JOB_POST_TYPE === get_post_type( (int) $_GET['post'] );
		}
	}

	return false;
}

/**
 * Constrain the job editor layout to the published 800px column.
 *
 * @param array                   $settings Editor settings.
 * @param WP_Block_Editor_Context $context  Editor context.
 * @return array
 */
function domio_tyopaikka_editor_settings( $settings, $context ) {
	$post_type = '';
	if ( isset( $context->post->post_type ) ) {
		$post_type = $context->post->post_type;
	} elseif ( ! empty( $settings['postType'] ) ) {
		$post_type = $settings['postType'];
	}

	if ( ! defined( 'DOMIO_JOB_POST_TYPE' ) || DOMIO_JOB_POST_TYPE !== $post_type ) {
		return $settings;
	}

	if ( isset( $settings['__experimentalFeatures']['layout'] ) && is_array( $settings['__experimentalFeatures']['layout'] ) ) {
		$settings['__experimentalFeatures']['layout']['contentSize'] = '800px';
		$settings['__experimentalFeatures']['layout']['wideSize']    = '800px';
	}

	$css_file = DOMIO_THEME_DIR . '/assets/css/domio-jobs-editor.css';
	if ( is_readable( $css_file ) ) {
		$settings['styles'][] = array(
			'css' => (string) file_get_contents( $css_file ),
		);
	}

	return $settings;
}
add_filter( 'block_editor_settings_all', 'domio_tyopaikka_editor_settings', 20, 2 );

/**
 * Load job-column editor CSS inside the iframed canvas.
 *
 * @return void
 */
function domio_enqueue_jobs_editor_canvas() {
	if ( ! domio_is_tyopaikka_editor() ) {
		return;
	}

	wp_enqueue_style(
		'domio-onest',
		'https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'domio-jobs-editor',
		DOMIO_THEME_URI . '/assets/css/domio-jobs-editor.css',
		array( 'domio-onest' ),
		DOMIO_THEME_VERSION
	);
}
add_action( 'enqueue_block_assets', 'domio_enqueue_jobs_editor_canvas' );

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
