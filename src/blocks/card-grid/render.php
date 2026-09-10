<?php
/**
 * Domio Card Grid block render.
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

$heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$columns = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
$cards   = isset( $attributes['cards'] ) && is_array( $attributes['cards'] ) ? $attributes['cards'] : array();
$variant = function_exists( 'domio_get_variant_from_attributes' )
	? domio_get_variant_from_attributes( $attributes, array( 'service', 'reason', 'reference', 'value' ), 'service' )
	: ( isset( $attributes['variant'] ) ? $attributes['variant'] : 'service' );

if ( ! in_array( $columns, array( 2, 3, 4 ), true ) ) {
	$columns = 3;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-card-grid domio-card-grid--' . $variant . ' ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' ),
		'style' => function_exists( 'domio_get_section_style' )
			? domio_get_section_style( $attributes, 3, '--domio-card-columns:' . $columns . ';' )
			: '--domio-card-columns:' . $columns . ';',
	)
);
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-card-grid__inner">
		<?php if ( $heading ) : ?>
			<h2 class="domio-card-grid__heading"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $intro ) : ?>
			<p class="domio-card-grid__intro"><?php echo wp_kses_post( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="domio-card-grid__grid">
				<?php foreach ( $cards as $card ) : ?>
					<?php
					if ( ! is_array( $card ) ) {
						continue;
					}

					$icon         = isset( $card['icon'] ) ? $card['icon'] : '';
					$image_id     = isset( $card['imageId'] ) ? (int) $card['imageId'] : 0;
					$title        = isset( $card['title'] ) ? $card['title'] : '';
					$text         = isset( $card['text'] ) ? $card['text'] : '';
					$link_url     = isset( $card['linkUrl'] ) ? $card['linkUrl'] : '';
					$link_text    = isset( $card['linkText'] ) ? $card['linkText'] : '';
					$quote_author = isset( $card['quoteAuthor'] ) ? $card['quoteAuthor'] : '';
					$quote_meta   = isset( $card['quoteMeta'] ) ? $card['quoteMeta'] : '';
					$rows         = isset( $card['rows'] ) && is_array( $card['rows'] ) ? $card['rows'] : array();
					$featured     = ! empty( $card['featured'] );

					// Skip empty reference placeholders so they are never published by accident.
					if ( 'reference' === $variant && ! $text && ! $quote_author && ! $quote_meta && ! $image_id ) {
						continue;
					}

					$image_html = '';
					if ( $image_id > 0 ) {
						$image_html = wp_get_attachment_image(
							$image_id,
							'medium_large',
							false,
							array(
								'class'    => 'domio-card-grid__image',
								'loading'  => 'lazy',
								'decoding' => 'async',
								'alt'      => '',
							)
						);
					}

					$card_class = 'domio-card-grid__card';
					if ( $image_html ) {
						$card_class .= ' domio-card-grid__card--has-media';
					}
					if ( $featured ) {
						$card_class .= ' domio-card-grid__card--featured';
					}
					?>
					<article class="<?php echo esc_attr( $card_class ); ?>">
						<?php if ( $image_html ) : ?>
							<div class="domio-card-grid__media" aria-hidden="true">
								<?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?>
							</div>
						<?php endif; ?>

						<?php if ( 'reference' !== $variant && $icon ) : ?>
							<span class="domio-card-grid__icon">
								<?php
								echo domio_get_icon_svg( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
									$icon
								);
								?>
							</span>
						<?php endif; ?>

						<?php if ( 'reference' === $variant ) : ?>
							<blockquote class="domio-card-grid__quote">
								<?php if ( $text ) : ?>
									<p class="domio-card-grid__text"><?php echo wp_kses_post( $text ); ?></p>
								<?php endif; ?>
								<?php if ( $quote_author || $quote_meta ) : ?>
									<cite class="domio-card-grid__cite">
										<?php
										if ( $quote_author ) {
											echo esc_html( $quote_author );
										}
										if ( $quote_author && $quote_meta ) {
											echo ', ';
										}
										if ( $quote_meta ) {
											echo esc_html( $quote_meta );
										}
										?>
									</cite>
								<?php endif; ?>
							</blockquote>
						<?php else : ?>
							<?php if ( $title ) : ?>
								<h3 class="domio-card-grid__card-title"><?php echo wp_kses_post( $title ); ?></h3>
							<?php endif; ?>

							<?php if ( ! empty( $rows ) ) : ?>
								<dl class="domio-card-grid__rows">
									<?php foreach ( $rows as $row ) : ?>
										<?php
										if ( ! is_array( $row ) ) {
											continue;
										}
										$label      = isset( $row['label'] ) ? $row['label'] : '';
										$value      = isset( $row['value'] ) ? $row['value'] : '';
										$row_class  = 'domio-card-grid__row';
										if ( ! empty( $row['total'] ) ) {
											$row_class .= ' domio-card-grid__row--total';
										}
										if ( '' === $label && '' === $value ) {
											continue;
										}
										?>
										<div class="<?php echo esc_attr( $row_class ); ?>">
											<dt><?php echo wp_kses_post( $label ); ?></dt>
											<dd><?php echo wp_kses_post( $value ); ?></dd>
										</div>
									<?php endforeach; ?>
								</dl>
							<?php elseif ( $text ) : ?>
								<p class="domio-card-grid__text"><?php echo wp_kses_post( $text ); ?></p>
							<?php endif; ?>

							<?php if ( $link_url && $link_text ) : ?>
								<a class="domio-card-grid__link" href="<?php echo esc_url( $link_url ); ?>">
									<?php echo esc_html( $link_text ); ?>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
