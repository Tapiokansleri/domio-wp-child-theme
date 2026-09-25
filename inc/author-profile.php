<?php
/**
 * Author profiles: expert fields, author archive query and Person schema.
 *
 * Job title, expertise and credentials are stored as user meta. The author
 * archive shows them, and Yoast's Person schema carries them as jobTitle,
 * knowsAbout and hasCredential so articles credit a named expert.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editable author profile fields.
 *
 * @return array<string, array{label: string, description: string, type: string}>
 */
function domio_author_profile_fields() {
	return array(
		'domio_job_title'   => array(
			'label'       => __( 'Tehtävänimike', 'domio' ),
			'description' => __( 'Esim. Operatiivinen johtaja. Näkyy tekijäsivulla ja rakenteisessa datassa (jobTitle).', 'domio' ),
			'type'        => 'text',
		),
		'domio_expertise'   => array(
			'label'       => __( 'Asiantuntemus', 'domio' ),
			'description' => __( 'Yksi aihe per rivi. Näkyy tekijäsivulla ja rakenteisessa datassa (knowsAbout).', 'domio' ),
			'type'        => 'lines',
		),
		'domio_credentials' => array(
			'label'       => __( 'Koulutus ja pätevyydet', 'domio' ),
			'description' => __( 'Yksi tutkinto, sertifikaatti tai kortti per rivi. Kirjaa vain tiedot, jotka voi todentaa.', 'domio' ),
			'type'        => 'lines',
		),
	);
}

/**
 * Non-empty lines from a multi-line author field.
 *
 * @param int    $user_id  User ID.
 * @param string $meta_key Meta key.
 * @return string[]
 */
function domio_get_author_lines( $user_id, $meta_key ) {
	$raw   = (string) get_user_meta( (int) $user_id, $meta_key, true );
	$lines = array_map( 'trim', preg_split( '/\R/u', $raw ) );

	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * Everything the author archive and schema need about one author.
 *
 * @param int $user_id User ID.
 * @return array<string, mixed>
 */
function domio_get_author_profile( $user_id ) {
	$user_id = (int) $user_id;
	$user    = $user_id ? get_userdata( $user_id ) : false;

	if ( ! $user ) {
		return array();
	}

	$company = class_exists( 'WPSEO_Options' ) ? (string) WPSEO_Options::get( 'company_name', '' ) : '';

	$about   = get_page_by_path( 'tietoja-meista' );
	$contact = get_page_by_path( 'yhteystiedot' );

	$latest = get_posts(
		array(
			'author'         => $user_id,
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'fields'         => 'ids',
		)
	);

	return array(
		'id'          => $user_id,
		'name'        => $user->display_name,
		'job_title'   => trim( (string) get_user_meta( $user_id, 'domio_job_title', true ) ),
		'company'     => '' !== $company ? $company : get_bloginfo( 'name' ),
		'company_url' => $about ? get_permalink( $about ) : home_url( '/' ),
		'contact_url' => $contact ? get_permalink( $contact ) : '',
		'bio'         => (string) get_the_author_meta( 'description', $user_id ),
		'expertise'   => domio_get_author_lines( $user_id, 'domio_expertise' ),
		'credentials' => domio_get_author_lines( $user_id, 'domio_credentials' ),
		'linkedin'    => (string) get_the_author_meta( 'linkedin', $user_id ),
		'post_count'  => (int) count_user_posts( $user_id, 'post', true ),
		'updated'     => $latest ? (int) $latest[0] : 0,
	);
}

/**
 * Author fields on the user profile screen, below the profile photo.
 *
 * @param WP_User $user User.
 * @return void
 */
function domio_author_profile_fields_ui( $user ) {
	if ( ! $user instanceof WP_User ) {
		return;
	}
	?>
	<h2><?php esc_html_e( 'Domio-kirjoittajaprofiili', 'domio' ); ?></h2>
	<p><?php esc_html_e( 'Näkyy tekijäsivulla ja artikkelien rakenteisessa datassa. Esittely kirjoitetaan yllä olevaan Elämäkerta-kenttään.', 'domio' ); ?></p>
	<table class="form-table" role="presentation">
		<?php foreach ( domio_author_profile_fields() as $key => $field ) : ?>
			<?php $value = (string) get_user_meta( $user->ID, $key, true ); ?>
			<tr>
				<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
				<td>
					<?php if ( 'lines' === $field['type'] ) : ?>
						<textarea id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="5" class="large-text"><?php echo esc_textarea( $value ); ?></textarea>
					<?php else : ?>
						<input type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
					<?php endif; ?>
					<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}
add_action( 'show_user_profile', 'domio_author_profile_fields_ui', 20 );
add_action( 'edit_user_profile', 'domio_author_profile_fields_ui', 20 );

/**
 * Save author fields. Core verifies the profile nonce before these hooks run.
 *
 * @param int $user_id User ID.
 * @return void
 */
function domio_save_author_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	foreach ( domio_author_profile_fields() as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			continue;
		}

		$raw   = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$value = 'lines' === $field['type'] ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );

		if ( '' === trim( $value ) ) {
			delete_user_meta( $user_id, $key );
			continue;
		}

		update_user_meta( $user_id, $key, $value );
	}
}
add_action( 'personal_options_update', 'domio_save_author_profile_fields' );
add_action( 'edit_user_profile_update', 'domio_save_author_profile_fields' );

/**
 * Twelve articles per author page fills the three-column card grid evenly.
 *
 * @param WP_Query $query Query.
 * @return void
 */
function domio_author_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_author() ) {
		return;
	}

	$query->set( 'posts_per_page', 12 );
}
add_action( 'pre_get_posts', 'domio_author_archive_query' );

/**
 * Add role, employer, expertise and credentials to Yoast's Person schema.
 *
 * @param array $data    Person schema data.
 * @param int   $user_id User ID.
 * @return array
 */
function domio_author_person_schema( $data, $user_id ) {
	$profile = domio_get_author_profile( $user_id );

	if ( ! $profile ) {
		return $data;
	}

	if ( '' !== $profile['job_title'] ) {
		$data['jobTitle'] = $profile['job_title'];
	}

	if ( class_exists( 'WPSEO_Options' ) && 'company' === WPSEO_Options::get( 'company_or_person', '' ) ) {
		$data['worksFor'] = array( '@id' => trailingslashit( home_url() ) . '#organization' );
	}

	if ( $profile['expertise'] ) {
		$data['knowsAbout'] = $profile['expertise'];
	}

	if ( $profile['credentials'] ) {
		$data['hasCredential'] = array_map(
			static function ( $credential ) {
				return array(
					'@type' => 'EducationalOccupationalCredential',
					'name'  => $credential,
				);
			},
			$profile['credentials']
		);
	}

	return $data;
}
add_filter( 'wpseo_schema_person_data', 'domio_author_person_schema', 10, 2 );
