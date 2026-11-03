<?php
/**
 * Google Analytics 4 for domio.fi (moved from Elementor Pro Custom Code snippets 2471 and 2470).
 *
 * The cookie consent plugin keeps the tag from running before consent and sends the Consent Mode signals;
 * this file only prints the tag. It is printed on the production domain only, so local and staging copies
 * never send visits to the live property.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DOMIO_GA4_ID = 'G-CDQJ1WMKL9';

/**
 * Whether the GA4 tag is printed: only on domio.fi, unless the filter says otherwise.
 *
 * @return bool
 */
function domio_analytics_enabled() {
	$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	// While Elementor Pro is still active, its Custom Code snippets print the same tag; printing it here too
	// would count every page view twice.
	$enabled = in_array( $host, array( 'domio.fi', 'www.domio.fi' ), true ) && ! defined( 'ELEMENTOR_PRO_VERSION' );
	return (bool) apply_filters( 'domio_analytics_enabled', $enabled );
}

/**
 * GA4 tag. A visit arriving from the hosting control panel (hostingpalvelu.fi) is not counted as a referral.
 *
 * @return void
 */
function domio_print_ga4_tag() {
	if ( is_admin() || ! domio_analytics_enabled() ) {
		return;
	}
	?>
<!-- Google Analytics 4 (domio.fi). Suostumustila ja tagin esto ennen suostumusta: evästebanneri-lisäosa. -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( DOMIO_GA4_ID ); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){ dataLayer.push(arguments); }
  gtag("js", new Date());
  var domioReferrerHost = "";
  try { domioReferrerHost = new URL(document.referrer).hostname; } catch (e) {}
  gtag("config", "<?php echo esc_js( DOMIO_GA4_ID ); ?>", /(^|\.)hostingpalvelu\.fi$/.test(domioReferrerHost) ? { ignore_referrer: "true" } : {});
</script>
	<?php
}
add_action( 'wp_head', 'domio_print_ga4_tag', 5 );

/**
 * GA4 events phone_click and email_click for tel: and mailto: links (delegated, so it covers every link).
 *
 * @return void
 */
function domio_print_contact_click_events() {
	if ( is_admin() || ! domio_analytics_enabled() ) {
		return;
	}
	?>
<script id="domio-contact-click-events">
(function () {
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('a[href]');
    if (!a || typeof window.gtag !== 'function') return;
    var href = a.getAttribute('href') || '';
    var name = href.indexOf('tel:') === 0 ? 'phone_click' : (href.indexOf('mailto:') === 0 ? 'email_click' : '');
    if (name) window.gtag('event', name, { link_url: href, link_text: (a.textContent || '').trim() });
  }, true);
})();
</script>
	<?php
}
add_action( 'wp_footer', 'domio_print_contact_click_events', 99 );
