<?php
/**
 * Domio Steps block render.
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

$heading      = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$steps        = isset( $attributes['steps'] ) && is_array( $attributes['steps'] ) ? $attributes['steps'] : array();
$orientation  = isset( $attributes['orientation'] ) ? $attributes['orientation'] : 'horizontal';
$show_numbers = ! isset( $attributes['showNumbers'] ) || ! empty( $attributes['showNumbers'] );

if ( ! in_array( $orientation, array( 'horizontal', 'vertical' ), true ) ) {
	$orientation = 'horizontal';
}

$classes = array(
	'domio-steps',
	'domio-steps--' . $orientation,
	$show_numbers ? 'domio-steps--numbered' : 'domio-steps--plain',
	function_exists( 'domio_get_section_classes' )
		? domio_get_section_classes( $attributes )
		: 'domio-bg--surface',
);

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $classes ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-steps__inner">
		<?php if ( $heading ) : ?>
			<h2 class="domio-steps__heading"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $steps ) ) : ?>
			<ol class="domio-steps__list">
				<?php foreach ( $steps as $step ) : ?>
					<?php
					if ( ! is_array( $step ) ) {
						continue;
					}

					$title      = isset( $step['title'] ) ? $step['title'] : '';
					$text       = isset( $step['text'] ) ? $step['text'] : '';
					$time_label = isset( $step['timeLabel'] ) ? $step['timeLabel'] : '';

					if ( ! $title && ! $text && ! $time_label ) {
						continue;
					}
					?>
					<li class="domio-steps__item">
						<div class="domio-steps__content">
							<?php if ( $title ) : ?>
								<h3 class="domio-steps__title"><?php echo wp_kses_post( $title ); ?></h3>
							<?php endif; ?>

							<?php if ( $text ) : ?>
								<p class="domio-steps__text"><?php echo wp_kses_post( $text ); ?></p>
							<?php endif; ?>

							<?php if ( $time_label ) : ?>
								<span class="domio-steps__time"><?php echo esc_html( $time_label ); ?></span>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
