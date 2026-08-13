<?php
/**
 * Domio CTA block render.
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
$text           = isset( $attributes['text'] ) ? $attributes['text'] : '';
$cta_text       = isset( $attributes['ctaText'] ) ? $attributes['ctaText'] : '';
$cta_url        = isset( $attributes['ctaUrl'] ) ? $attributes['ctaUrl'] : '';
$phone          = isset( $attributes['phone'] ) ? $attributes['phone'] : '';
$form_shortcode = isset( $attributes['formShortcode'] ) ? $attributes['formShortcode'] : '';
$variant        = function_exists( 'domio_get_variant_from_attributes' )
	? domio_get_variant_from_attributes( $attributes, array( 'band', 'form' ), 'band' )
	: ( isset( $attributes['variant'] ) ? $attributes['variant'] : 'band' );

if ( 'form' === $variant && ( '' === $form_shortcode || false !== strpos( $form_shortcode, 'form_id="967"' ) ) ) {
	$form_shortcode = '[metform form_id="270"]';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-cta domio-cta--' . $variant . ' alignfull ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes, 'green', '1' ) : 'domio-bg--green domio-pattern--1' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes, 100 ) : '',
	)
);

$phone_href = $phone ? 'tel:' . preg_replace( '/\s+/', '', $phone ) : '';
$is_form    = ( 'form' === $variant );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-cta__inner">
		<div class="domio-cta__copy">
			<?php if ( $heading ) : ?>
				<h2 class="domio-cta__heading"><?php echo wp_kses_post( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="domio-cta__text"><?php echo wp_kses_post( $text ); ?></p>
			<?php endif; ?>

			<?php if ( ! $is_form && $cta_text && $cta_url ) : ?>
				<a class="domio-cta__link" href="<?php echo esc_url( $cta_url ); ?>">
					<?php echo esc_html( $cta_text ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $is_form && $phone && $phone_href ) : ?>
				<p class="domio-cta__phone-wrap">
					<span class="domio-cta__phone-label"><?php echo esc_html__( 'Tai soita', 'domio' ); ?></span>
					<a class="domio-cta__phone" href="<?php echo esc_url( $phone_href ); ?>">
						<?php echo esc_html( $phone ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( $is_form && $form_shortcode ) : ?>
			<div class="domio-cta__panel">
				<div class="domio-cta__form">
					<?php echo do_shortcode( $form_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode output. ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
