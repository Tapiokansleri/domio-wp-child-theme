<?php
/**
 * Single job listing. Add a form in the editor with a shortcode if needed.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = get_the_ID();
$rows    = function_exists( 'domio_tyopaikka_detail_rows' ) ? domio_tyopaikka_detail_rows( $post_id ) : array();
?>
<article <?php post_class( 'domio-job' ); ?>>
	<p class="domio-job__eyebrow">
		<a href="<?php echo esc_url( get_post_type_archive_link( DOMIO_JOB_POST_TYPE ) ); ?>">
			<?php echo esc_html( domio_jobs_copy( 'single_eyebrow' ) ); ?>
		</a>
	</p>
	<h1 class="domio-job__title"><?php the_title(); ?></h1>

	<?php if ( ! empty( $rows ) ) : ?>
		<dl class="domio-job__facts">
			<?php foreach ( $rows as $row ) : ?>
				<div class="domio-job__fact">
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
		<figure class="domio-job__media">
			<?php
			the_post_thumbnail(
				'large',
				array(
					'class'   => 'domio-job__image',
					'loading' => 'eager',
				)
			);
			?>
		</figure>
	<?php endif; ?>

	<div class="domio-job__content is-layout-flow">
		<?php the_content(); ?>
	</div>
</article>
