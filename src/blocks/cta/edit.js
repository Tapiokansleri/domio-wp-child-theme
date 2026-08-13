/**
 * Domio CTA block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import {
	useVariantBlockStyle,
	withStyleSlug,
} from '../../shared/block-style';
import { DomioTemplateNotice } from '../../shared/template-notice';

const CTA_VARIANTS = [ 'band', 'form' ];

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		heading,
		text,
		ctaText,
		ctaUrl,
		phone,
		formShortcode,
		variant,
		background,
		pattern,
		patternOpacity,
		className,
	} = attributes;

	useVariantBlockStyle( {
		className,
		variant,
		allowed: CTA_VARIANTS,
		fallback: 'band',
		setAttributes,
	} );

	const blockProps = useBlockProps( {
		className: `domio-cta domio-cta--${ variant } alignfull ${ getSectionClasses(
			background,
			pattern,
			'green',
			'1'
		) }`,
		style: getPatternStyle( patternOpacity, 100 ),
	} );

	const isForm = variant === 'form';

	return (
		<>
			<DomioTemplateNotice />
			<BackgroundControls
				value={ background }
				onChange={ ( value ) => setAttributes( { background: value } ) }
				fallback="green"
				pattern={ pattern }
				onPatternChange={ ( value ) => setAttributes( { pattern: value } ) }
				patternFallback="1"
				patternOpacity={ patternOpacity }
				onPatternOpacityChange={ ( value ) =>
					setAttributes( { patternOpacity: value } )
				}
				patternOpacityFallback={ 100 }
			/>
			<InspectorControls>
				<PanelBody title={ __( 'Asettelu', 'domio' ) } initialOpen={ true }>
					<SelectControl
						label={ __( 'Variantti', 'domio' ) }
						value={ variant }
						options={ [
							{ label: __( 'Väli-CTA', 'domio' ), value: 'band' },
							{ label: __( 'Lomake-CTA', 'domio' ), value: 'form' },
						] }
						onChange={ ( value ) => {
							if ( value === 'form' ) {
								setAttributes( {
									variant: value,
									className: withStyleSlug( className, value ),
									heading:
										'Kerro meille kiinteistöstäsi, niin me hoidamme loput',
									text: 'Olitpa isännöitsijä, hallituksen puheenjohtaja tai kiinteistön omistaja, saat meiltä selkeän tarjouksen kiinteistösi hoitoon. Kartoitus on maksuton eikä sido mihinkään.',
									ctaText: 'Pyydä maksuton kartoitus',
									ctaUrl: '#tarjous',
								} );
								return;
							}

							setAttributes( {
								variant: value,
								className: withStyleSlug( className, value ),
								heading:
									'Haluatko tietää mitä kiinteistösi huolto maksaisi?',
								text: 'Laske suuntaa-antava arvio muutamassa sekunnissa.',
								ctaText: 'Laske hinta-arvio',
								ctaUrl: '/hintalaskuri/',
							} );
						} }
					/>
				</PanelBody>

				{ ! isForm ? (
					<PanelBody title={ __( 'CTA-linkki', 'domio' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'CTA-teksti', 'domio' ) }
							value={ ctaText }
							onChange={ ( value ) =>
								setAttributes( { ctaText: value } )
							}
						/>
						<TextControl
							label={ __( 'CTA-URL', 'domio' ) }
							value={ ctaUrl }
							onChange={ ( value ) => setAttributes( { ctaUrl: value } ) }
							type="url"
						/>
					</PanelBody>
				) : (
					<PanelBody title={ __( 'Lomake', 'domio' ) } initialOpen={ true }>
						<TextareaControl
							label={ __( 'Lomakkeen shortcode', 'domio' ) }
							value={ formShortcode }
							onChange={ ( value ) =>
								setAttributes( { formShortcode: value } )
							}
							help={ __(
								'Esim. [metform form_id="270"]',
								'domio'
							) }
						/>
						<TextControl
							label={ __( 'Puhelinnumero', 'domio' ) }
							value={ phone }
							onChange={ ( value ) => setAttributes( { phone: value } ) }
							help={ __( 'Valinnainen tel:-linkki.', 'domio' ) }
						/>
					</PanelBody>
				) }
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-cta__inner">
					<div className="domio-cta__copy">
						<RichText
							tagName="h2"
							className="domio-cta__heading"
							value={ heading }
							onChange={ ( value ) => setAttributes( { heading: value } ) }
							placeholder={ __( 'Toimintakehotteen otsikko…', 'domio' ) }
							allowedFormats={ [] }
						/>
						<RichText
							tagName="p"
							className="domio-cta__text"
							value={ text }
							onChange={ ( value ) => setAttributes( { text: value } ) }
							placeholder={ __( 'Lyhyt johdantoteksti…', 'domio' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
						{ ! isForm && ctaText ? (
							<span className="domio-cta__link">{ ctaText }</span>
						) : null }
						{ isForm && phone ? (
							<p className="domio-cta__phone-wrap">
								<span className="domio-cta__phone-label">
									{ __( 'Tai soita', 'domio' ) }
								</span>
								<a
									className="domio-cta__phone"
									href={ `tel:${ phone.replace( /\s+/g, '' ) }` }
								>
									{ phone }
								</a>
							</p>
						) : null }
					</div>

					{ isForm ? (
						<div className="domio-cta__panel">
							{ formShortcode ? (
								<div className="domio-cta__form-preview">
									<code>{ formShortcode }</code>
								</div>
							) : (
								<p className="domio-cta-editor__empty">
									{ __(
										'Lisää lomakkeen shortcode sivupalkista.',
										'domio'
									) }
								</p>
							) }
						</div>
					) : null }
				</div>
			</section>
		</>
	);
}
