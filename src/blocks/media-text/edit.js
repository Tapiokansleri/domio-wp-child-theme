/**
 * Domio Media Text block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	RangeControl,
	Button,
	Notice,
} from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

const ALLOWED_BLOCKS = [
	'core/paragraph',
	'core/heading',
	'core/list',
	'core/table',
	'core/buttons',
	'core/quote',
	'domio/steps',
];

const TEMPLATE = [
	[
		'core/paragraph',
		{
			placeholder:
				'Kirjoita 3 - 5 kappaletta, jotka koskevat vain tätä aluetta. Käsittele: alueen rakennuskanta ja sen ikä, talvikunnossapidon erityispiirteet, pysäköinti ja lumensiirto, jätehuollon käytännöt, kaupunginosat joissa toimitaan. Älä kirjoita mitään, mikä pätisi yhtä hyvin toiseen kaupunkiin.',
		},
	],
];

const LAYOUTS = [ 'default', 'narrow' ];

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		heading,
		layout,
		mediaPosition,
		mediaId,
		mediaUrl,
		mediaAlt,
		mediaWidth,
		background,
		pattern,
		patternOpacity,
	} = attributes;

	const normalizedLayout = LAYOUTS.includes( layout ) ? layout : 'default';
	const isNarrow = normalizedLayout === 'narrow';
	const missingAlt = Boolean( mediaId && ! mediaAlt );

	const blockProps = useBlockProps( {
		className: [
			'domio-media-text',
			isNarrow
				? 'domio-media-text--narrow'
				: `domio-media-text--media-${ mediaPosition }`,
			getSectionClasses( background, pattern ),
		].join( ' ' ),
		style: {
			...getPatternStyle( patternOpacity ),
			...( isNarrow
				? {}
				: { '--domio-media-width': `${ mediaWidth }%` } ),
		},
	} );

	const mediaPicker = ( open ) => (
		<button
			type="button"
			className="domio-media-text__media-placeholder"
			onClick={ open }
		>
			{ __( 'Valitse kuva', 'domio' ) }
		</button>
	);

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
				<PanelBody title={ __( 'Asettelu', 'domio' ) } initialOpen={ true }>
					<SelectControl
						label={ __( 'Tyyli', 'domio' ) }
						value={ normalizedLayout }
						options={ [
							{
								label: __( 'Vakiomalli (teksti + media)', 'domio' ),
								value: 'default',
							},
							{
								label: __( 'Narrow (teksti, sitten kuva)', 'domio' ),
								value: 'narrow',
							},
						] }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
					{ ! isNarrow ? (
						<>
							<SelectControl
								label={ __( 'Median sijainti', 'domio' ) }
								value={ mediaPosition }
								options={ [
									{
										label: __( 'Vasemmalla', 'domio' ),
										value: 'left',
									},
									{
										label: __( 'Oikealla', 'domio' ),
										value: 'right',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { mediaPosition: value } )
								}
							/>
							<RangeControl
								label={ __( 'Median leveys (%)', 'domio' ) }
								value={ mediaWidth }
								onChange={ ( value ) =>
									setAttributes( { mediaWidth: value } )
								}
								min={ 30 }
								max={ 70 }
							/>
						</>
					) : null }
				</PanelBody>

				<PanelBody title={ __( 'Media', 'domio' ) } initialOpen={ true }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									mediaId: media.id,
									mediaUrl: media.url,
									mediaAlt: media.alt || '',
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ mediaId }
							render={ ( { open } ) => (
								<div className="domio-media-text-editor__media">
									{ mediaUrl ? (
										<img
											src={ mediaUrl }
											alt={ mediaAlt || '' }
										/>
									) : null }
									<Button variant="secondary" onClick={ open }>
										{ mediaId
											? __( 'Vaihda kuva', 'domio' )
											: __( 'Valitse kuva', 'domio' ) }
									</Button>
									{ mediaId ? (
										<Button
											isDestructive
											variant="link"
											onClick={ () =>
												setAttributes( {
													mediaId: 0,
													mediaUrl: '',
													mediaAlt: '',
												} )
											}
										>
											{ __( 'Poista kuva', 'domio' ) }
										</Button>
									) : null }
								</div>
							) }
						/>
					</MediaUploadCheck>
					{ mediaId ? (
						<TextControl
							label={ __( 'Alt-teksti', 'domio' ) }
							value={ mediaAlt }
							onChange={ ( value ) =>
								setAttributes( { mediaAlt: value } )
							}
							help={
								missingAlt
									? __(
											'Lisää alt-teksti saavutettavuutta varten.',
											'domio'
									  )
									: undefined
							}
						/>
					) : null }
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				{ missingAlt ? (
					<Notice status="warning" isDismissible={ false }>
						{ __( 'Mediasta puuttuu alt-teksti.', 'domio' ) }
					</Notice>
				) : null }

				<div className="domio-media-text__inner">
					<div className="domio-media-text__content">
						<RichText
							tagName="h2"
							className="domio-media-text__heading"
							value={ heading }
							onChange={ ( value ) =>
								setAttributes( { heading: value } )
							}
							placeholder={ __(
								'Kiinteistöhuolto [alueella]',
								'domio'
							) }
							allowedFormats={ [] }
						/>
						<div className="domio-media-text__body">
							<InnerBlocks
								allowedBlocks={ ALLOWED_BLOCKS }
								template={ TEMPLATE }
								templateLock={ false }
							/>
						</div>
					</div>

					<div className="domio-media-text__media">
						{ mediaUrl ? (
							<img
								src={ mediaUrl }
								alt={ mediaAlt || '' }
								className="domio-media-text__image"
							/>
						) : (
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) =>
										setAttributes( {
											mediaId: media.id,
											mediaUrl: media.url,
											mediaAlt: media.alt || '',
										} )
									}
									allowedTypes={ [ 'image' ] }
									value={ mediaId }
									render={ ( { open } ) => mediaPicker( open ) }
								/>
							</MediaUploadCheck>
						) }
					</div>
				</div>
			</section>
		</>
	);
}
