<?php
/**
 * Register Domio Gutenberg blocks.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Relative path to the default Domio hero image inside the theme.
 */
define(
	'DOMIO_HERO_DEFAULT_IMAGE',
	'assets/images/kiinteistohuolto-ja-kiinteistonhoitopalvelut-kevathuolto.webp'
);

/**
 * Relative path to the CTA repeating pattern inside the theme.
 */
define( 'DOMIO_CTA_PATTERN_IMAGE', 'assets/images/cover-bg.png' );

/**
 * Default Domio hero image URL (theme-bundled).
 *
 * @return string
 */
function domio_get_default_hero_image_url() {
	return trailingslashit( DOMIO_THEME_URI ) . DOMIO_HERO_DEFAULT_IMAGE;
}

/**
 * Default Domio hero image alt text.
 *
 * @return string
 */
function domio_get_default_hero_image_alt() {
	return __( 'Kiinteistöhuolto ja kiinteistönhoitopalvelut – keväthuolto', 'domio' );
}

/**
 * Repeating CTA background pattern URL (theme-bundled).
 *
 * @return string
 */
function domio_get_cta_pattern_url() {
	return trailingslashit( DOMIO_THEME_URI ) . DOMIO_CTA_PATTERN_IMAGE;
}

/**
 * Theme-bundled default photos for the contacts block.
 *
 * @return array<string, string> Person id => URL.
 */
function domio_get_default_contact_photos() {
	$base = trailingslashit( DOMIO_THEME_URI ) . 'assets/images/contacts/';

	return array(
		'elias' => $base . 'elias-myllykoski.png',
		'eero'  => $base . 'eero-jarvenpaa.webp',
		'henna' => $base . 'henna-jarvenpaa.png',
	);
}

/**
 * Build a tel: href from a display phone number.
 *
 * @param string $phone Display phone.
 * @return string
 */
function domio_tel_href( $phone ) {
	$digits = preg_replace( '/\D+/', '', (string) $phone );

	if ( '' === $digits ) {
		return '';
	}

	if ( 0 === strpos( $digits, '358' ) ) {
		return 'tel:+' . $digits;
	}

	if ( 0 === strpos( $digits, '0' ) ) {
		return 'tel:+358' . substr( $digits, 1 );
	}

	return 'tel:' . $digits;
}

/**
 * Person photo HTML for the contacts block.
 *
 * Prefers a media library attachment, then a theme-bundled default by person id.
 *
 * @param array $person Person attributes.
 * @return string
 */
