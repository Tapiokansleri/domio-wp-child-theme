<?php
/**
 * Local author photos instead of Gravatar.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User meta key for a media library avatar attachment.
 */
define( 'DOMIO_AVATAR_META', 'domio_avatar_id' );

/**
 * Resolve a user ID from get_avatar's $id_or_email argument.
 *
 * @param mixed $id_or_email User ID, email, or user/comment/post object.
 * @return int
 */
function domio_avatar_user_id( $id_or_email ) {
	if ( $id_or_email instanceof WP_User ) {
		return (int) $id_or_email->ID;
	}

	if ( $id_or_email instanceof WP_Post ) {
		return (int) $id_or_email->post_author;
	}

	if ( $id_or_email instanceof WP_Comment ) {
		if ( ! empty( $id_or_email->user_id ) ) {
			return (int) $id_or_email->user_id;
		}
		$id_or_email = $id_or_email->comment_author_email;
	}

	if ( is_numeric( $id_or_email ) ) {
		return (int) $id_or_email;
	}

	if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		$user = get_user_by( 'email', $id_or_email );
		return $user ? (int) $user->ID : 0;
	}

	return 0;
}

/**
 * Attachment ID stored as a user's local avatar.
 *
 * @param int $user_id User ID.
 * @return int
 */
function domio_get_user_avatar_id( $user_id ) {
	$user_id = (int) $user_id;

	if ( $user_id < 1 ) {
		return 0;
	}

	return (int) get_user_meta( $user_id, DOMIO_AVATAR_META, true );
}

/**
 * Prefer the local photo over Gravatar.
 *
 * @param array $args        Avatar args.
 * @param mixed $id_or_email Identifier passed to get_avatar().
 * @return array
 */
function domio_pre_get_avatar_data( $args, $id_or_email ) {
	if ( ! empty( $args['force_default'] ) ) {
		return $args;
	}

	$user_id = domio_avatar_user_id( $id_or_email );
	$image_id = domio_get_user_avatar_id( $user_id );

	if ( $image_id < 1 ) {
		return $args;
	}

	$size     = isset( $args['size'] ) ? (int) $args['size'] : 96;
	$img_size = $size >= 96 ? 'medium' : 'thumbnail';
	$url      = wp_get_attachment_image_url( $image_id, $img_size );

	if ( ! $url ) {
		$url = wp_get_attachment_image_url( $image_id, 'full' );
	}

	if ( ! $url ) {
		return $args;
	}

	$args['url']          = $url;
	$args['found_avatar'] = true;

	return $args;
}
add_filter( 'pre_get_avatar_data', 'domio_pre_get_avatar_data', 10, 2 );

/**
 * Avatar field on the user profile screen.
 *
 * @param WP_User $user User.
 * @return void
 */
function domio_user_avatar_field( $user ) {
	if ( ! $user instanceof WP_User ) {
		return;
	}

	$image_id = domio_get_user_avatar_id( $user->ID );
	$preview  = $image_id ? wp_get_attachment_image( $image_id, 'thumbnail' ) : '';
	?>
	<h2><?php esc_html_e( 'Domio-profiilikuva', 'domio' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th>
				<label for="domio_avatar_id"><?php esc_html_e( 'Kuva mediakirjastosta', 'domio' ); ?></label>
			</th>
			<td>
				<?php if ( $preview ) : ?>
					<p><?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image. ?></p>
				<?php endif; ?>
				<input type="number" min="0" class="small-text" id="domio_avatar_id" name="domio_avatar_id" value="<?php echo esc_attr( (string) $image_id ); ?>" />
				<p class="description"><?php esc_html_e( 'Liitteen ID mediakirjastosta. Käytetään blogin hero- ja tekijäsivuilla Gravatarin sijaan.', 'domio' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'domio_user_avatar_field' );
add_action( 'edit_user_profile', 'domio_user_avatar_field' );

/**
 * Save the local avatar attachment ID.
 *
 * @param int $user_id User ID.
 * @return void
 */
function domio_save_user_avatar_field( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( ! isset( $_POST['domio_avatar_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$image_id = absint( wp_unslash( $_POST['domio_avatar_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( $image_id > 0 ) {
		update_user_meta( $user_id, DOMIO_AVATAR_META, $image_id );
		return;
	}

	delete_user_meta( $user_id, DOMIO_AVATAR_META );
}
add_action( 'personal_options_update', 'domio_save_user_avatar_field' );
add_action( 'edit_user_profile_update', 'domio_save_user_avatar_field' );
