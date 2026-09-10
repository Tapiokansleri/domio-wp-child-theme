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
	Flex,
	FlexItem,
} from '@wordpress/components';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';
import { DomioIcon, DOMIO_ICON_KEYS, DOMIO_ICON_LABELS } from '../../shared/icons';

const ALLOWED_BLOCKS = [
	'core/paragraph',
	'core/heading',
	'core/list',
	'core/table',
	'core/buttons',
	'core/quote',
	'domio/steps',
	'domio/hintalaskuri',
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
const CONTENT_WIDTHS = [ 'default', 'full' ];
const SUMMARY_MAX = 7;

const uid = ( prefix ) =>
	`${ prefix }-${ Date.now() }-${ Math.floor( Math.random() * 1000 ) }`;

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		heading,
		layout,
		contentWidth,
		mediaPosition,
		mediaId,
		mediaUrl,
		mediaAlt,
		mediaWidth,
		background,
		pattern,
		patternOpacity,
		summaryItems,
	} = attributes;

	const items = Array.isArray( summaryItems ) ? summaryItems : [];
	const hasSummary = items.some(
		( item ) => ( item && ( item.title || item.text ) )
	);
	const normalizedLayout = LAYOUTS.includes( layout ) ? layout : 'default';
	const normalizedWidth = CONTENT_WIDTHS.includes( contentWidth )
		? contentWidth
		: 'default';
	const isNarrow = normalizedLayout === 'narrow';
	const isContentFull = normalizedWidth === 'full';
	const missingAlt = Boolean( mediaId && ! mediaAlt && ! hasSummary );
	const hasAside = hasSummary || Boolean( mediaUrl );

	const blockProps = useBlockProps( {
		className: [
			'domio-media-text',
			hasSummary ? 'domio-media-text--has-summary' : '',
			isNarrow
				? 'domio-media-text--narrow'
				: hasAside
					? `domio-media-text--media-${ mediaPosition }`
					: 'domio-media-text--no-media',
			isContentFull ? 'domio-media-text--content-full' : '',
			getSectionClasses( background, pattern ),
		]
			.filter( Boolean )
			.join( ' ' ),
		style: {
			...getPatternStyle( patternOpacity ),
			...( isNarrow || ! hasAside
				? {}
				: { '--domio-media-width': `${ mediaWidth }%` } ),
		},
	} );

	const updateItem = ( index, key, value ) => {
		const next = items.map( ( item, i ) =>
			i === index ? { ...item, [ key ]: value } : item
		);
		setAttributes( { summaryItems: next } );
	};

	const addItem = () => {
		if ( items.length >= SUMMARY_MAX ) {
			return;
		}
		setAttributes( {
			summaryItems: [
				...items,
				{
					id: uid( 's' ),
					icon: 'check',
					title: '',
					text: '',
				},
			],
		} );
	};

	const removeItem = ( index ) => {
		setAttributes( {
			summaryItems: items.filter( ( _, i ) => i !== index ),
		} );
	};

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
					<SelectControl
						label={ __( 'Leveys', 'domio' ) }
						value={ normalizedWidth }
						options={ [
							{
								label: __( 'Oletus', 'domio' ),
								value: 'default',
							},
							{
								label: __( 'Täysleveä', 'domio' ),
								value: 'full',
							},
						] }
						help={ __(
							'Täysleveä käyttää sivun täyttä sisältöleveyttä. Oletus kaventaa tekstiä blogikirjoituksissa.',
							'domio'
						) }
						onChange={ ( value ) =>
							setAttributes( { contentWidth: value } )
						}
					/>
					{ ! isNarrow ? (
						<>
							<SelectControl
								label={ __( 'Sivupalkin sijainti', 'domio' ) }
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
								label={ __( 'Sivupalkin leveys (%)', 'domio' ) }
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

				<PanelBody
					title={ __( 'Ikoniyhteenveto (1–7)', 'domio' ) }
					initialOpen={ true }
				>
					<p className="domio-media-text-editor__help">
						{ __(
							'Jos lisäät kohtia, ne korvaavat kuvan oikeassa (tai vasemmassa) palstassa.',
							'domio'
						) }
					</p>
					{ items.map( ( item, index ) => (
						<div
							className="domio-media-text-editor__item"
							key={ item.id || index }
						>
							<p className="domio-media-text-editor__item-label">
								{ __( 'Kohta', 'domio' ) } { index + 1 }
							</p>
							<SelectControl
								label={ __( 'Ikoni', 'domio' ) }
								value={ item.icon || 'check' }
								options={ DOMIO_ICON_KEYS.map( ( key ) => ( {
									label: DOMIO_ICON_LABELS[ key ],
									value: key,
								} ) ) }
								onChange={ ( value ) =>
									updateItem( index, 'icon', value )
								}
							/>
							<TextControl
								label={ __( 'Otsikko', 'domio' ) }
								value={ item.title || '' }
								onChange={ ( value ) =>
									updateItem( index, 'title', value )
								}
							/>
							<TextControl
								label={ __( 'Teksti', 'domio' ) }
								value={ item.text || '' }
								onChange={ ( value ) =>
									updateItem( index, 'text', value )
								}
							/>
							<Flex>
								<FlexItem>
									<Button
										size="small"
										isDestructive
										onClick={ () => removeItem( index ) }
									>
										{ __( 'Poista', 'domio' ) }
									</Button>
								</FlexItem>
							</Flex>
						</div>
					) ) }
					{ items.length < SUMMARY_MAX ? (
						<Button variant="secondary" onClick={ addItem }>
							{ __( 'Lisää kohta', 'domio' ) }
						</Button>
					) : null }
				</PanelBody>

				{ ! hasSummary ? (
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
				) : null }
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

					{ hasSummary ? (
						<ul className="domio-media-text__summary">
							{ items.map( ( item, index ) =>
								item.title || item.text ? (
									<li
										className="domio-media-text__summary-item"
										key={ item.id || index }
									>
										<span
											className="domio-media-text__summary-icon"
											aria-hidden="true"
										>
											<DomioIcon
												name={ item.icon || 'check' }
											/>
										</span>
										<div className="domio-media-text__summary-copy">
											{ item.title ? (
												<p className="domio-media-text__summary-title">
													{ item.title }
												</p>
											) : null }
											{ item.text ? (
												<p className="domio-media-text__summary-text">
													{ item.text }
												</p>
											) : null }
										</div>
									</li>
								) : null
							) }
						</ul>
					) : (
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
										render={ ( { open } ) =>
											mediaPicker( open )
										}
									/>
								</MediaUploadCheck>
							) }
						</div>
					) }
				</div>
			</section>
		</>
	);
}
