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
$content_width  = isset( $attributes['contentWidth'] ) ? $attributes['contentWidth'] : 'default';
$media_position = isset( $attributes['mediaPosition'] ) ? $attributes['mediaPosition'] : 'right';
$media_id       = isset( $attributes['mediaId'] ) ? (int) $attributes['mediaId'] : 0;
$media_alt      = isset( $attributes['mediaAlt'] ) ? $attributes['mediaAlt'] : '';
$media_width    = isset( $attributes['mediaWidth'] ) ? (int) $attributes['mediaWidth'] : 50;

if ( ! in_array( $layout, array( 'default', 'narrow' ), true ) ) {
	$layout = 'default';
}

if ( ! in_array( $content_width, array( 'default', 'full' ), true ) ) {
	$content_width = 'default';
}

if ( ! in_array( $media_position, array( 'left', 'right' ), true ) ) {
	$media_position = 'right';
}

if ( $media_width < 30 || $media_width > 70 ) {
	$media_width = 50;
}

$is_narrow = ( 'narrow' === $layout );

$summary_items = isset( $attributes['summaryItems'] ) && is_array( $attributes['summaryItems'] )
	? $attributes['summaryItems']
	: array();

$summary_items = array_values(
	array_filter(
		$summary_items,
		static function ( $item ) {
			if ( ! is_array( $item ) ) {
				return false;
			}
			$title = isset( $item['title'] ) ? trim( wp_strip_all_tags( (string) $item['title'] ) ) : '';
			$text  = isset( $item['text'] ) ? trim( wp_strip_all_tags( (string) $item['text'] ) ) : '';
			return '' !== $title || '' !== $text;
		}
	)
);
$summary_items = array_slice( $summary_items, 0, 7 );
$has_summary   = count( $summary_items ) > 0;

$image_html = '';
if ( ! $has_summary && $media_id > 0 ) {
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

$has_media = ( '' !== $image_html );
$has_aside = $has_summary || $has_media;

$classes = array( 'domio-media-text' );
if ( $has_summary ) {
	$classes[] = 'domio-media-text--has-summary';
}
if ( ! $has_aside ) {
	$classes[] = 'domio-media-text--no-media';
} elseif ( $is_narrow ) {
	$classes[] = 'domio-media-text--narrow';
} else {
	$classes[] = 'domio-media-text--media-' . $media_position;
}
if ( 'full' === $content_width ) {
	$classes[] = 'domio-media-text--content-full';
}
$classes[] = function_exists( 'domio_get_section_classes' )
	? domio_get_section_classes( $attributes )
	: 'domio-bg--surface';

$style_extra = ( $is_narrow || ! $has_aside ) ? '' : '--domio-media-width:' . $media_width . '%;';

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $classes ),
		'style' => function_exists( 'domio_get_section_style' )
			? domio_get_section_style( $attributes, 3, $style_extra )
			: $style_extra,
	)
);
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

		<?php if ( $has_summary ) : ?>
			<ul class="domio-media-text__summary">
				<?php foreach ( $summary_items as $item ) : ?>
					<?php
					$icon  = isset( $item['icon'] ) ? $item['icon'] : 'check';
					$title = isset( $item['title'] ) ? $item['title'] : '';
					$text  = isset( $item['text'] ) ? $item['text'] : '';
					?>
					<li class="domio-media-text__summary-item">
						<span class="domio-media-text__summary-icon" aria-hidden="true">
							<?php
							echo function_exists( 'domio_get_icon_svg' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
								? domio_get_icon_svg( $icon )
								: '';
							?>
						</span>
						<div class="domio-media-text__summary-copy">
							<?php if ( $title ) : ?>
								<p class="domio-media-text__summary-title"><?php echo wp_kses_post( $title ); ?></p>
							<?php endif; ?>
							<?php if ( $text ) : ?>
								<p class="domio-media-text__summary-text"><?php echo wp_kses_post( $text ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $has_media ) : ?>
			<div class="domio-media-text__media">
				<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?>
			</div>
		<?php endif; ?>
	</div>
</section>
