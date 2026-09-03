<?php
/**
 * Domio JSON-LD schema assembly.
 *
 * FAQPage pieces are collected during block render and printed once
 * in wp_footer. LocalBusiness is emitted once per page. Service
 * schema is opt-in via post meta.
 *
 * @package Domio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Collected FAQ items for the current request.
 *
 * @var array<int, array{question: string, answer: string}>
 */
$GLOBALS['domio_faq_items'] = array();

/**
 * Add FAQ items to the page-level collector.
 *
 * @param array<int, array{question: string, answer: string}> $items FAQ items.
 * @return void
 */
function domio_collect_faq_items( $items ) {
	if ( empty( $items ) || ! is_array( $items ) ) {
		return;
	}

	foreach ( $items as $item ) {
		if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
			continue;
		}

		$GLOBALS['domio_faq_items'][] = array(
			'question' => $item['question'],
			'answer'   => $item['answer'],
		);
	}
}

/**
 * Whether Yoast already provides FAQPage schema on this page.
 *
 * @return bool
 */
function domio_page_has_yoast_faq_block() {
	if ( ! is_singular() ) {
		return false;
	}

	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		return false;
	}

	return has_block( 'yoast/faq-block', $post );
}

/**
 * Print aggregated Domio schema in the footer.
 *
 * @return void
 */
function domio_print_schema() {
	if ( is_admin() || ! is_singular() ) {
		return;
	}

	$graph = array();

	$local_business = domio_get_local_business_schema();
	if ( ! empty( $local_business ) ) {
		$graph[] = $local_business;
	}

	$service = domio_get_service_schema();
	if ( ! empty( $service ) ) {
		$graph[] = $service;
	}

	$job = function_exists( 'domio_get_job_posting_schema' ) ? domio_get_job_posting_schema() : array();
	if ( ! empty( $job ) ) {
		$graph[] = $job;
	}

	if ( ! empty( $GLOBALS['domio_faq_items'] ) && ! domio_page_has_yoast_faq_block() ) {
		$faq = domio_get_faq_page_schema( $GLOBALS['domio_faq_items'] );
		if ( ! empty( $faq ) ) {
			$graph[] = $faq;
		}
	}

	if ( empty( $graph ) ) {
		return;
	}

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
	);
}
add_action( 'wp_footer', 'domio_print_schema', 20 );

/**
 * Build LocalBusiness schema for the current page.
 *
 * @return array<string, mixed>
 */
function domio_get_local_business_schema() {
	$schema = array(
		'@type' => 'LocalBusiness',
		'@id'   => home_url( '/#localbusiness' ),
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);

	/**
	 * Filter LocalBusiness schema fields.
	 *
	 * @param array<string, mixed> $schema Schema piece.
	 */
	return apply_filters( 'domio_local_business_schema', $schema );
}

/**
 * Build Service schema when enabled via post meta.
 *
 * @return array<string, mixed>
 */
function domio_get_service_schema() {
	if ( ! is_singular() ) {
		return array();
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id || ! get_post_meta( $post_id, '_domio_emit_service_schema', true ) ) {
		return array();
	}

	$name = get_post_meta( $post_id, '_domio_service_name', true );
	if ( ! $name ) {
		$name = get_the_title( $post_id );
	}

	$schema = array(
		'@type'       => 'Service',
		'name'        => $name,
		'provider'    => array(
			'@id' => home_url( '/#localbusiness' ),
		),
		'url'         => get_permalink( $post_id ),
		'description' => has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '',
	);

	/**
	 * Filter Service schema fields.
	 *
	 * @param array<string, mixed> $schema  Schema piece.
	 * @param int                  $post_id Post ID.
	 */
	return apply_filters( 'domio_service_schema', $schema, $post_id );
}

/**
 * Build a single FAQPage schema object from collected items.
 *
 * @param array<int, array{question: string, answer: string}> $items FAQ items.
 * @return array<string, mixed>
 */
function domio_get_faq_page_schema( $items ) {
	$entities = array();

	foreach ( $items as $item ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['question'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_kses_post( $item['answer'] ),
			),
		);
	}

	if ( empty( $entities ) ) {
		return array();
	}

	return array(
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);
}

/**
 * JobPosting schema for a single tyopaikka.
 *
 * @return array<string, mixed>
 */
function domio_get_job_posting_schema() {
	if ( ! function_exists( 'domio_is_tyopaikka_template' ) || ! is_singular( DOMIO_JOB_POST_TYPE ) ) {
		return array();
	}

	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return array();
	}

	$meta = function_exists( 'domio_get_tyopaikka_meta' ) ? domio_get_tyopaikka_meta( $post_id ) : array();
	$map  = array(
		'full_time'  => 'FULL_TIME',
		'part_time'  => 'PART_TIME',
		'temporary'  => 'TEMPORARY',
		'internship' => 'INTERN',
		'open'       => 'OTHER',
	);

	$schema = array(
		'@type'            => 'JobPosting',
		'title'            => get_the_title( $post_id ),
		'description'      => wp_strip_all_tags( get_the_content( null, false, $post_id ) ),
		'datePosted'       => get_the_date( 'c', $post_id ),
		'url'              => get_permalink( $post_id ),
		'hiringOrganization' => array(
			'@type'  => 'Organization',
			'name'   => ! empty( $meta['company'] ) ? $meta['company'] : get_bloginfo( 'name' ),
			'sameAs' => home_url( '/' ),
		),
	);

	if ( ! empty( $meta['deadline'] ) ) {
		$schema['validThrough'] = $meta['deadline'] . 'T23:59:59';
	}

	if ( ! empty( $meta['employment_type'] ) && isset( $map[ $meta['employment_type'] ] ) ) {
		$schema['employmentType'] = $map[ $meta['employment_type'] ];
	}

	if ( ! empty( $meta['salary'] ) ) {
		$schema['baseSalary'] = array(
			'@type'    => 'MonetaryAmount',
			'currency' => 'EUR',
			'value'    => array(
				'@type' => 'QuantitativeValue',
				'name'  => $meta['salary'],
			),
		);
	}

	if ( ! empty( $meta['location'] ) ) {
		$schema['jobLocation'] = array(
			'@type'   => 'Place',
			'address' => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => $meta['location'],
				'addressCountry'  => 'FI',
			),
		);
	}

	return $schema;
}
