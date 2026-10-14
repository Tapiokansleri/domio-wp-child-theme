/**
 * Domio Trustmary block editor.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { widgetSrc, mode, heading, limit, showSummary, background, pattern, patternOpacity } =
		attributes;
	const isReviews = mode === 'reviews';

	const blockProps = useBlockProps( {
		className: `domio-trustmary alignfull ${ getSectionClasses(
			background,
			pattern
		) }`,
		style: getPatternStyle( patternOpacity ),
	} );

	return (
		<>
			<DomioTemplateNotice />
			<BackgroundControls
				value={ background }
				onChange={ ( value ) => setAttributes( { background: value } ) }
				pattern={ pattern }
				onPatternChange={ ( value ) => setAttributes( { pattern: value } ) }
				patternOpacity={ patternOpacity }
				onPatternOpacityChange={ ( value ) =>
					setAttributes( { patternOpacity: value } )
				}
			/>
			<InspectorControls>
				<PanelBody title={ __( 'Asetukset', 'domio' ) } initialOpen={ true }>
					<SelectControl
						label={ __( 'Näyttötapa', 'domio' ) }
						value={ mode }
						options={ [
							{ label: __( 'Arvostelut sivun tekstinä', 'domio' ), value: 'reviews' },
							{ label: __( 'Trustmary-widget', 'domio' ), value: 'widget' },
						] }
						onChange={ ( value ) => setAttributes( { mode: value } ) }
						help={
							isReviews
								? __(
										'Arvostelut haetaan palvelimella ja kirjoitetaan sivulle, joten hakukoneet ja tekoäly lukevat ne eikä kävijän selain lataa Trustmarya.',
										'domio'
								  )
								: __(
										'Trustmaryn oma widget latautuu kävijän selaimessa.',
										'domio'
								  )
						}
						__nextHasNoMarginBottom
					/>
					{ isReviews && (
						<>
							<TextControl
								label={ __( 'Otsikko', 'domio' ) }
								value={ heading }
								onChange={ ( value ) => setAttributes( { heading: value } ) }
								__nextHasNoMarginBottom
							/>
							<RangeControl
								label={ __( 'Arvostelujen määrä', 'domio' ) }
								value={ limit }
								onChange={ ( value ) => setAttributes( { limit: value ?? 6 } ) }
								min={ 0 }
								max={ 30 }
								help={ __( '0 näyttää kaikki arvostelut.', 'domio' ) }
								__nextHasNoMarginBottom
							/>
							<SelectControl
								label={ __( 'Järjestys', 'domio' ) }
								value={ attributes.order }
								options={ [
									{ label: __( 'Uusimmat ensin', 'domio' ), value: 'latest' },
									{ label: __( 'Kattavimmat ensin', 'domio' ), value: 'longest' },
								] }
								onChange={ ( value ) => setAttributes( { order: value } ) }
								help={ __(
									'Kattavimmat ensin on tarkoitettu Trustmary-widgetin rinnalle: se jättää pois kolme uusinta, jotka widget näyttää ensin, ja näyttää muista kattavimmat.',
									'domio'
								) }
								__nextHasNoMarginBottom
							/>
							<ToggleControl
								label={ __( 'Näytä keskiarvo', 'domio' ) }
								checked={ showSummary }
								onChange={ ( value ) => setAttributes( { showSummary: value } ) }
								__nextHasNoMarginBottom
							/>
						</>
					) }
					<TextControl
						label={ __( 'Widgetin osoite', 'domio' ) }
						value={ widgetSrc }
						onChange={ ( value ) => setAttributes( { widgetSrc: value } ) }
						type="url"
						help={ __(
							'Trustmary-widgetin script-osoite. Arvostelut luetaan tästä widgetistä.',
							'domio'
						) }
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-trustmary__inner">
					{ isReviews && heading && (
						<h2 className="domio-trustmary__heading">{ heading }</h2>
					) }
					<div className="domio-trustmary__placeholder">
						{ isReviews
							? __(
									'Arvostelut kirjoitetaan sivulle tekstinä julkaistulla sivulla.',
									'domio'
							  )
							: __( 'Trustmary-arvostelut näkyvät julkaistulla sivulla.', 'domio' ) }
					</div>
				</div>
			</section>
		</>
	);
}
