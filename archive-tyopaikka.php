<?php
/**
 * Archive: Avoimet työpaikat.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$count = isset( $wp_query ) ? (int) $wp_query->found_posts : 0;
?>

<main id="content" class="site-main domio-jobs">
	<div class="domio-jobs__inner">
		<header class="domio-jobs__head">
			<h1 class="domio-jobs__title"><?php echo esc_html( domio_jobs_copy( 'archive_title' ) ); ?></h1>
			<p class="domio-jobs__count">
				<?php
				$one  = domio_jobs_copy( 'archive_count_one' );
				$many = domio_jobs_copy( 'archive_count_many' );
				echo esc_html( sprintf( 1 === $count ? $one : $many, $count ) );
				?>
			</p>
		</header>

		<p class="domio-jobs__lead">
			<?php echo esc_html( domio_jobs_copy( 'archive_lead' ) ); ?>
		</p>

		<?php if ( have_posts() ) : ?>
			<div class="domio-jobs__list">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/tyopaikka', 'card' );
				endwhile;
				?>
			</div>
		<?php else : ?>
			<p class="domio-jobs__empty">
				<?php echo esc_html( domio_jobs_copy( 'archive_empty' ) ); ?>
			</p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
