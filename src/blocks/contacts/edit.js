/**
 * Domio contacts block editor.
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
	TextControl,
	TextareaControl,
	Button,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

const uid = ( prefix ) =>
	`${ prefix }-${ Date.now() }-${ Math.floor( Math.random() * 1000 ) }`;

/**
 * @param {string} phone Display phone number.
 * @return {string} tel: href.
 */
function telHref( phone ) {
	const digits = String( phone || '' ).replace( /\D+/g, '' );
	if ( ! digits ) {
		return '';
	}
	if ( digits.startsWith( '358' ) ) {
		return `tel:+${ digits }`;
	}
	if ( digits.startsWith( '0' ) ) {
		return `tel:+358${ digits.slice( 1 ) }`;
	}
	return `tel:${ digits }`;
}

/**
 * @param {string} query Maps search query.
 * @return {string} Google Maps URL.
 */
function mapHref( query ) {
	return `https://www.google.com/maps/search/?api=1&query=${ encodeURIComponent(
		query || ''
	) }`;
}

/**
 * @param {string} personId Person slug.
 * @return {string} Theme-bundled photo URL.
 */
function defaultPhotoUrl( personId ) {
	const photos =
		( typeof window !== 'undefined' &&
			window.domioBlockDefaults &&
			window.domioBlockDefaults.contactPhotos ) ||
		{};
	return photos[ personId ] || '';
}

/**
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
 * @return {JSX.Element} Map pin icon.
 */
function MapPin() {
	return (
		<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path
				d="M12 21s7-6.05 7-12a7 7 0 1 0-14 0c0 5.95 7 12 7 12Z"
				stroke="currentColor"
				strokeWidth="1.8"
				strokeLinecap="round"
				strokeLinejoin="round"
			/>
			<circle
				cx="12"
				cy="9"
				r="2.5"
				stroke="currentColor"
				strokeWidth="1.8"
			/>
		</svg>
	);
}

/**
 * Person photo that falls back to the theme default.
 *
 * @param {Object} props        Props.
 * @param {Object} props.person Person data.
 * @return {JSX.Element|null} Image or nothing.
 */
