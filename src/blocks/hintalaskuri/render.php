<?php
/**
 * Domio hintalaskuri block render.
 *
 * @package Domio
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'domio_enqueue_hintalaskuri_assets' ) ) {
	domio_enqueue_hintalaskuri_assets();
}

$anchor_id = isset( $attributes['anchor'] ) && is_string( $attributes['anchor'] ) && '' !== $attributes['anchor']
	? $attributes['anchor']
	: 'hintalaskuri';

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'id'    => sanitize_title( $anchor_id ),
		'class' => 'domio-hintalaskuri-block alignfull ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);

$calculator = shortcode_exists( 'domio_hintalaskuri' )
	? apply_shortcodes( '[domio_hintalaskuri]' )
	: '';
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-hintalaskuri-block__inner">
		<div class="domio-hintalaskuri-block__widget">
			<?php echo $calculator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin shortcode HTML. ?>
		</div>
	</div>
</section>
