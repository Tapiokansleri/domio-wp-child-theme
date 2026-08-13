/**
 * Shared Domio section background and pattern controls (Styles tab).
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export const DOMIO_BACKGROUNDS = [ 'surface', 'muted', 'green' ];
export const DOMIO_PATTERNS = [ 'none', '1', '2' ];
export const DOMIO_PATTERN_OPACITY_DEFAULT = 3;
export const DOMIO_PATTERN_OPACITY_GREEN = 100;

export const DOMIO_BACKGROUND_OPTIONS = [
	{ label: __( 'Valkoinen', 'domio' ), value: 'surface' },
	{ label: __( 'Vaaleanharmaa', 'domio' ), value: 'muted' },
	{ label: __( 'Tummanvihreä', 'domio' ), value: 'green' },
];

export const DOMIO_PATTERN_OPTIONS = [
	{ label: __( 'Ei patternia', 'domio' ), value: 'none' },
	{ label: __( 'Pattern 1', 'domio' ), value: '1' },
	{ label: __( 'Pattern 2', 'domio' ), value: '2' },
];

/**
 * @param {string} value    Stored background.
 * @param {string} fallback Default when value is empty or legacy.
 * @return {string} Sanitized background slug.
 */
export function sanitizeBackground( value, fallback = 'surface' ) {
	if ( value === 'none' ) {
		return 'surface';
	}
	if ( value === 'dark' ) {
		return 'green';
	}
	return DOMIO_BACKGROUNDS.includes( value ) ? value : fallback;
}

/**
 * @param {string} value    Stored pattern.
 * @param {string} fallback Default when value is empty.
 * @return {string} Sanitized pattern slug.
 */
export function sanitizePattern( value, fallback = 'none' ) {
	return DOMIO_PATTERNS.includes( value ) ? value : fallback;
}

/**
 * @param {number} value    Stored opacity 0–100.
 * @param {number} fallback Default when value is empty.
 * @return {number} Sanitized opacity.
 */
export function sanitizePatternOpacity( value, fallback = DOMIO_PATTERN_OPACITY_DEFAULT ) {
	const next = Number( value );
	if ( Number.isNaN( next ) ) {
		return fallback;
	}
	return Math.min( 100, Math.max( 0, Math.round( next ) ) );
}

/**
 * @param {string} value    Stored background.
 * @param {string} fallback Default when value is empty or legacy.
 * @return {string} CSS class.
 */
export function getBackgroundClass( value, fallback = 'surface' ) {
	return `domio-bg--${ sanitizeBackground( value, fallback ) }`;
}

/**
 * @param {string} value    Stored pattern.
 * @param {string} fallback Default when value is empty.
 * @return {string} CSS class.
 */
export function getPatternClass( value, fallback = 'none' ) {
	return `domio-pattern--${ sanitizePattern( value, fallback ) }`;
}

/**
 * Inline style for pattern opacity.
 *
 * @param {number} opacity  Stored opacity 0–100.
 * @param {number} fallback Default when value is empty.
 * @return {Object} Style object.
 */
export function getPatternStyle(
	opacity,
	fallback = DOMIO_PATTERN_OPACITY_DEFAULT
) {
	return {
		'--domio-pattern-opacity': String(
			sanitizePatternOpacity( opacity, fallback ) / 100
		),
	};
}

/**
 * Combined section style classes.
 *
 * @param {string} background Background slug.
 * @param {string} pattern    Pattern slug.
 * @param {string} bgFallback Background default.
 * @param {string} patternFallback Pattern default.
 * @return {string} Class list.
 */
export function getSectionClasses(
	background,
	pattern,
	bgFallback = 'surface',
	patternFallback = 'none'
) {
	return [
		getBackgroundClass( background, bgFallback ),
		getPatternClass( pattern, patternFallback ),
	].join( ' ' );
}

/**
 * Styles-tab radios for section background and pattern.
 *
 * @param {Object}   props                         Props.
 * @param {string}   props.value                   Current background.
 * @param {Function} props.onChange                Background change handler.
 * @param {string}   [props.fallback]              Background default.
 * @param {string}   props.pattern                 Current pattern.
 * @param {Function} props.onPatternChange         Pattern change handler.
 * @param {string}   [props.patternFallback]       Pattern default.
 * @param {number}   props.patternOpacity          Current opacity 0–100.
 * @param {Function} props.onPatternOpacityChange  Opacity change handler.
 * @param {number}   [props.patternOpacityFallback] Opacity default.
 * @return {JSX.Element} Inspector controls.
 */
export function BackgroundControls( {
	value,
	onChange,
	fallback = 'surface',
	pattern,
	onPatternChange,
	patternFallback = 'none',
	patternOpacity,
	onPatternOpacityChange,
	patternOpacityFallback = DOMIO_PATTERN_OPACITY_DEFAULT,
} ) {
	const selectedPattern = sanitizePattern( pattern, patternFallback );

	return (
		<InspectorControls group="styles">
			<PanelBody title={ __( 'Tausta', 'domio' ) } initialOpen={ true }>
				<RadioControl
					label={ __( 'Taustaväri', 'domio' ) }
					selected={ sanitizeBackground( value, fallback ) }
					options={ DOMIO_BACKGROUND_OPTIONS }
					onChange={ onChange }
				/>
				<RadioControl
					label={ __( 'Pattern', 'domio' ) }
					selected={ selectedPattern }
					options={ DOMIO_PATTERN_OPTIONS }
					onChange={ onPatternChange }
				/>
				{ selectedPattern !== 'none' ? (
					<RangeControl
						label={ __( 'Patternin opacity', 'domio' ) }
						value={ sanitizePatternOpacity(
							patternOpacity,
							patternOpacityFallback
						) }
						onChange={ ( next ) =>
							onPatternOpacityChange(
								sanitizePatternOpacity( next, patternOpacityFallback )
							)
						}
						min={ 0 }
						max={ 100 }
						help={ __(
							'Vaalealla taustalla noin 3, vihreällä noin 100.',
							'domio'
						) }
					/>
				) : null }
			</PanelBody>
		</InspectorControls>
	);
}
