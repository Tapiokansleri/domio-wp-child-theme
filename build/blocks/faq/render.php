<?php
/**
 * Domio FAQ block render.
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
$items        = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();
$default_open = isset( $attributes['defaultOpen'] ) ? (int) $attributes['defaultOpen'] : -1;
$emit_schema  = ! isset( $attributes['emitSchema'] ) || ! empty( $attributes['emitSchema'] );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-faq ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);

$schema_items = array();

foreach ( $items as $item ) {
	if ( ! is_array( $item ) ) {
		continue;
	}

	$question = isset( $item['question'] ) ? $item['question'] : '';
	$answer   = isset( $item['answer'] ) ? $item['answer'] : '';

	if ( $question && $answer ) {
		$schema_items[] = array(
			'question' => wp_strip_all_tags( $question ),
			'answer'   => wp_strip_all_tags( $answer ),
		);
	}
}

if ( $emit_schema && ! empty( $schema_items ) && function_exists( 'domio_collect_faq_items' ) ) {
	domio_collect_faq_items( $schema_items );
}
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-faq__inner">
		<?php if ( $heading ) : ?>
			<h2 class="domio-faq__heading"><?php echo wp_kses_post( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $items ) ) : ?>
			<div class="domio-faq__list">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					if ( ! is_array( $item ) ) {
						continue;
					}

					$question = isset( $item['question'] ) ? $item['question'] : '';
					$answer   = isset( $item['answer'] ) ? $item['answer'] : '';

					if ( ! $question && ! $answer ) {
						continue;
					}
					?>
					<details
						class="domio-faq__item"
						<?php echo ( $default_open === (int) $index ) ? 'open' : ''; ?>
					>
						<summary class="domio-faq__summary">
							<?php if ( $question ) : ?>
								<h3 class="domio-faq__question"><?php echo esc_html( wp_strip_all_tags( $question ) ); ?></h3>
							<?php endif; ?>
						</summary>
						<?php if ( $answer ) : ?>
							<div class="domio-faq__answer">
								<?php echo wp_kses_post( $answer ); ?>
							</div>
						<?php endif; ?>
					</details>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
