<?php
/**
 * Single job listing.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="content" class="site-main domio-jobs">
	<div class="domio-jobs__inner">
		<?php
		while ( have_posts() ) :
			the_post();
			$domio_job_heading_link = false;
			get_template_part( 'template-parts/tyopaikka' );
		endwhile;
		?>
	</div>
</main>

<?php
get_footer();
