/**
 * Domio Hero block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	Button,
	Notice,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { store as coreStore } from '@wordpress/core-data';
import { dateI18n, getSettings as getDateSettings } from '@wordpress/date';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

const DEFAULT_HERO_IMAGE =
	( typeof window !== 'undefined' &&
		window.domioBlockDefaults &&
		window.domioBlockDefaults.heroImageUrl ) ||
	'';
const DEFAULT_HERO_ALT =
	( typeof window !== 'undefined' &&
		window.domioBlockDefaults &&
		window.domioBlockDefaults.heroImageAlt ) ||
	'';

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		heading,
		headingLevel,
		subheading,
		primaryCtaText,
		primaryCtaUrl,
		secondaryCtaText,
		secondaryCtaUrl,
		imageId,
		imageUrl,
		imageAlt,
		layout,
		background,
		pattern,
		patternOpacity,
	} = attributes;

	const normalizedLayout = layout === 'overlay' ? 'banner' : layout;
	const displayImageUrl = imageUrl || DEFAULT_HERO_IMAGE;
	const displayImageAlt = imageAlt || ( imageUrl ? '' : DEFAULT_HERO_ALT );

	const postMeta = useSelect( ( select ) => {
		const postType = select( editorStore ).getCurrentPostType();
		if ( postType !== 'post' ) {
			return null;
		}

		const authorId = select( editorStore ).getEditedPostAttribute( 'author' );
		const date = select( editorStore ).getEditedPostAttribute( 'date' );
		const modified = select( editorStore ).getEditedPostAttribute( 'modified' );
		const categories = select( editorStore ).getEditedPostAttribute( 'categories' ) || [];
		const author = authorId
			? select( coreStore ).getEntityRecord( 'root', 'user', authorId )
			: null;
		const categoryRecords = categories.length
			? select( coreStore ).getEntityRecords( 'taxonomy', 'category', {
					include: categories,
					per_page: categories.length,
			  } )
			: [];

		return {
			authorName: author?.name || '',
			authorAvatar: author?.avatar_urls?.[ 48 ] || author?.avatar_urls?.[ 96 ] || '',
			date,
			modified,
			categories: Array.isArray( categoryRecords ) ? categoryRecords : [],
		};
	}, [] );

	const hasOtherH1 = useSelect(
		( select ) => {
			if ( headingLevel !== 1 ) {
				return false;
			}

			const { getBlocks } = select( blockEditorStore );
			const blocks = getBlocks();

			const scan = ( list ) => {
				for ( const block of list ) {
					if ( block.clientId === clientId ) {
						continue;
					}

					if (
						block.name === 'core/heading' &&
						block.attributes?.level === 1
					) {
						return true;
					}

					if (
						block.name === 'domio/hero' &&
						( block.attributes?.headingLevel ?? 1 ) === 1 &&
						block.attributes?.heading
					) {
						return true;
					}

					if ( block.innerBlocks?.length && scan( block.innerBlocks ) ) {
						return true;
					}
				}
				return false;
			};

			return scan( blocks );
		},
		[ clientId, headingLevel ]
	);

	const missingAlt = Boolean( imageId && ! imageAlt );

	const blockProps = useBlockProps( {
		className: [
			'domio-hero',
			`domio-hero--${ normalizedLayout }`,
			postMeta ? 'domio-hero--has-meta' : '',
			getSectionClasses( background, pattern ),
		]
			.filter( Boolean )
			.join( ' ' ),
		style: getPatternStyle( patternOpacity ),
	} );

	const TagName = headingLevel === 2 ? 'h2' : 'h1';
	const dateSettings = getDateSettings();
	const dateFormat = dateSettings?.formats?.date || 'j.n.Y';
	const showModified =
		postMeta?.date &&
		postMeta?.modified &&
		postMeta.date.slice( 0, 16 ) !== postMeta.modified.slice( 0, 16 );

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
						label={ __( 'Layout', 'domio' ) }
						value={ normalizedLayout }
						options={ [
							{
								label: __( 'Banneri (taustakuva)', 'domio' ),
								value: 'banner',
							},
							{ label: __( 'Jaettu (split)', 'domio' ), value: 'split' },
							{ label: __( 'Keskitetty', 'domio' ), value: 'centered' },
						] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
					<SelectControl
						label={ __( 'Otsikkotaso', 'domio' ) }
						value={ String( headingLevel ) }
						options={ [
							{ label: 'H1', value: '1' },
							{ label: 'H2', value: '2' },
						] }
						onChange={ ( value ) =>
							setAttributes( { headingLevel: parseInt( value, 10 ) } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'CTA', 'domio' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'CTA-teksti', 'domio' ) }
						value={ primaryCtaText }
						onChange={ ( value ) =>
							setAttributes( { primaryCtaText: value } )
						}
					/>
					<TextControl
						label={ __( 'CTA-URL', 'domio' ) }
						value={ primaryCtaUrl }
						onChange={ ( value ) =>
							setAttributes( { primaryCtaUrl: value } )
						}
						type="url"
					/>
					<TextControl
						label={ __( 'Toissijainen CTA-teksti', 'domio' ) }
						value={ secondaryCtaText }
						onChange={ ( value ) =>
							setAttributes( { secondaryCtaText: value } )
						}
					/>
					<TextControl
						label={ __( 'Toissijainen CTA-URL', 'domio' ) }
						value={ secondaryCtaUrl }
						onChange={ ( value ) =>
							setAttributes( { secondaryCtaUrl: value } )
						}
						type="url"
					/>
				</PanelBody>

				<PanelBody title={ __( 'Taustakuva', 'domio' ) } initialOpen={ true }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									imageId: media.id,
									imageUrl: media.url,
									imageAlt: media.alt || '',
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ imageId }
							render={ ( { open } ) => (
								<div className="domio-hero-editor__media">
									{ displayImageUrl ? (
										<img
											src={ displayImageUrl }
											alt={ displayImageAlt || '' }
										/>
									) : null }
									<Button variant="secondary" onClick={ open }>
										{ imageId
											? __( 'Vaihda kuva', 'domio' )
											: __( 'Valitse kuva', 'domio' ) }
									</Button>
									{ imageId ? (
										<Button
											isDestructive
											variant="link"
											onClick={ () =>
												setAttributes( {
													imageId: 0,
													imageUrl: '',
													imageAlt: '',
												} )
											}
										>
											{ __( 'Palauta oletuskuva', 'domio' ) }
										</Button>
									) : null }
								</div>
							) }
						/>
					</MediaUploadCheck>
					{ imageId ? (
						<TextControl
							label={ __( 'Alt-teksti', 'domio' ) }
							value={ imageAlt }
							onChange={ ( value ) => setAttributes( { imageAlt: value } ) }
						/>
					) : null }
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				{ hasOtherH1 ? (
					<Notice status="warning" isDismissible={ false }>
						{ __(
							'Sivulla on jo H1. Vaihda tämän heron otsikkotasoksi H2 tai poista toinen H1.',
							'domio'
						) }
					</Notice>
				) : null }
				{ missingAlt ? (
					<Notice status="warning" isDismissible={ false }>
						{ __( 'Hero-kuvasta puuttuu alt-teksti.', 'domio' ) }
					</Notice>
				) : null }

				<div className="domio-hero__media">
					{ displayImageUrl ? (
						<img
							src={ displayImageUrl }
							alt={ displayImageAlt || '' }
							className="domio-hero__image"
						/>
					) : (
						<div className="domio-hero__media-placeholder">
							{ __( 'Valitse taustakuva sivupalkista', 'domio' ) }
						</div>
					) }
				</div>

				<div className="domio-hero__inner">
					<div className="domio-hero__content">
						<RichText
							tagName={ TagName }
							className="domio-hero__heading"
							value={ heading }
							onChange={ ( value ) => setAttributes( { heading: value } ) }
							placeholder={ __(
								'Kiinteistöhuolto [alue]',
								'domio'
							) }
							allowedFormats={ [] }
						/>

						<RichText
							tagName="p"
							className="domio-hero__subheading"
							value={ subheading }
							onChange={ ( value ) =>
								setAttributes( { subheading: value } )
							}
							placeholder={ __(
								'Huolehdimme kiinteistösi arjen sujuvuudesta, turvallisuudesta ja viihtyisyydestä. Kaikki palvelut saman sopimuksen alla.',
								'domio'
							) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>

						{ primaryCtaText || secondaryCtaText ? (
							<div className="domio-hero__actions">
								{ primaryCtaText ? (
									<span className="domio-hero__cta domio-hero__cta--primary">
										{ primaryCtaText }
									</span>
								) : null }
								{ secondaryCtaText ? (
									<span className="domio-hero__cta domio-hero__cta--secondary">
										{ secondaryCtaText }
									</span>
								) : null }
							</div>
						) : null }

						{ postMeta ? (
							<div className="domio-hero__meta">
								{ postMeta.authorName ? (
									<div className="domio-hero__meta-author">
										{ postMeta.authorAvatar ? (
											<img
												className="domio-hero__meta-avatar"
												src={ postMeta.authorAvatar }
												alt=""
											/>
										) : null }
										<div className="domio-hero__meta-author-text">
											<span className="domio-hero__meta-label">
												{ __( 'Kirjoittaja', 'domio' ) }
											</span>
											<span className="domio-hero__meta-author-name">
												{ postMeta.authorName }
											</span>
										</div>
									</div>
								) : null }

								<div className="domio-hero__meta-details">
									{ postMeta.date ? (
										<span className="domio-hero__meta-item">
											<span className="domio-hero__meta-label">
												{ __( 'Julkaistu', 'domio' ) }
											</span>
											<span className="domio-hero__meta-value">
												{ dateI18n( dateFormat, postMeta.date ) }
											</span>
										</span>
									) : null }
									{ showModified ? (
										<span className="domio-hero__meta-item">
											<span className="domio-hero__meta-label">
												{ __( 'Päivitetty', 'domio' ) }
											</span>
											<span className="domio-hero__meta-value">
												{ dateI18n( dateFormat, postMeta.modified ) }
											</span>
										</span>
									) : null }
								</div>

								{ postMeta.categories?.length ? (
									<ul className="domio-hero__meta-categories">
										{ postMeta.categories.map( ( category ) => (
											<li key={ category.id }>
												<span>{ category.name }</span>
											</li>
										) ) }
									</ul>
								) : null }
							</div>
						) : null }
					</div>
				</div>
			</section>
		</>
	);
}
