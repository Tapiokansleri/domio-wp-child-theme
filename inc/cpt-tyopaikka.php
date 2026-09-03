<?php
/**
 * Avoimet työpaikat custom post type.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DOMIO_JOB_POST_TYPE = 'tyopaikka';

/**
 * Registers the job listing post type.
 *
 * @return void
 */
function domio_register_tyopaikka_cpt() {
	$labels = array(
		'name'               => __( 'Avoimet työpaikat', 'domio' ),
		'singular_name'      => __( 'Työpaikka', 'domio' ),
		'add_new'            => __( 'Lisää uusi', 'domio' ),
		'add_new_item'       => __( 'Lisää työpaikka', 'domio' ),
		'edit_item'          => __( 'Muokkaa työpaikkaa', 'domio' ),
		'new_item'           => __( 'Uusi työpaikka', 'domio' ),
		'view_item'          => __( 'Näytä työpaikka', 'domio' ),
		'view_items'         => __( 'Näytä työpaikat', 'domio' ),
		'search_items'       => __( 'Hae työpaikkoja', 'domio' ),
		'not_found'          => __( 'Työpaikkoja ei löytynyt.', 'domio' ),
		'not_found_in_trash' => __( 'Roskakorissa ei ole työpaikkoja.', 'domio' ),
		'all_items'          => __( 'Kaikki työpaikat', 'domio' ),
		'archives'           => __( 'Avoimet työpaikat', 'domio' ),
		'menu_name'          => __( 'Avoimet työpaikat', 'domio' ),
	);

	register_post_type(
		DOMIO_JOB_POST_TYPE,
		array(
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'has_archive'         => 'avoimet-tyopaikat',
			'rewrite'             => array(
				'slug'       => 'avoimet-tyopaikat',
				'with_front' => false,
			),
			'menu_icon'           => 'dashicons-businessperson',
			'menu_position'       => 25,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'capability_type'     => 'post',
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
		)
	);
}
add_action( 'init', 'domio_register_tyopaikka_cpt' );

/**
 * Flushes rewrite rules after the theme is switched on.
 *
 * @return void
 */
function domio_tyopaikka_flush_rewrites() {
	domio_register_tyopaikka_cpt();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'domio_tyopaikka_flush_rewrites' );

/**
 * One-time permalink flush after this CPT is added to an existing site.
 *
 * @return void
 */
function domio_tyopaikka_maybe_flush_rewrites() {
	if ( get_option( 'domio_tyopaikka_rewrite_flushed' ) ) {
		return;
	}

	domio_register_tyopaikka_cpt();
	flush_rewrite_rules();
	update_option( 'domio_tyopaikka_rewrite_flushed', DOMIO_THEME_VERSION );
}
add_action( 'init', 'domio_tyopaikka_maybe_flush_rewrites', 20 );

/**
 * Show every open job on the archive.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function domio_tyopaikka_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( DOMIO_JOB_POST_TYPE ) ) {
		return;
	}

	$query->set( 'posts_per_page', -1 );
	$query->set( 'orderby', 'menu_order date' );
	$query->set( 'order', 'DESC' );
}
add_action( 'pre_get_posts', 'domio_tyopaikka_archive_query' );

/**
 * Keep Elementor Theme Builder from replacing job templates.
 *
 * @param bool   $need     Whether Elementor should override.
 * @param string $location Theme location.
 * @return bool
 */
function domio_tyopaikka_skip_elementor_location( $need, $location ) {
	unset( $location );

	if ( is_singular( DOMIO_JOB_POST_TYPE ) || is_post_type_archive( DOMIO_JOB_POST_TYPE ) ) {
		return false;
	}

	return $need;
}
add_filter( 'elementor/theme/need_override_location', 'domio_tyopaikka_skip_elementor_location', 10, 2 );

/**
 * Force the theme job templates even if a plugin swaps template_include.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function domio_tyopaikka_template_include( $template ) {
	if ( is_singular( DOMIO_JOB_POST_TYPE ) ) {
		$found = locate_template( 'single-tyopaikka.php' );
		return $found ? $found : $template;
	}

	if ( is_post_type_archive( DOMIO_JOB_POST_TYPE ) ) {
		$found = locate_template( 'archive-tyopaikka.php' );
		return $found ? $found : $template;
	}

	return $template;
}
add_filter( 'template_include', 'domio_tyopaikka_template_include', 99 );

/**
 * Employment type choices.
 *
 * @return array<string, string>
 */
