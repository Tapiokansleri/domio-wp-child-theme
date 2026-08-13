<?php
/**
 * Author archive — Domio sivupohja.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$author      = get_queried_object();
$author_id   = ( $author instanceof WP_User ) ? (int) $author->ID : (int) get_query_var( 'author' );
$author_name = $author_id ? get_the_author_meta( 'display_name', $author_id ) : '';
$author_bio  = $author_id ? get_the_author_meta( 'description', $author_id ) : '';
$post_count  = $author_id ? (int) count_user_posts( $author_id, 'post', true ) : 0;
$avatar      = $author_id
	? get_avatar(
		$author_id,
		120,
		'',
		$author_name,
		array(
			'class' => 'domio-author__avatar',
		)
	)
	: '';
?>

<main id="content" class="site-main domio-author">
	<div class="domio-author__inner">
		<header class="domio-author__profile">
			<?php if ( $avatar ) : ?>
				<div class="domio-author__avatar-wrap" aria-hidden="true">
					<?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar. ?>
				</div>
			<?php endif; ?>

			<div class="domio-author__profile-text">
				<p class="domio-author__eyebrow"><?php esc_html_e( 'Tekijä', 'domio' ); ?></p>
				<h1 class="domio-author__title"><?php echo esc_html( $author_name ); ?></h1>

				<?php if ( $author_bio ) : ?>
					<div class="domio-author__bio">
						<?php echo wp_kses_post( wpautop( $author_bio ) ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $post_count > 0 ) : ?>
					<p class="domio-author__count">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d: number of published posts */
								_n( '%d artikkeli', '%d artikkelia', $post_count, 'domio' ),
								$post_count
							)
						);
						?>
					</p>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="domio-author__posts">
				<?php
				while ( have_posts() ) :
					the_post();

					$post_id      = get_the_ID();
					$permalink    = get_permalink();
					$has_thumb    = has_post_thumbnail( $post_id );
					$word_count   = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
					$reading_mins = $word_count > 0 ? max( 1, (int) ceil( $word_count / 200 ) ) : 0;
					?>
					<article <?php post_class( 'domio-author__post' ); ?>>
						<h2 class="domio-author__post-title">
							<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
						</h2>

						<?php if ( $has_thumb ) : ?>
							<a class="domio-author__post-media" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
								<?php
								the_post_thumbnail(
									'large',
									array(
										'class'   => 'domio-author__post-image',
										'loading' => 'lazy',
									)
								);
								?>
							</a>
						<?php endif; ?>

						<div class="domio-author__post-meta">
							<time class="domio-author__post-date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
								<?php echo esc_html( get_the_date() ); ?>
							</time>
							<?php if ( $reading_mins > 0 ) : ?>
								<span class="domio-author__post-meta-sep" aria-hidden="true">·</span>
								<span class="domio-author__post-reading">
									<?php
									echo esc_html(
										sprintf(
											/* translators: %d: estimated reading time in minutes */
											_n( '%d min', '%d min', $reading_mins, 'domio' ),
											$reading_mins
										)
									);
									?>
								</span>
							<?php endif; ?>
						</div>

						<?php if ( has_excerpt() || get_the_content() ) : ?>
							<div class="domio-author__post-excerpt">
								<?php the_excerpt(); ?>
							</div>
						<?php endif; ?>

						<p class="domio-author__post-more">
							<a class="domio-author__read-more" href="<?php echo esc_url( $permalink ); ?>">
								<?php esc_html_e( 'Lue artikkeli', 'domio' ); ?>
								<span aria-hidden="true"> →</span>
							</a>
						</p>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Edellinen', 'domio' ),
					'next_text' => __( 'Seuraava', 'domio' ),
					'class'     => 'domio-author__pagination',
				)
			);
			?>
		<?php else : ?>
			<p class="domio-author__empty">
				<?php esc_html_e( 'Tällä tekijällä ei ole vielä julkaistuja artikkeleita.', 'domio' ); ?>
			</p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
