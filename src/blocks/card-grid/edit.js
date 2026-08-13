/**
 * Domio Card Grid block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	Button,
	Flex,
	FlexItem,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';

import { DomioIcon, DOMIO_ICON_KEYS, DOMIO_ICON_LABELS } from '../../shared/icons';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';
import {
	useVariantBlockStyle,
	withStyleSlug,
} from '../../shared/block-style';

const CARD_GRID_VARIANTS = [ 'service', 'reason', 'reference' ];

const uid = ( prefix ) =>
	`${ prefix }-${ Date.now() }-${ Math.floor( Math.random() * 1000 ) }`;

const REASON_DEFAULTS = {
	heading: 'Miksi valita Domio kiinteistöhuollon kumppaniksi?',
	intro: 'Domio on paikallinen ja joustava kumppani, joka tuntee pääkaupunkiseudun olosuhteet ja kiinteistökannan.',
	cards: [
		{
			icon: 'check',
			title: 'Kaikki yhdeltä kumppanilta',
			text: 'Huolto, siivous, pihatyöt ja LVI saman sopimuksen alla. Isännöitsijän ei tarvitse koordinoida useaa toimittajaa.',
		},
		{
			icon: 'heart',
			title: 'Tuttu yhteyshenkilö',
			text: 'Saat nimetyn kontaktihenkilön, joka tuntee kohteen ja sen erityispiirteet. Et selitä samaa asiaa joka kerta uudelleen.',
		},
		{
			icon: 'pin',
			title: 'Paikallinen läsnäolo',
			text: 'Toimipisteemme sijainti pitää vasteajat lyhyinä koko pääkaupunkiseudulla.',
		},
		{
			icon: 'clock',
			title: 'Päivystys ympäri vuorokauden',
			text: 'Äkilliset vikatilanteet hoidetaan nopeasti, jotta vahingot pysyvät pieninä.',
		},
	],
};

const REFERENCE_DEFAULTS = {
	heading: 'Asiakkaidemme kokemuksia',
	intro: '',
	cards: [ {}, {} ],
};

const SERVICE_DEFAULTS = {
	heading: 'Kiinteistöhuoltomme osa-alueet',
	intro: 'Domion kiinteistöhuolto kattaa kaikki ne osa-alueet, joista kiinteistön laadukas huolto ja ylläpito koostuu.',
};

/**
 * Resolve attachment URL for a card image preview.
 *
 * @param {number} imageId Attachment ID.
 * @return {string} Image URL.
 */
function useAttachmentUrl( imageId ) {
	return useSelect(
		( select ) => {
			if ( ! imageId ) {
				return '';
			}
			const media = select( 'core' ).getMedia( imageId );
			return media?.source_url || '';
		},
		[ imageId ]
	);
}

/**
 * Single card preview with optional media URL lookup.
 *
 * @param {Object}   props            Props.
 * @param {Object}   props.card       Card data.
 * @param {number}   props.index      Card index.
 * @param {string}   props.variant    Grid variant.
 * @param {Function} props.onChange   Update callback.
 * @return {JSX.Element} Card element.
 */