function domio_get_contact_person_image( $person ) {
	if ( ! is_array( $person ) ) {
		return '';
	}

	$name      = isset( $person['name'] ) ? $person['name'] : '';
	$image_alt = isset( $person['imageAlt'] ) && '' !== $person['imageAlt'] ? $person['imageAlt'] : $name;
	$image_id  = isset( $person['imageId'] ) ? (int) $person['imageId'] : 0;

	if ( $image_id > 0 ) {
		$html = wp_get_attachment_image(
			$image_id,
			'medium',
			false,
			array(
				'class'    => 'domio-contacts__photo',
				'alt'      => $image_alt,
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);

		if ( $html ) {
			return $html;
		}
	}

	$defaults = domio_get_default_contact_photos();
	$person_id = isset( $person['id'] ) ? $person['id'] : '';
	$src       = '';

	if ( ! empty( $person['imageUrl'] ) ) {
		$src = $person['imageUrl'];
	} elseif ( $person_id && isset( $defaults[ $person_id ] ) ) {
		$src = $defaults[ $person_id ];
	}

	if ( '' === $src ) {
		return '';
	}

	return sprintf(
		'<img class="domio-contacts__photo" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
		esc_url( $src ),
		esc_attr( $image_alt )
	);
}

/**
 * Allowed Domio section background slugs.
 *
 * @return string[]
 */
function domio_get_background_slugs() {
	return array( 'surface', 'muted', 'green' );
}

/**
 * Sanitize a block background attribute.
 *
 * Maps legacy media-text values (none/dark) to the current slugs.
 *
 * @param mixed  $background Raw attribute.
 * @param string $default    Fallback slug.
 * @return string
 */
function domio_sanitize_background( $background, $default = 'surface' ) {
	if ( 'none' === $background ) {
		return 'surface';
	}

	if ( 'dark' === $background ) {
		return 'green';
	}

	$allowed = domio_get_background_slugs();

	if ( is_string( $background ) && in_array( $background, $allowed, true ) ) {
		return $background;
	}

	return in_array( $default, $allowed, true ) ? $default : 'surface';
}

/**
 * CSS class for a section background.
 *
 * @param mixed  $background Raw attribute.
 * @param string $default    Fallback slug.
 * @return string
 */
function domio_get_background_class( $background, $default = 'surface' ) {
	return 'domio-bg--' . domio_sanitize_background( $background, $default );
}

/**
 * Allowed Domio section pattern slugs.
 *
 * @return string[]
 */
function domio_get_pattern_slugs() {
	return array( 'none', '1', '2' );
}

/**
 * Sanitize a block pattern attribute.
 *
 * @param mixed  $pattern Raw attribute.
 * @param string $default Fallback slug.
 * @return string
 */
function domio_sanitize_pattern( $pattern, $default = 'none' ) {
	$allowed = domio_get_pattern_slugs();

	if ( is_string( $pattern ) && in_array( $pattern, $allowed, true ) ) {
		return $pattern;
	}

	return in_array( $default, $allowed, true ) ? $default : 'none';
}

/**
 * CSS class for a section pattern.
 *
 * @param mixed  $pattern Raw attribute.
 * @param string $default Fallback slug.
 * @return string
 */
function domio_get_pattern_class( $pattern, $default = 'none' ) {
	return 'domio-pattern--' . domio_sanitize_pattern( $pattern, $default );
}

/**
 * Sanitize pattern opacity (0–100).
 *
 * @param mixed $opacity Raw attribute.
 * @param int   $default Fallback.
 * @return int
 */
function domio_sanitize_pattern_opacity( $opacity, $default = 3 ) {
	if ( ! is_numeric( $opacity ) ) {
		$opacity = $default;
	}

	$opacity = (int) round( (float) $opacity );

	if ( $opacity < 0 ) {
		return 0;
	}

	if ( $opacity > 100 ) {
		return 100;
	}

	return $opacity;
}

/**
 * Resolve a block variant from className (is-style-*) or the variant attribute.
 *
 * @param array    $attributes Block attributes.
 * @param string[] $allowed    Allowed slugs.
 * @param string   $default    Fallback slug.
 * @return string
 */
function domio_get_variant_from_attributes( $attributes, $allowed, $default ) {
	$class = isset( $attributes['className'] ) ? $attributes['className'] : '';

	if ( is_string( $class ) && preg_match( '/(?:^|\s)is-style-([a-z0-9-]+)(?:\s|$)/', $class, $matches ) ) {
		if ( in_array( $matches[1], $allowed, true ) ) {
			return $matches[1];
		}
	}

	$variant = isset( $attributes['variant'] ) ? $attributes['variant'] : $default;

	return in_array( $variant, $allowed, true ) ? $variant : $default;
}

/**
 * Inline style for pattern opacity, optionally merged with extra declarations.
 *
 * @param array  $attributes Block attributes.
 * @param int    $default    Opacity fallback 0–100.
 * @param string $extra      Extra CSS declarations.
 * @return string
 */
function domio_get_section_style( $attributes, $default = 3, $extra = '' ) {
	$raw     = isset( $attributes['patternOpacity'] ) ? $attributes['patternOpacity'] : $default;
	$opacity = domio_sanitize_pattern_opacity( $raw, $default );
	$style   = '--domio-pattern-opacity:' . ( $opacity / 100 ) . ';';

	if ( is_string( $extra ) && '' !== $extra ) {
		$style .= ltrim( $extra, ';' );
	}

	return $style;
}

/**
 * Combined background + pattern classes for a Domio section.
 *
 * @param array  $attributes       Block attributes.
 * @param string $bg_default       Background fallback.
 * @param string $pattern_default  Pattern fallback.
 * @return string
 */
function domio_get_section_classes( $attributes, $bg_default = 'surface', $pattern_default = 'none' ) {
	$background = isset( $attributes['background'] ) ? $attributes['background'] : $bg_default;
	$pattern    = isset( $attributes['pattern'] ) ? $attributes['pattern'] : $pattern_default;

	return trim(
		domio_get_background_class( $background, $bg_default ) . ' ' . domio_get_pattern_class( $pattern, $pattern_default )
	);
}

/**
 * Register Domio block category (shown first in the inserter).
 *
 * @param array[] $categories Block categories.
 * @return array[]
 */
function domio_register_block_category( $categories ) {
	foreach ( $categories as $category ) {
		if ( isset( $category['slug'] ) && 'domio' === $category['slug'] ) {
			return $categories;
		}
	}

	array_unshift(
		$categories,
		array(
			'slug'  => 'domio',
			'title' => 'Domio',
			'icon'  => null,
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'domio_register_block_category' );

/**
 * Default vertical padding preset for a Domio block.
 *
 * Matches the CSS `padding-block` on section wrappers so the editor
 * Dimensions slider starts on the real default, not zero.
 *
 * @param string $block_name Block name (e.g. domio/card-grid).
 * @return string Theme.json spacing preset reference.
 */
function domio_get_block_padding_preset( $block_name ) {
	if ( 'domio/cta' === $block_name ) {
		return 'var:preset|spacing|80';
	}

	return 'var:preset|spacing|60';
}

/**
 * Seed spacing.padding defaults so Gutenberg's Mitat slider matches CSS.
 *
 * Block supports do not inherit stylesheet padding, so an unset value
 * renders the slider at 0 even when the section already has spacing 60/80.
 *
 * @param array $metadata Block metadata from block.json.
 * @return array
 */
function domio_block_spacing_defaults( $metadata ) {
	if ( empty( $metadata['name'] ) || 0 !== strpos( $metadata['name'], 'domio/' ) ) {
		return $metadata;
	}

	if ( empty( $metadata['supports']['spacing']['padding'] ) ) {
		return $metadata;
	}

	$preset   = domio_get_block_padding_preset( $metadata['name'] );
	$existing = array();

	if ( isset( $metadata['attributes']['style']['default'] ) && is_array( $metadata['attributes']['style']['default'] ) ) {
		$existing = $metadata['attributes']['style']['default'];
	}

	$metadata['attributes']['style'] = array(
		'type'    => 'object',
		'default' => array_replace_recursive(
			$existing,
			array(
				'spacing' => array(
					'padding' => array(
						'top'    => $preset,
						'bottom' => $preset,
					),
				),
			)
		),
	);

	return $metadata;
}
add_filter( 'block_type_metadata', 'domio_block_spacing_defaults' );

/**
 * Register all blocks from the build directory.
 *
 * Uses scandir instead of glob for Windows path compatibility.
 *
 * @return void
 */
function domio_register_blocks() {
	$blocks_dir = trailingslashit( DOMIO_THEME_DIR ) . 'build/blocks';

	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	$entries = scandir( $blocks_dir );

	if ( false === $entries ) {
		return;
	}

	foreach ( $entries as $entry ) {
		if ( '.' === $entry || '..' === $entry ) {
			continue;
		}

		$block_dir = $blocks_dir . '/' . $entry;

		if ( is_dir( $block_dir ) && file_exists( $block_dir . '/block.json' ) ) {
			register_block_type( $block_dir );
		}
	}
}
add_action( 'init', 'domio_register_blocks' );

/**
 * Register the Domio landing pattern category.
 *
 * @param array[] $categories Existing categories.
 * @return array[]
 */
function domio_register_block_pattern_category( $categories ) {
	$categories[] = array(
		'slug'  => 'domio-landing',
		'title' => __( 'Domio: Ländärit', 'domio' ),
	);

	return $categories;
}
add_filter( 'block_pattern_categories', 'domio_register_block_pattern_category' );

/**
 * Enqueue block editor plugin (landing preload sidebar).
 *
 * @return void
 */
function domio_enqueue_editor_plugin() {
	$asset_file = DOMIO_THEME_DIR . '/build/editor/landing-preload.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = include $asset_file;

	wp_enqueue_script(
		'domio-landing-preload',
		DOMIO_THEME_URI . '/build/editor/landing-preload.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);

	wp_localize_script(
		'domio-landing-preload',
		'domioLandingPreload',
		array(
			'formShortcode' => '[metform form_id="270"]',
			'defaultPhone'  => '',
			'pageTemplate'  => function_exists( 'domio_get_page_template_slug' )
				? domio_get_page_template_slug()
				: 'page-templates/domio-sivupohja.php',
			'heroImageUrl'   => domio_get_default_hero_image_url(),
			'heroImageAlt'   => domio_get_default_hero_image_alt(),
			'ctaPatternUrl'  => domio_get_cta_pattern_url(),
		)
	);

	wp_add_inline_script(
		'wp-block-editor',
		'window.domioBlockDefaults=' . wp_json_encode(
			array(
				'pageTemplate'   => function_exists( 'domio_get_page_template_slug' )
					? domio_get_page_template_slug()
					: 'page-templates/domio-sivupohja.php',
				'heroImageUrl'   => domio_get_default_hero_image_url(),
				'heroImageAlt'   => domio_get_default_hero_image_alt(),
				'ctaPatternUrl'  => domio_get_cta_pattern_url(),
				'contactPhotos'  => function_exists( 'domio_get_default_contact_photos' )
					? domio_get_default_contact_photos()
					: array(),
				'relatedLinkGroups' => function_exists( 'domio_get_related_link_groups' )
					? domio_get_related_link_groups()
					: array(),
				'settingsUrl'    => admin_url( 'themes.php?page=domio-settings' ),
			)
		) . ';',
		'before'
	);

	wp_set_script_translations( 'domio-landing-preload', 'domio' );
}
add_action( 'enqueue_block_editor_assets', 'domio_enqueue_editor_plugin' );

/**
 * Enqueue Domio hintalaskuri plugin + block wrapper assets.
 *
 * The Gutenberg block must NOT register handle `domio-hintalaskuri-style`
 * (that is the plugin's calculator.css). Wrapper CSS uses a separate handle.
 *
 * @return void
 */
function domio_enqueue_hintalaskuri_assets() {
	$block_css = DOMIO_THEME_DIR . '/build/blocks/hintalaskuri/style-index.css';

	if ( file_exists( $block_css ) && ! wp_style_is( 'domio-hintalaskuri-block', 'enqueued' ) ) {
		wp_enqueue_style(
			'domio-hintalaskuri-block',
			DOMIO_THEME_URI . '/build/blocks/hintalaskuri/style-index.css',
			array(),
			(string) filemtime( $block_css )
		);
	}

	if ( wp_style_is( 'domio-hintalaskuri-style', 'registered' ) ) {
		wp_enqueue_style( 'domio-hintalaskuri-style' );
	}

	if ( wp_script_is( 'domio-hintalaskuri-calculator', 'registered' ) ) {
		wp_enqueue_script( 'domio-hintalaskuri-calculator' );
	}
}

/**
 * Front enqueue for pages that contain the hintalaskuri block.
 *
 * @return void
 */
function domio_maybe_enqueue_hintalaskuri_assets() {
	if ( is_admin() || ! is_singular() ) {
		return;
	}

	if ( ! has_block( 'domio/hintalaskuri' ) ) {
		return;
	}

	domio_enqueue_hintalaskuri_assets();
}
add_action( 'wp_enqueue_scripts', 'domio_maybe_enqueue_hintalaskuri_assets', 30 );

/**
 * Keep the calculator ES module out of WP Rocket delay/lazyload.
 *
 * @param string[] $exclusions Exclusion patterns.
 * @return string[]
 */
function domio_rocket_exclude_hintalaskuri_js( $exclusions ) {
	$exclusions[] = 'domio-hintalaskuri';
	$exclusions[] = 'calculator.js';
	return $exclusions;
}
add_filter( 'rocket_delay_js_exclusions', 'domio_rocket_exclude_hintalaskuri_js' );
add_filter( 'rocket_exclude_defer_js', 'domio_rocket_exclude_hintalaskuri_js' );
add_filter( 'rocket_exclude_js', 'domio_rocket_exclude_hintalaskuri_js' );