function PersonPhoto( { person } ) {
	const uploaded = useAttachmentUrl( person.imageId || 0 );
	const src = uploaded || defaultPhotoUrl( person.id );
	if ( ! src ) {
		return null;
	}

	return (
		<img
			className="domio-contacts__photo"
			src={ src }
			alt={ person.imageAlt || person.name || '' }
		/>
	);
}

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		heading,
		serviceHours,
		servicePhone,
		urgentHeading,
		serviceBlocks,
		serviceNote,
		areas,
		people,
		invoiceHeading,
		invoiceRows,
		background,
		pattern,
		patternOpacity,
	} = attributes;

	const blockProps = useBlockProps( {
		className: `domio-contacts ${ getSectionClasses( background, pattern ) }`,
		style: getPatternStyle( patternOpacity ),
	} );

	const updateArea = ( index, key, value ) => {
		const next = areas.map( ( area, i ) =>
			i === index ? { ...area, [ key ]: value } : area
		);
		setAttributes( { areas: next } );
	};

	const updatePerson = ( index, key, value ) => {
		const next = people.map( ( person, i ) =>
			i === index ? { ...person, [ key ]: value } : person
		);
		setAttributes( { people: next } );
	};

	const updateInvoice = ( index, key, value ) => {
		const next = invoiceRows.map( ( row, i ) =>
			i === index ? { ...row, [ key ]: value } : row
		);
		setAttributes( { invoiceRows: next } );
	};

	const updateServiceBlock = ( index, key, value ) => {
		const next = serviceBlocks.map( ( block, i ) =>
			i === index ? { ...block, [ key ]: value } : block
		);
		setAttributes( { serviceBlocks: next } );
	};

	const addArea = () => {
		setAttributes( {
			areas: [
				...areas,
				{
					id: uid( 'area' ),
					label: '',
					street: '',
					city: '',
					mapQuery: '',
				},
			],
		} );
	};

	const removeArea = ( index ) => {
		setAttributes( {
			areas: areas.filter( ( _, i ) => i !== index ),
		} );
	};

	const addPerson = () => {
		setAttributes( {
			people: [
				...people,
				{
					id: uid( 'person' ),
					section: '',
					name: '',
					role: '',
					phone: '',
					email: '',
					imageId: 0,
					imageAlt: '',
				},
			],
		} );
	};

	const removePerson = ( index ) => {
		setAttributes( {
			people: people.filter( ( _, i ) => i !== index ),
		} );
	};

	const addInvoiceRow = () => {
		setAttributes( {
			invoiceRows: [
				...invoiceRows,
				{ id: uid( 'inv' ), label: '', value: '', href: '' },
			],
		} );
	};

	const removeInvoiceRow = ( index ) => {
		setAttributes( {
			invoiceRows: invoiceRows.filter( ( _, i ) => i !== index ),
		} );
	};

	const addServiceBlock = () => {
		setAttributes( {
			serviceBlocks: [
				...serviceBlocks,
				{
					id: uid( 'service' ),
					title: '',
					phone: '',
					email: '',
					description: '',
				},
			],
		} );
	};

	const removeServiceBlock = ( index ) => {
		setAttributes( {
			serviceBlocks: serviceBlocks.filter( ( _, i ) => i !== index ),
		} );
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
				<PanelBody title={ __( 'Palvelu ja päivystys', 'domio' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Akuutti-otsikko', 'domio' ) }
						value={ urgentHeading }
						onChange={ ( value ) =>
							setAttributes( { urgentHeading: value } )
						}
					/>
					{ serviceBlocks.map( ( block, index ) => (
						<div
							className="domio-contacts-editor__item"
							key={ block.id || index }
						>
							<p className="domio-contacts-editor__label">
								{ block.title ||
									`${ __( 'Palvelu', 'domio' ) } ${ index + 1 }` }
							</p>
							<TextControl
								label={ __( 'Otsikko', 'domio' ) }
								value={ block.title || '' }
								onChange={ ( value ) =>
									updateServiceBlock( index, 'title', value )
								}
							/>
							<TextControl
								label={ __( 'Puhelin', 'domio' ) }
								value={ block.phone || '' }
								onChange={ ( value ) =>
									updateServiceBlock( index, 'phone', value )
								}
							/>
							<TextControl
								label={ __( 'Sähköposti', 'domio' ) }
								value={ block.email || '' }
								onChange={ ( value ) =>
									updateServiceBlock( index, 'email', value )
								}
								type="email"
							/>
							<TextareaControl
								label={ __( 'Kuvaus', 'domio' ) }
								value={ block.description || '' }
								onChange={ ( value ) =>
									updateServiceBlock( index, 'description', value )
								}
							/>
							<Button
								size="small"
								isDestructive
								onClick={ () => removeServiceBlock( index ) }
							>
								{ __( 'Poista palvelu', 'domio' ) }
							</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addServiceBlock }>
						{ __( 'Lisää palvelu', 'domio' ) }
					</Button>
					<TextareaControl
						label={ __( 'Huomautus', 'domio' ) }
						value={ serviceNote }
						onChange={ ( value ) =>
							setAttributes( { serviceNote: value } )
						}
					/>
				</PanelBody>

				<PanelBody title={ __( 'Toimipisteet', 'domio' ) } initialOpen={ true }>
					{ areas.map( ( area, index ) => (
						<div
							className="domio-contacts-editor__item"
							key={ area.id || index }
						>
							<p className="domio-contacts-editor__label">
								{ __( 'Alue', 'domio' ) } { index + 1 }
							</p>
							<TextControl
								label={ __( 'Alueen nimi', 'domio' ) }
								value={ area.label || '' }
								onChange={ ( value ) =>
									updateArea( index, 'label', value )
								}
							/>
							<TextControl
								label={ __( 'Katuosoite', 'domio' ) }
								value={ area.street || '' }
								onChange={ ( value ) =>
									updateArea( index, 'street', value )
								}
							/>
							<TextControl
								label={ __( 'Postinumero ja kaupunki', 'domio' ) }
								value={ area.city || '' }
								onChange={ ( value ) =>
									updateArea( index, 'city', value )
								}
							/>
							<TextControl
								label={ __( 'Karttahaku', 'domio' ) }
								value={ area.mapQuery || '' }
								onChange={ ( value ) =>
									updateArea( index, 'mapQuery', value )
								}
								help={ __(
									'Google Maps -haku. Tyhjä käyttää osoitetta.',
									'domio'
								) }
							/>
							<Button
								size="small"
								isDestructive
								onClick={ () => removeArea( index ) }
							>
								{ __( 'Poista alue', 'domio' ) }
							</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addArea }>
						{ __( 'Lisää alue', 'domio' ) }
					</Button>
				</PanelBody>

				<PanelBody title={ __( 'Henkilöt', 'domio' ) } initialOpen={ false }>
					{ people.map( ( person, index ) => (
						<div
							className="domio-contacts-editor__item"
							key={ person.id || index }
						>
							<p className="domio-contacts-editor__label">
								{ person.name ||
									`${ __( 'Henkilö', 'domio' ) } ${ index + 1 }` }
							</p>
							<TextControl
								label={ __( 'Osion otsikko', 'domio' ) }
								value={ person.section || '' }
								onChange={ ( value ) =>
									updatePerson( index, 'section', value )
								}
							/>
							<TextControl
								label={ __( 'Nimi', 'domio' ) }
								value={ person.name || '' }
								onChange={ ( value ) =>
									updatePerson( index, 'name', value )
								}
							/>
							<TextControl
								label={ __( 'Rooli', 'domio' ) }
								value={ person.role || '' }
								onChange={ ( value ) =>
									updatePerson( index, 'role', value )
								}
							/>
							<TextControl
								label={ __( 'Puhelin', 'domio' ) }
								value={ person.phone || '' }
								onChange={ ( value ) =>
									updatePerson( index, 'phone', value )
								}
							/>
							<TextControl
								label={ __( 'Sähköposti', 'domio' ) }
								value={ person.email || '' }
								onChange={ ( value ) =>
									updatePerson( index, 'email', value )
								}
								type="email"
							/>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) => {
										const next = people.map( ( item, i ) =>
											i === index
												? {
														...item,
														imageId: media.id,
														imageAlt:
															item.imageAlt ||
															media.alt ||
															item.name ||
															'',
												  }
												: item
										);
										setAttributes( { people: next } );
									} }
									allowedTypes={ [ 'image' ] }
									value={ person.imageId || 0 }
									render={ ( { open } ) => (
										<div className="domio-contacts-editor__media">
											<Button variant="secondary" onClick={ open }>
												{ person.imageId
													? __( 'Vaihda kuva', 'domio' )
													: __( 'Valitse kuva', 'domio' ) }
											</Button>
											{ person.imageId ? (
												<Button
													isDestructive
													variant="link"
													onClick={ () =>
														updatePerson( index, 'imageId', 0 )
													}
												>
													{ __( 'Palauta oletuskuva', 'domio' ) }
												</Button>
											) : null }
										</div>
									) }
								/>
							</MediaUploadCheck>
							<Button
								size="small"
								isDestructive
								onClick={ () => removePerson( index ) }
							>
								{ __( 'Poista henkilö', 'domio' ) }
							</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addPerson }>
						{ __( 'Lisää henkilö', 'domio' ) }
					</Button>
				</PanelBody>

				<PanelBody title={ __( 'Laskutus', 'domio' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Otsikko', 'domio' ) }
						value={ invoiceHeading }
						onChange={ ( value ) =>
							setAttributes( { invoiceHeading: value } )
						}
					/>
					{ invoiceRows.map( ( row, index ) => (
						<div
							className="domio-contacts-editor__item"
							key={ row.id || index }
						>
							<TextControl
								label={ __( 'Kenttä', 'domio' ) }
								value={ row.label || '' }
								onChange={ ( value ) =>
									updateInvoice( index, 'label', value )
								}
							/>
							<TextareaControl
								label={ __( 'Arvo', 'domio' ) }
								value={ row.value || '' }
								onChange={ ( value ) =>
									updateInvoice( index, 'value', value )
								}
							/>
							<TextControl
								label={ __( 'Linkki (valinnainen)', 'domio' ) }
								value={ row.href || '' }
								onChange={ ( value ) =>
									updateInvoice( index, 'href', value )
								}
							/>
							<Button
								size="small"
								isDestructive
								onClick={ () => removeInvoiceRow( index ) }
							>
								{ __( 'Poista rivi', 'domio' ) }
							</Button>
						</div>
					) ) }
					<Button variant="secondary" onClick={ addInvoiceRow }>
						{ __( 'Lisää rivi', 'domio' ) }
					</Button>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-contacts__inner">
					<RichText
						tagName="h2"
						className="domio-contacts__title"
						value={ heading }
						onChange={ ( value ) =>
							setAttributes( { heading: value } )
						}
						placeholder={ __( 'Otsikko…', 'domio' ) }
						allowedFormats={ [] }
					/>

					<div className="domio-contacts__columns">
						<div className="domio-contacts__aside">
							{ areas.length > 0 ? (
								<div className="domio-contacts__addresses">
									{ areas.map( ( area ) => {
										const query =
											area.mapQuery ||
											[ area.street, area.city ]
												.filter( Boolean )
												.join( ', ' );
										if ( ! area.street && ! area.city ) {
											return null;
										}
										const line = [ area.street, area.city ]
											.filter( Boolean )
											.join( ', ' );
										return (
											<div
												key={ area.id }
												className="domio-contacts__addr"
											>
												<a
													className="domio-contacts__map-link"
													href={ mapHref( query ) }
													target="_blank"
													rel="noopener noreferrer"
													onClick={ ( event ) =>
														event.preventDefault()
													}
												>
													<span className="domio-contacts__map-icon">
														<MapPin />
													</span>
													<span className="domio-contacts__map-copy">
														<strong>
															{ area.label ||
																area.street }
														</strong>
														{ area.label && line ? (
															<span>{ line }</span>
														) : area.city ? (
															<span>{ area.city }</span>
														) : null }
													</span>
												</a>
											</div>
										);
									} ) }
								</div>
							) : null }

							{ urgentHeading ||
							serviceBlocks.length > 0 ||
							serviceNote ||
							serviceHours ||
							servicePhone ? (
								<div className="domio-contacts__services">
									{ urgentHeading ? (
										<p className="domio-contacts__urgent-heading">
											{ urgentHeading }
										</p>
									) : null }

									{ serviceBlocks.length > 0 ? (
										<div className="domio-contacts__service-blocks">
											{ serviceBlocks.map( ( block, index ) => {
												if (
													! block.title &&
													! block.phone &&
													! block.email &&
													! block.description
												) {
													return null;
												}
												return (
													<div
														className="domio-contacts__service"
														key={ block.id || index }
													>
														{ block.phone || block.email ? (
															<p className="domio-contacts__service-contact">
																{ block.phone ? (
																	<a href={ telHref( block.phone ) }>
																		{ block.phone }
																	</a>
																) : null }
																{ block.phone && block.email ? (
																	<br />
																) : null }
																{ block.email ? (
																	<a href={ `mailto:${ block.email }` }>
																		{ block.email }
																	</a>
																) : null }
															</p>
														) : null }
														{ block.description ? (
															<p className="domio-contacts__service-desc">
																{ block.description }
															</p>
														) : null }
													</div>
												);
											} ) }
										</div>
									) : serviceHours || servicePhone ? (
										<p className="domio-contacts__sub">
											{ serviceHours }
											{ servicePhone ? (
												<a href={ telHref( servicePhone ) }>
													{ servicePhone }
												</a>
											) : null }
										</p>
									) : null }

									{ serviceNote ? (
										<p className="domio-contacts__service-note">
											{ serviceNote }
										</p>
									) : null }
								</div>
							) : null }
						</div>

						{ people.length > 0 ? (
							<div className="domio-contacts__people">
								{ people.map( ( person, index ) => (
									<div
										className="domio-contacts__person"
										key={ person.id || index }
									>
										<div className="domio-contacts__person-media">
											<PersonPhoto person={ person } />
										</div>
										<div className="domio-contacts__person-body">
											{ person.name ? (
												<p className="domio-contacts__name">
													{ person.name }
												</p>
											) : null }
											{ person.role ? (
												<p className="domio-contacts__role">
													{ person.role }
												</p>
											) : null }
											<p className="domio-contacts__contact">
												{ person.phone ? (
													<>
														<a href={ telHref( person.phone ) }>
															{ person.phone }
														</a>
														{ person.email ? <br /> : null }
													</>
												) : null }
												{ person.email ? (
													<a href={ `mailto:${ person.email }` }>
														{ person.email }
													</a>
												) : null }
											</p>
										</div>
									</div>
								) ) }
							</div>
						) : null }
					</div>

					{ invoiceHeading || invoiceRows.length > 0 ? (
						<div className="domio-contacts__billing">
							{ invoiceRows.length > 0 ? (
								<div className="domio-contacts__invoice">
									{ invoiceRows.map( ( row, index ) => (
										<div
											className="domio-contacts__inv-row"
											key={ row.id || index }
										>
											<span className="domio-contacts__inv-label">
												{ row.label }
											</span>
											<span className="domio-contacts__inv-value">
												{ row.href ? (
													<a href={ row.href }>{ row.value }</a>
												) : (
													( row.value || '' )
														.split( '\n' )
														.map( ( line, lineIndex ) => (
															<span key={ lineIndex }>
																{ lineIndex > 0 ? (
																	<br />
																) : null }
																{ line }
															</span>
														) )
												) }
											</span>
										</div>
									) ) }
								</div>
							) : null }
						</div>
					) : null }
				</div>
			</section>
		</>
	);
}
