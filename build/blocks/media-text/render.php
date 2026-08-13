<?php
/**
 * Domio Media Text block render.
 *
 * @package Domio
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading        = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$layout         = isset( $attributes['layout'] ) ? $attributes['layout'] : 'default';
$media_position = isset( $attributes['mediaPosition'] ) ? $attributes['mediaPosition'] : 'right';
$media_id       = isset( $attributes['mediaId'] ) ? (int) $attributes['mediaId'] : 0;
$media_alt      = isset( $attributes['mediaAlt'] ) ? $attributes['mediaAlt'] : '';
$media_width    = isset( $attributes['mediaWidth'] ) ? (int) $attributes['mediaWidth'] : 50;

if ( ! in_array( $layout, array( 'default', 'narrow' ), true ) ) {
	$layout = 'default';
}

if ( ! in_array( $media_position, array( 'left', 'right' ), true ) ) {
	$media_position = 'right';
}

if ( $media_width < 30 || $media_width > 70 ) {
	$media_width = 50;
}

$is_narrow = ( 'narrow' === $layout );

$classes = array( 'domio-media-text' );
if ( $is_narrow ) {
	$classes[] = 'domio-media-text--narrow';
} else {
	$classes[] = 'domio-media-text--media-' . $media_position;
}
$classes[] = function_exists( 'domio_get_section_classes' )
	? domio_get_section_classes( $attributes )
	: 'domio-bg--surface';

$style_extra = $is_narrow ? '' : '--domio-media-width:' . $media_width . '%;';

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $classes ),
		'style' => function_exists( 'domio_get_section_style' )
			? domio_get_section_style( $attributes, 3, $style_extra )
			: $style_extra,
	)
);

$image_html = '';
if ( $media_id > 0 ) {
	$image_html = wp_get_attachment_image(
		$media_id,
		'large',
		false,
		array(
			'class'    => 'domio-media-text__image',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => $media_alt,
		)
	);
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-media-text__inner">
		<div class="domio-media-text__content">
			<?php if ( $heading ) : ?>
				<h2 class="domio-media-text__heading"><?php echo wp_kses_post( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<div class="domio-media-text__body">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- InnerBlocks. ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="domio-media-text__media"<?php echo $image_html ? '' : ' aria-hidden="true"'; ?>>
			<?php if ( $image_html ) : ?>
				<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?>
			<?php else : ?>
				<div class="domio-media-text__media-placeholder"></div>
			<?php endif; ?>
		</div>
	</div>
</section>
