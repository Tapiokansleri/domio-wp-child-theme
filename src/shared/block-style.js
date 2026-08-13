/**
 * Keep Gutenberg block styles (is-style-*) in sync with the variant attribute.
 */
import { useEffect, useRef } from '@wordpress/element';

/**
 * @param {string}   className Extra class names from the block.
 * @param {string[]} allowed   Allowed style slugs.
 * @return {string} Style slug or empty string.
 */
export function getStyleSlug( className, allowed ) {
	const match = String( className || '' ).match(
		/(?:^|\s)is-style-([a-z0-9-]+)(?:\s|$)/
	);

	if ( match && allowed.includes( match[ 1 ] ) ) {
		return match[ 1 ];
	}

	return '';
}

/**
 * @param {string} className Extra class names from the block.
 * @param {string} slug      Style slug to apply.
 * @return {string} Class list with a single is-style-* class.
 */
export function withStyleSlug( className, slug ) {
	const cleaned = String( className || '' )
		.replace( /(?:^|\s)is-style-[a-z0-9-]+(?=\s|$)/g, ' ' )
		.replace( /\s+/g, ' ' )
		.trim();

	if ( ! slug ) {
		return cleaned;
	}

	return [ cleaned, `is-style-${ slug }` ].filter( Boolean ).join( ' ' );
}

/**
 * @param {Object}   props
 * @param {string}   props.className    Block className attribute.
 * @param {string}   props.variant      Current variant attribute.
 * @param {string[]} props.allowed      Allowed style slugs.
 * @param {string}   props.fallback     Default variant.
 * @param {Function} props.setAttributes Block setAttributes.
 */
export function useVariantBlockStyle( {
	className,
	variant,
	allowed,
	fallback,
	setAttributes,
} ) {
	const didHydrate = useRef( false );
	const previousSlug = useRef( getStyleSlug( className, allowed ) );

	useEffect( () => {
		const slug = getStyleSlug( className, allowed );

		if ( ! didHydrate.current ) {
			didHydrate.current = true;

			if ( ! slug && allowed.includes( variant ) ) {
				previousSlug.current = variant;
				setAttributes( { className: withStyleSlug( className, variant ) } );
				return;
			}

			if ( slug && slug !== variant ) {
				previousSlug.current = slug;
				setAttributes( { variant: slug } );
				return;
			}

			previousSlug.current = slug || variant || fallback;
			return;
		}

		if ( slug && slug !== variant ) {
			previousSlug.current = slug;
			setAttributes( { variant: slug } );
			return;
		}

		if (
			! slug &&
			previousSlug.current &&
			previousSlug.current !== fallback &&
			variant !== fallback
		) {
			previousSlug.current = fallback;
			setAttributes( { variant: fallback } );
			return;
		}

		previousSlug.current = slug || fallback;
	}, [ allowed, className, fallback, setAttributes, variant ] );
}
