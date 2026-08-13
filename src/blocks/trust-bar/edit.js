/**
 * Domio trust bar block editor.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
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

import { DomioIcon, DOMIO_ICON_KEYS, DOMIO_ICON_LABELS } from '../../shared/icons';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { items, imageId, imageUrl, imageAlt, background, pattern, patternOpacity } =
		attributes;

	const blockProps = useBlockProps( {
		className: `domio-trust-bar alignfull ${ getSectionClasses(
			background,
			pattern,
			'green'
		) }`,
		style: getPatternStyle( patternOpacity, 100 ),
	} );

	const updateItem = ( index, key, value ) => {
		const next = items.map( ( item, i ) =>
			i === index ? { ...item, [ key ]: value } : item
		);
		setAttributes( { items: next } );
	};

	const addItem = () => {
		if ( items.length >= 6 ) {
			return;
		}
		setAttributes( {
			items: [
				...items,
				{ id: `trust-${ Date.now() }`, icon: 'check', text: '' },
			],
		} );
	};

	const removeItem = ( index ) => {
		setAttributes( {
			items: items.filter( ( _, i ) => i !== index ),
		} );
	};

	const moveItem = ( index, direction ) => {
		const target = index + direction;
		if ( target < 0 || target >= items.length ) {
			return;
		}
		const next = [ ...items ];
		const [ item ] = next.splice( index, 1 );
		next.splice( target, 0, item );
		setAttributes( { items: next } );
	};

	return (
		<>
			<DomioTemplateNotice />
			<BackgroundControls
				value={ background }
				onChange={ ( value ) => setAttributes( { background: value } ) }
				fallback="green"
				pattern={ pattern }
				onPatternChange={ ( value ) => setAttributes( { pattern: value } ) }
				patternOpacity={ patternOpacity }
				onPatternOpacityChange={ ( value ) =>
					setAttributes( { patternOpacity: value } )
				}
				patternOpacityFallback={ 100 }
			/>
			<InspectorControls>
				<PanelBody
					title={ __( 'Luottamuselementit', 'domio' ) }
					initialOpen={ true }
				>
					{ items.map( ( item, index ) => (
						<div
							className="domio-trust-bar-editor__item"
							key={ item.id || index }
						>
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
								label={ __( 'Teksti', 'domio' ) }
								value={ item.text || '' }
								onChange={ ( value ) =>
									updateItem( index, 'text', value )
								}
							/>
							<Flex gap={ 2 }>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === 0 }
										onClick={ () => moveItem( index, -1 ) }
									>
										{ __( 'Ylös', 'domio' ) }
									</Button>
								</FlexItem>
								<FlexItem>
									<Button
										size="small"
										disabled={ index === items.length - 1 }
										onClick={ () => moveItem( index, 1 ) }
									>
										{ __( 'Alas', 'domio' ) }
									</Button>
								</FlexItem>
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
					<Button
						variant="secondary"
						onClick={ addItem }
						disabled={ items.length >= 6 }
					>
						{ __( 'Lisää kohta', 'domio' ) }
					</Button>
				</PanelBody>

				<PanelBody title={ __( 'Taustakuva', 'domio' ) } initialOpen={ false }>
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
								<div className="domio-trust-bar-editor__media">
									{ imageUrl ? (
										<img src={ imageUrl } alt={ imageAlt || '' } />
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
											{ __( 'Poista kuva', 'domio' ) }
										</Button>
									) : null }
								</div>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				{ imageUrl ? (
					<div className="domio-trust-bar__media" aria-hidden="true">
						<img
							src={ imageUrl }
							alt=""
							className="domio-trust-bar__image"
						/>
					</div>
				) : null }
				<div className="domio-trust-bar__inner">
					<ul className="domio-trust-bar__list">
						{ items.map( ( item, index ) => (
							<li
								className="domio-trust-bar__item"
								key={ item.id || index }
							>
								<span className="domio-trust-bar__icon">
									<DomioIcon name={ item.icon || 'check' } />
								</span>
								<span className="domio-trust-bar__text">
									{ item.text ||
										__( 'Luottamusteksti…', 'domio' ) }
								</span>
							</li>
						) ) }
					</ul>
				</div>
			</section>
		</>
	);
}
