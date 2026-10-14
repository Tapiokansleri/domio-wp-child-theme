<?php
/**
 * Domio Trustmary block render.
 *
 * Two modes: "reviews" writes the reviews into the page as text (read on the server, see
 * inc/trustmary.php, so no Trustmary script runs for the visitor); "widget" embeds the
 * Trustmary widget script. Reviews mode falls back to the widget if the reviews cannot be read.
 *
 * @package Domio
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$widget_src = isset( $attributes['widgetSrc'] ) ? (string) $attributes['widgetSrc'] : '';

if ( '' === $widget_src ) {
	$widget_src = 'https://widget.trustmary.com/n3eIUlhS5';
}

$allowed_host = 'widget.trustmary.com';
$parsed       = wp_parse_url( $widget_src );
$is_valid     = is_array( $parsed )
	&& ! empty( $parsed['scheme'] )
	&& 'https' === strtolower( $parsed['scheme'] )
	&& ! empty( $parsed['host'] )
	&& 0 === strcasecmp( $parsed['host'], $allowed_host );

$mode         = isset( $attributes['mode'] ) && 'reviews' === $attributes['mode'] ? 'reviews' : 'widget';
$heading      = isset( $attributes['heading'] ) ? trim( (string) $attributes['heading'] ) : '';
$limit        = isset( $attributes['limit'] ) ? max( 0, (int) $attributes['limit'] ) : 6;
$show_summary = ! isset( $attributes['showSummary'] ) || ! empty( $attributes['showSummary'] );

$data = ( 'reviews' === $mode && $is_valid && function_exists( 'domio_trustmary_reviews' ) ) ? domio_trustmary_reviews( $widget_src ) : null;
if ( 'reviews' === $mode && empty( $data['reviews'] ) ) {
	$mode = 'widget';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'domio-trustmary domio-trustmary--' . $mode . ' alignfull ' . ( function_exists( 'domio_get_section_classes' ) ? domio_get_section_classes( $attributes ) : 'domio-bg--surface' ),
		'style' => function_exists( 'domio_get_section_style' ) ? domio_get_section_style( $attributes ) : '',
	)
);

// Rating with one decimal in the site's number format, e.g. 4,5.
$domio_rating_text = static function ( $value ) {
	return number_format_i18n( (float) $value, 1 );
};
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="domio-trustmary__inner">
		<?php if ( 'reviews' === $mode ) : ?>
			<?php
			$reviews = $data['reviews'];
			// "longest" is for a text section next to the Trustmary widget: it leaves out the three
			// newest reviews the widget carousel opens with and shows the most detailed of the rest.
			if ( isset( $attributes['order'] ) && 'longest' === $attributes['order'] ) {
				if ( count( $reviews ) > 3 + max( 1, $limit ) ) {
					$reviews = array_slice( $reviews, 3 );
				}
				usort(
					$reviews,
					static function ( $a, $b ) {
						return mb_strlen( $b['text'] ) <=> mb_strlen( $a['text'] );
					}
				);
			}
			$reviews = $limit > 0 ? array_slice( $reviews, 0, $limit ) : $reviews;
			$sources = array_map( 'domio_trustmary_source_label', $data['sources'] );
			?>
			<?php if ( '' !== $heading ) : ?>
				<h2 class="domio-trustmary__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $show_summary && $data['count'] > 0 && $data['average'] > 0 ) : ?>
				<p class="domio-trustmary__summary">
					<span class="domio-trustmary__stars" style="--domio-stars: <?php echo esc_attr( $data['average'] ); ?>" aria-hidden="true"></span>
					<?php
					echo esc_html(
						sprintf(
							/* translators: 1: average rating, e.g. 4,9. 2: number of reviews. 3: review sites. */
							__( 'Keskiarvo %1$s / 5, %2$d arviota (%3$s)', 'domio' ),
							$domio_rating_text( $data['average'] ),
							$data['count'],
							wp_sprintf_l( '%l', $sources )
						)
					);
					?>
				</p>
			<?php endif; ?>

			<ul class="domio-trustmary__reviews">
				<?php foreach ( $reviews as $review ) : ?>
					<?php
					$meta = array_filter( array( $review['organization'], $review['location'] ) );
					$time = $review['date'] ? strtotime( $review['date'] ) : false;
					?>
					<li class="domio-trustmary__review">
						<?php if ( $review['rating'] > 0 ) : ?>
							<span class="domio-trustmary__stars" style="--domio-stars: <?php echo esc_attr( $review['rating'] ); ?>" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: rating, e.g. 4,5 */ __( 'Arvio %s / 5', 'domio' ), $domio_rating_text( $review['rating'] ) ) ); ?>"></span>
						<?php endif; ?>
						<blockquote class="domio-trustmary__text">
							<p><?php echo esc_html( $review['text'] ); ?></p>
						</blockquote>
						<p class="domio-trustmary__author">
							<strong><?php echo esc_html( $review['name'] ); ?></strong>
							<?php if ( $meta ) : ?>
								<span><?php echo esc_html( implode( ', ', $meta ) ); ?></span>
							<?php endif; ?>
							<span class="domio-trustmary__source">
								<?php echo esc_html( domio_trustmary_source_label( $review['source'] ) ); ?>
								<?php if ( $time ) : ?>
									<time datetime="<?php echo esc_attr( $review['date'] ); ?>"><?php echo esc_html( wp_date( 'j.n.Y', $time ) ); ?></time>
								<?php endif; ?>
							</span>
						</p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $is_valid ) : ?>
			<div class="domio-trustmary__widget">
				<script src="<?php echo esc_url( $widget_src ); ?>"></script>
			</div>
		<?php endif; ?>
	</div>
</section>
