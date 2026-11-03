<?php
/**
 * Domio footer template. Replaces Hello Elementor's footer.php, which printed the Elementor Theme Builder
 * footer; the footer is now the theme's own (inc/footer.php).
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

domio_render_footer();
?>

<?php wp_footer(); ?>

</body>
</html>