function domio_tyopaikka_employment_types() {
	return array(
		'full_time'  => __( 'Kokoaikainen', 'domio' ),
		'part_time'  => __( 'Osa-aikainen', 'domio' ),
		'temporary'  => __( 'Määräaikainen', 'domio' ),
		'internship' => __( 'Harjoittelu', 'domio' ),
		'open'       => __( 'Avoin hakemus', 'domio' ),
	);
}

/**
 * Job listing meta defaults.
 *
 * @return array<string, string>
 */
function domio_tyopaikka_meta_defaults() {
	return array(
		'company'         => '',
		'location'        => '',
		'salary'          => '',
		'employment_type' => 'full_time',
		'deadline'        => '',
	);
}

/**
 * Reads sanitized job meta.
 *
 * @param int $post_id Post ID.
 * @return array<string, string>
 */
function domio_get_tyopaikka_meta( $post_id ) {
	$saved = get_post_meta( $post_id, '_domio_tyopaikka', true );

	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	return wp_parse_args( $saved, domio_tyopaikka_meta_defaults() );
}

/**
 * Registers the job details metabox.
 *
 * @return void
 */
function domio_tyopaikka_add_metabox() {
	add_meta_box(
		'domio-tyopaikka-details',
		__( 'Työpaikan tiedot', 'domio' ),
		'domio_tyopaikka_render_metabox',
		DOMIO_JOB_POST_TYPE,
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'domio_tyopaikka_add_metabox' );

/**
 * Renders the job details metabox.
 *
 * @param WP_Post $post Post.
 * @return void
 */
function domio_tyopaikka_render_metabox( $post ) {
	$meta = domio_get_tyopaikka_meta( $post->ID );
	wp_nonce_field( 'domio_tyopaikka_meta', 'domio_tyopaikka_meta_nonce' );
	?>
	<p>
		<label for="domio_tyopaikka_company"><?php esc_html_e( 'Yritys', 'domio' ); ?></label>
		<input type="text" class="widefat" id="domio_tyopaikka_company" name="domio_tyopaikka[company]" value="<?php echo esc_attr( $meta['company'] ); ?>" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
	</p>
	<p>
		<label for="domio_tyopaikka_location"><?php esc_html_e( 'Paikkakunta', 'domio' ); ?></label>
		<input type="text" class="widefat" id="domio_tyopaikka_location" name="domio_tyopaikka[location]" value="<?php echo esc_attr( $meta['location'] ); ?>" />
	</p>
	<p>
		<label for="domio_tyopaikka_salary"><?php esc_html_e( 'Palkka', 'domio' ); ?></label>
		<input type="text" class="widefat" id="domio_tyopaikka_salary" name="domio_tyopaikka[salary]" value="<?php echo esc_attr( $meta['salary'] ); ?>" placeholder="<?php esc_attr_e( 'esim. 3 200–3 800 € / kk', 'domio' ); ?>" />
	</p>
	<p>
		<label for="domio_tyopaikka_employment_type"><?php esc_html_e( 'Tyyppi', 'domio' ); ?></label>
		<select class="widefat" id="domio_tyopaikka_employment_type" name="domio_tyopaikka[employment_type]">
			<?php foreach ( domio_tyopaikka_employment_types() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $meta['employment_type'], $value ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="domio_tyopaikka_deadline"><?php esc_html_e( 'Viim. hakupäivä', 'domio' ); ?></label>
		<input type="date" class="widefat" id="domio_tyopaikka_deadline" name="domio_tyopaikka[deadline]" value="<?php echo esc_attr( $meta['deadline'] ); ?>" />
	</p>
	<p class="description"><?php esc_html_e( 'Hakulomake lisätään ilmoituksen sisältöön shortcodella, esim. [metform form_id="270"].', 'domio' ); ?></p>
	<?php
}

/**
 * Saves job details metabox.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function domio_tyopaikka_save_metabox( $post_id ) {
	if ( ! isset( $_POST['domio_tyopaikka_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['domio_tyopaikka_meta_nonce'] ) ), 'domio_tyopaikka_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( DOMIO_JOB_POST_TYPE !== get_post_type( $post_id ) ) {
		return;
	}

	$raw   = isset( $_POST['domio_tyopaikka'] ) && is_array( $_POST['domio_tyopaikka'] ) ? wp_unslash( $_POST['domio_tyopaikka'] ) : array();
	$types = array_keys( domio_tyopaikka_employment_types() );

	$clean = array(
		'company'         => isset( $raw['company'] ) ? sanitize_text_field( $raw['company'] ) : '',
		'location'        => isset( $raw['location'] ) ? sanitize_text_field( $raw['location'] ) : '',
		'salary'          => isset( $raw['salary'] ) ? sanitize_text_field( $raw['salary'] ) : '',
		'employment_type' => isset( $raw['employment_type'] ) && in_array( $raw['employment_type'], $types, true ) ? $raw['employment_type'] : 'full_time',
		'deadline'        => isset( $raw['deadline'] ) ? sanitize_text_field( $raw['deadline'] ) : '',
	);

	if ( $clean['deadline'] && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $clean['deadline'] ) ) {
		$clean['deadline'] = '';
	}

	update_post_meta( $post_id, '_domio_tyopaikka', $clean );
}
add_action( 'save_post_' . DOMIO_JOB_POST_TYPE, 'domio_tyopaikka_save_metabox' );

/**
 * Whether the current request is a job archive or single.
 *
 * @return bool
 */
function domio_is_tyopaikka_template() {
	return is_singular( DOMIO_JOB_POST_TYPE ) || is_post_type_archive( DOMIO_JOB_POST_TYPE );
}

/**
 * Label:value rows for a job listing card.
 *
 * @param int $post_id Job post ID.
 * @return array<int, array{label:string,value:string,badge?:string}>
 */
function domio_tyopaikka_detail_rows( $post_id ) {
	$meta    = domio_get_tyopaikka_meta( $post_id );
	$company = '' !== $meta['company'] ? $meta['company'] : (string) get_bloginfo( 'name' );
	$types   = domio_tyopaikka_employment_types();
	$rows    = array();

	if ( '' !== $company ) {
		$rows[] = array(
			'label' => function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'label_company' ) : __( 'Yritys', 'domio' ),
			'value' => $company,
		);
	}

	if ( '' !== $meta['location'] ) {
		$rows[] = array(
			'label' => function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'label_location' ) : __( 'Paikkakunta', 'domio' ),
			'value' => $meta['location'],
		);
	}

	if ( '' !== $meta['salary'] ) {
		$rows[] = array(
			'label' => function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'label_salary' ) : __( 'Palkka', 'domio' ),
			'value' => $meta['salary'],
		);
	}

	if ( isset( $types[ $meta['employment_type'] ] ) ) {
		$rows[] = array(
			'label' => function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'label_type' ) : __( 'Tyyppi', 'domio' ),
			'value' => $types[ $meta['employment_type'] ],
		);
	}

	if ( '' !== $meta['deadline'] ) {
		$ts = strtotime( $meta['deadline'] . ' 12:00:00' );
		if ( $ts ) {
			$row = array(
				'label' => function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'label_deadline' ) : __( 'Viim. hakupäivä', 'domio' ),
				'value' => date_i18n( get_option( 'date_format' ), $ts ),
			);

			$badge = domio_tyopaikka_deadline_badge( $ts );
			if ( '' !== $badge ) {
				$row['badge'] = $badge;
			}

			$rows[] = $row;
		}
	}

	return $rows;
}

/**
 * Remaining-time badge for a deadline.
 *
 * @param int $timestamp Deadline timestamp.
 * @return string
 */
function domio_tyopaikka_deadline_badge( $timestamp ) {
	$now  = (int) current_time( 'timestamp' );
	$days = (int) floor( ( $timestamp - $now ) / DAY_IN_SECONDS );

	if ( $days < 0 ) {
		return function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'badge_ended' ) : __( 'Haku päättynyt', 'domio' );
	}

	if ( 0 === $days ) {
		return function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'badge_last_day' ) : __( 'Viimeinen päivä', 'domio' );
	}

	$one  = function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'badge_days_one' ) : __( '%d päivä jäljellä', 'domio' );
	$many = function_exists( 'domio_jobs_copy' ) ? domio_jobs_copy( 'badge_days_many' ) : __( '%d päivää jäljellä', 'domio' );

	return sprintf( 1 === $days ? $one : $many, $days );
}

/**
 * Circular arrow icon used in listing links.
 *
 * @return string
 */
function domio_tyopaikka_arrow_icon() {
	return '<span class="domio-jobs-arrow" aria-hidden="true"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="11" fill="currentColor"/><path d="M10 8l4 4-4 4" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
}
