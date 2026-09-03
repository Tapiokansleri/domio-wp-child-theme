<?php
/**
 * Compact job card for the archive list.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = get_the_ID();
$permalink = get_permalink();
$rows      = function_exists( 'domio_tyopaikka_detail_rows' ) ? domio_tyopaikka_detail_rows( $post_id ) : array();
$arrow     = function_exists( 'domio_tyopaikka_arrow_icon' ) ? domio_tyopaikka_arrow_icon() : '';
?>
<article <?php post_class( 'domio-job-card' ); ?>>
	<h2 class="domio-job-card__title">
		<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
	</h2>

	<p class="domio-job-card__cta">
		<a class="domio-job-card__link" href="<?php echo esc_url( $permalink ); ?>">
			<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG helper. ?>
			<?php echo esc_html( domio_jobs_copy( 'card_cta' ) ); ?>
		</a>
	</p>

	<div class="domio-job-card__body">
		<?php if ( ! empty( $rows ) ) : ?>
			<dl class="domio-job-card__facts">
				<?php foreach ( $rows as $row ) : ?>
					<div class="domio-job-card__fact">
						<dt><?php echo esc_html( $row['label'] ); ?></dt>
						<dd>
							<?php echo esc_html( $row['value'] ); ?>
							<?php if ( ! empty( $row['badge'] ) ) : ?>
								<span class="domio-job-card__badge"><?php echo esc_html( $row['badge'] ); ?></span>
							<?php endif; ?>
						</dd>
					</div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>

		<?php if ( has_post_thumbnail() ) : ?>
			<a class="domio-job-card__logo" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
				<?php
				the_post_thumbnail(
					'medium',
					array(
						'class'   => 'domio-job-card__logo-img',
						'loading' => 'lazy',
					)
				);
				?>
			</a>
		<?php endif; ?>
	</div>

	<?php if ( has_excerpt() || get_the_content() ) : ?>
		<div class="domio-job-card__excerpt">
			<?php echo wp_kses_post( wpautop( get_the_excerpt() ) ); ?>
			<p class="domio-job-card__more">
				<span aria-hidden="true">[…] </span>
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( domio_jobs_copy( 'card_read_more' ) ); ?></a>
			</p>
		</div>
	<?php endif; ?>
</article>