function CardPreview( { card, index, variant, onChange } ) {
	const imageUrl = useAttachmentUrl( card.imageId || 0 );
	const isReference = variant === 'reference';

	return (
		<article className="domio-card-grid__card">
			{ ! isReference && card.icon ? (
				<span className="domio-card-grid__icon">
					<DomioIcon name={ card.icon } />
				</span>
			) : null }

			{ imageUrl ? (
				<div className="domio-card-grid__media">
					<img src={ imageUrl } alt="" />
				</div>
			) : null }

			{ isReference ? (
				<blockquote className="domio-card-grid__quote">
					<RichText
						tagName="p"
						className="domio-card-grid__text"
						value={ card.text || '' }
						onChange={ ( value ) => onChange( index, 'text', value ) }
						placeholder={ __(
							'Lisää asiakaslainaus, kirjoittajan rooli ja kohdetyyppi. Käytä vain asiakkaan hyväksymiä lainauksia.',
							'domio'
						) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					{ card.quoteAuthor || card.quoteMeta ? (
						<cite className="domio-card-grid__cite">
							{ card.quoteAuthor }
							{ card.quoteAuthor && card.quoteMeta ? ', ' : '' }
							{ card.quoteMeta }
						</cite>
					) : null }
				</blockquote>
			) : (
				<>
					<RichText
						tagName="h3"
						className="domio-card-grid__card-title"
						value={ card.title || '' }
						onChange={ ( value ) => onChange( index, 'title', value ) }
						placeholder={ __( 'Kortin otsikko…', 'domio' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="p"
						className="domio-card-grid__text"
						value={ card.text || '' }
						onChange={ ( value ) => onChange( index, 'text', value ) }
						placeholder={ __( 'Kortin teksti…', 'domio' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					{ card.linkText ? (
						<span className="domio-card-grid__link">{ card.linkText }</span>
					) : null }
				</>
			) }
		</article>
	);
}

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { heading, intro, columns, cards, variant, background, pattern, patternOpacity, className } =
		attributes;

	useVariantBlockStyle( {
		className,
		variant,
		allowed: CARD_GRID_VARIANTS,
		fallback: 'service',
		setAttributes,
	} );

	const blockProps = useBlockProps( {
		className: `domio-card-grid domio-card-grid--${ variant } ${ getSectionClasses(
			background,
			pattern
		) }`,
		style: {
			...getPatternStyle( patternOpacity ),
			'--domio-card-columns': String( columns ),
		},
	} );

	const updateCard = ( index, key, value ) => {
		const next = cards.map( ( card, i ) =>
			i === index ? { ...card, [ key ]: value } : card
		);
		setAttributes( { cards: next } );
	};

	const addCard = () => {
		setAttributes( {
			cards: [
				...cards,
				{
					id: `card-${ Date.now() }`,
					icon: 'check',
					imageId: 0,
					title: '',
					text: '',
					linkUrl: '',
					linkText: '',
					quoteAuthor: '',
					quoteMeta: '',
				},
			],
		} );
	};

	const removeCard = ( index ) => {
		setAttributes( {
			cards: cards.filter( ( _, i ) => i !== index ),
		} );
	};

	const moveCard = ( index, direction ) => {
		const target = index + direction;
		if ( target < 0 || target >= cards.length ) {
			return;
		}
		const next = [ ...cards ];
		const [ item ] = next.splice( index, 1 );
		next.splice( target, 0, item );
		setAttributes( { cards: next } );
	};

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
						label={ __( 'Sarakkeet', 'domio' ) }
						value={ String( columns ) }
						options={ [
							{ label: '2', value: '2' },
							{ label: '3', value: '3' },
							{ label: '4', value: '4' },
						] }
						onChange={ ( value ) =>
							setAttributes( { columns: parseInt( value, 10 ) } )
						}
					/>
					<SelectControl
						label={ __( 'Variantti', 'domio' ) }
						value={ variant }
						options={ [
							{ label: __( 'Palvelut', 'domio' ), value: 'service' },
							{ label: __( 'Miksi Domio', 'domio' ), value: 'reason' },
							{ label: __( 'Referenssit', 'domio' ), value: 'reference' },
						] }
						onChange={ ( value ) => {
							if ( value === 'reason' ) {
								setAttributes( {
									variant: value,
									className: withStyleSlug( className, value ),
									heading: REASON_DEFAULTS.heading,
									intro: REASON_DEFAULTS.intro,
									columns: 2,
									cards: REASON_DEFAULTS.cards.map( ( card ) => ( {
										...card,
										id: uid( 'c' ),
									} ) ),
								} );
								return;
							}

							if ( value === 'reference' ) {
								setAttributes( {
									variant: value,
									className: withStyleSlug( className, value ),
									heading: REFERENCE_DEFAULTS.heading,
									intro: REFERENCE_DEFAULTS.intro,
									columns: 2,
									cards: REFERENCE_DEFAULTS.cards.map( () => ( {
										id: uid( 'c' ),
										title: '',
										text: '',
										quoteAuthor: '',
										quoteMeta: '',
									} ) ),
								} );
								return;
							}

							setAttributes( {
								variant: value,
								className: withStyleSlug( className, value ),
								heading: SERVICE_DEFAULTS.heading,
								intro: SERVICE_DEFAULTS.intro,
								columns: 3,
							} );
						} }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Kortit', 'domio' ) } initialOpen={ true }>
					{ cards.map( ( card, index ) => (
						<div
							className="domio-card-grid-editor__card"
							key={ card.id || index }
						>
							<p className="domio-card-grid-editor__card-label">
								{ __( 'Kortti', 'domio' ) } { index + 1 }
							</p>

							{ variant !== 'reference' ? (
								<SelectControl
									label={ __( 'Ikoni', 'domio' ) }
									value={ card.icon || 'check' }
									options={ DOMIO_ICON_KEYS.map( ( key ) => ( {
										label: DOMIO_ICON_LABELS[ key ],
										value: key,
									} ) ) }
									onChange={ ( value ) =>
										updateCard( index, 'icon', value )
									}
								/>
							) : null }

							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) =>
										updateCard( index, 'imageId', media.id )
									}
									allowedTypes={ [ 'image' ] }
									value={ card.imageId || 0 }
									render={ ( { open } ) => (
										<div className="domio-card-grid-editor__media">
											<Button variant="secondary" onClick={ open }>
												{ card.imageId
													? __( 'Vaihda kuva', 'domio' )
													: __( 'Valitse kuva', 'domio' ) }
											</Button>
											{ card.imageId ? (
												<Button
													isDestructive
													variant="link"
													onClick={ () =>
														updateCard( index, 'imageId', 0 )
													}
												>
													{ __( 'Poista kuva', 'domio' ) }
												</Button>
											) : null }
										</div>
									) }
								/>
							</MediaUploadCheck>

							{ variant === 'reference' ? (
								<>
									<TextControl
										label={ __( 'Lainauksen kirjoittaja', 'domio' ) }
										value={ card.quoteAuthor || '' }
										onChange={ ( value ) =>
											updateCard( index, 'quoteAuthor', value )
										}
									/>
									<TextControl
										label={ __( 'Lisätieto (esim. rooli)', 'domio' ) }
										value={ card.quoteMeta || '' }
										onChange={ ( value ) =>
											updateCard( index, 'quoteMeta', value )
										}
									/>
								</>
							) : (
								<>
									<TextControl
										label={ __( 'Linkin teksti', 'domio' ) }
										value={ card.linkText || '' }
										onChange={ ( value ) =>
											updateCard( index, 'linkText', value )
										}
									/>
									<TextControl
										label={ __( 'Linkin URL', 'domio' ) }
										value={ card.linkUrl || '' }
										onChange={ ( value ) =>
											updateCard( index, 'linkUrl', value )
										}
										type="url"
									/>
								</>
							) }

							<Flex gap={ 2 }>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === 0 }
										onClick={ () => moveCard( index, -1 ) }
									>
										{ __( 'Ylös', 'domio' ) }
									</Button>
								</FlexItem>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === cards.length - 1 }
										onClick={ () => moveCard( index, 1 ) }
									>
										{ __( 'Alas', 'domio' ) }
									</Button>
								</FlexItem>
								<FlexItem>
									<Button
										size="small"
										isDestructive
										onClick={ () => removeCard( index ) }
									>
										{ __( 'Poista', 'domio' ) }
									</Button>
								</FlexItem>
							</Flex>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addCard }>
						{ __( 'Lisää kortti', 'domio' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-card-grid__inner">
					<RichText
						tagName="h2"
						className="domio-card-grid__heading"
						value={ heading }
						onChange={ ( value ) => setAttributes( { heading: value } ) }
						placeholder={ __( 'Osion otsikko…', 'domio' ) }
						allowedFormats={ [] }
					/>
					<RichText
						tagName="p"
						className="domio-card-grid__intro"
						value={ intro }
						onChange={ ( value ) => setAttributes( { intro: value } ) }
						placeholder={ __( 'Lyhyt johdanto…', 'domio' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>

					{ cards.length > 0 ? (
						<div className="domio-card-grid__grid">
							{ cards.map( ( card, index ) => (
								<CardPreview
									key={ card.id || index }
									card={ card }
									index={ index }
									variant={ variant }
									onChange={ updateCard }
								/>
							) ) }
						</div>
					) : (
						<p className="domio-card-grid-editor__empty">
							{ __( 'Lisää kortteja sivupalkista.', 'domio' ) }
						</p>
					) }
				</div>
			</section>
		</>
	);
}
