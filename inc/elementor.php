<?php
/**
 * Domio ↔ Elementor integration.
 *
 * When “Domio sivupohja” is active, hide Elementor edit entry points so
 * editors stay in Gutenberg.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Domio page template slug.
 *
 * @return string
 */
function domio_get_page_template_slug() {
	return 'page-templates/domio-sivupohja.php';
}

/**
 * Whether a post uses Domio sivupohja.
 *
 * @param int|\WP_Post|null $post Post ID or object. Null = current post.
 * @return bool
 */
function domio_post_uses_sivupohja( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}

	return domio_get_page_template_slug() === (string) get_page_template_slug( $post );
}

/**
 * Remove Elementor “Edit with Elementor” from the admin bar on Domio pages.
 *
 * @param array $settings Admin bar config.
 * @return array
 */
function domio_filter_elementor_admin_bar( $settings ) {
	if ( function_exists( 'domio_is_landing_template' ) && domio_is_landing_template() ) {
		unset( $settings['elementor_edit_page'] );
	}

	return $settings;
}
add_filter( 'elementor/frontend/admin_bar/settings', 'domio_filter_elementor_admin_bar' );

/**
 * Remove “Edit with Elementor” from page/post list row actions.
 *
 * @param array    $actions Row actions.
 * @param \WP_Post $post    Post.
 * @return array
 */
function domio_filter_elementor_row_actions( $actions, $post ) {
	if ( domio_post_uses_sivupohja( $post ) ) {
		unset( $actions['edit_with_elementor'] );
	}

	return $actions;
}
add_filter( 'page_row_actions', 'domio_filter_elementor_row_actions', 20, 2 );
add_filter( 'post_row_actions', 'domio_filter_elementor_row_actions', 20, 2 );

/**
 * Hide classic-editor Elementor switch UI when Domio sivupohja is set.
 *
 * @return void
 */
function domio_hide_elementor_classic_switch() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->base ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id || ! domio_post_uses_sivupohja( $post_id ) ) {
		return;
	}

	echo '<style id="domio-hide-elementor-classic">#elementor-switch-mode,#elementor-editor{display:none!important}</style>';
}
add_action( 'admin_head', 'domio_hide_elementor_classic_switch' );

/**
 * Do not let Elementor Theme Builder wrap Domio sivupohja posts/pages.
 *
 * @param bool   $need_override Whether Elementor should override the location.
 * @param string $location      Theme Builder location slug.
 * @return bool
 */
function domio_skip_elementor_theme_builder( $need_override, $location ) {
	if ( ! $need_override ) {
		return $need_override;
	}

	if ( ! function_exists( 'domio_post_uses_sivupohja' ) || ! is_singular() ) {
		return $need_override;
	}

	if ( ! in_array( $location, array( 'single', 'single-post', 'page' ), true ) ) {
		return $need_override;
	}

	if ( domio_post_uses_sivupohja() ) {
		return false;
	}

	return $need_override;
}
add_filter( 'elementor/theme/need_override_location', 'domio_skip_elementor_theme_builder', 20, 2 );

/**
 * Do not print Elementor Theme Builder single layout on sivupohja.
 *
 * @param bool $should_do Whether Elementor should render the location.
 * @return bool
 */
function domio_prevent_elementor_single( $should_do ) {
	if ( function_exists( 'domio_post_uses_sivupohja' ) && is_singular() && domio_post_uses_sivupohja() ) {
		return false;
	}

	return $should_do;
}
add_filter( 'elementor/theme/do_location/single', 'domio_prevent_elementor_single' );

/**
 * Force Domio sivupohja even if Elementor Theme Builder swaps template_include.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function domio_sivupohja_template_include( $template ) {
	if ( ! is_singular() || ! function_exists( 'domio_post_uses_sivupohja' ) || ! function_exists( 'domio_get_page_template_slug' ) ) {
		return $template;
	}

	if ( ! domio_post_uses_sivupohja() ) {
		return $template;
	}

	$found = locate_template( domio_get_page_template_slug() );

	return $found ? $found : $template;
}
add_filter( 'template_include', 'domio_sivupohja_template_include', 99 );

/**
 * Drop Theme Builder single CSS on sivupohja so the old blog sidebar cannot leak in.
 *
 * @return void
 */
function domio_dequeue_elementor_single_css() {
	if ( ! function_exists( 'domio_post_uses_sivupohja' ) || ! is_singular() || ! domio_post_uses_sivupohja() ) {
		return;
	}

	wp_dequeue_style( 'elementor-post-1676' );
	wp_deregister_style( 'elementor-post-1676' );
}
add_action( 'wp_enqueue_scripts', 'domio_dequeue_elementor_single_css', 100 );

/**
 * Remove Theme Builder body class on sivupohja.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function domio_filter_elementor_body_class( $classes ) {
	if ( ! function_exists( 'domio_post_uses_sivupohja' ) || ! is_singular() || ! domio_post_uses_sivupohja() ) {
		return $classes;
	}

	return array_values(
		array_filter(
			$classes,
			static function ( $class ) {
				return 0 !== strpos( (string) $class, 'elementor-page-' );
			}
		)
	);
}
add_filter( 'body_class', 'domio_filter_elementor_body_class', 40 );
