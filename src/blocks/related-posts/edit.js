/**
 * Domio related-posts block editor.
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	ComboboxControl,
	Button,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { store as editorStore } from '@wordpress/editor';
import { dateI18n } from '@wordpress/date';
import { decodeEntities } from '@wordpress/html-entities';
import { BackgroundControls, getSectionClasses, getPatternStyle } from '../../shared/background';
import { DomioTemplateNotice } from '../../shared/template-notice';

const strip = ( html ) =>
	decodeEntities( String( html || '' ).replace( /<[^>]*>/g, ' ' ) )
		.replace( /\s+/g, ' ' )
		.trim();

const trimWords = ( text, count ) => {
	const words = strip( text ).split( ' ' ).filter( Boolean );
	if ( words.length <= count ) {
		return words.join( ' ' );
	}
	return words.slice( 0, count ).join( ' ' ) + ' …';
};

/**
 * @param {Object} props Block props.
 * @return {JSX.Element} Editor element.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { heading, postIds, columns, background, pattern, patternOpacity } =
		attributes;
	const ids = Array.isArray( postIds ) ? postIds.map( Number ).filter( Boolean ) : [];
	const [ search, setSearch ] = useState( '' );

	const currentPostId = useSelect(
		( select ) => select( editorStore ).getCurrentPostId() || 0,
		[]
	);

	const selected = useSelect(
		( select ) => {
			const { getEntityRecord, getMedia } = select( coreStore );
			return ids.map( ( id ) => {
				const record = getEntityRecord( 'postType', 'post', id );
				if ( ! record ) {
					return { id, title: `#${ id }`, date: '', excerpt: '', image: '' };
				}
				const media = record.featured_media
					? getMedia( record.featured_media )
					: null;
				return {
					id,
					title: strip( record.title?.rendered ) || `#${ id }`,
					date: record.date || '',
					excerpt: trimWords(
						record.excerpt?.rendered || record.content?.rendered || '',
						22
					),
					image:
						media?.source_url ||
						media?.media_details?.sizes?.large?.source_url ||
						'',
				};
			} );
		},
		[ ids.join( ',' ) ]
	);

	const suggestions = useSelect(
		( select ) => {
			if ( ! search || search.length < 2 ) {
				return [];
			}
			const { getEntityRecords } = select( coreStore );
			const query = {
				search,
				per_page: 8,
				status: 'publish',
				_fields: [ 'id', 'title', 'type' ],
			};
			const posts = getEntityRecords( 'postType', 'post', query ) || [];
			return posts
				.filter(
					( item ) =>
						! ids.includes( item.id ) && item.id !== currentPostId
				)
				.map( ( item ) => ( {
					value: String( item.id ),
					label: strip( item.title?.rendered ),
				} ) );
		},
		[ search, ids.join( ',' ), currentPostId ]
	);

	const addId = ( value ) => {
		const id = parseInt( value, 10 );
		if ( ! id || ids.includes( id ) || id === currentPostId ) {
			return;
		}
		setAttributes( { postIds: [ ...ids, id ] } );
		setSearch( '' );
	};

	const removeId = ( id ) => {
		setAttributes( { postIds: ids.filter( ( item ) => item !== id ) } );
	};

	const blockProps = useBlockProps( {
		className: `domio-related-posts ${ getSectionClasses( background, pattern ) }`,
		style: {
			...getPatternStyle( patternOpacity ),
			'--domio-related-columns': String( columns || 3 ),
		},
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
				<PanelBody title={ __( 'Artikkelit', 'domio' ) } initialOpen={ true }>
					<SelectControl
						label={ __( 'Sarakkeet', 'domio' ) }
						value={ String( columns || 3 ) }
						options={ [
							{ label: '2', value: '2' },
							{ label: '3', value: '3' },
						] }
						onChange={ ( value ) =>
							setAttributes( { columns: parseInt( value, 10 ) } )
						}
					/>
					{ ids.length ? (
						<ul className="domio-related-posts-editor__list">
							{ selected.map( ( item ) => (
								<li
									className="domio-related-posts-editor__item"
									key={ item.id }
								>
									<span className="domio-related-posts-editor__item-title">
										{ item.title }
									</span>
									<Button
										isDestructive
										variant="link"
										onClick={ () => removeId( item.id ) }
									>
										{ __( 'Poista', 'domio' ) }
									</Button>
								</li>
							) ) }
						</ul>
					) : null }
					<ComboboxControl
						label={ __( 'Lisää artikkeli', 'domio' ) }
						value={ null }
						options={ suggestions }
						onFilterValueChange={ setSearch }
						onChange={ addId }
					/>
				</PanelBody>
			</InspectorControls>

			<section { ...blockProps }>
				<div className="domio-related-posts__inner">
					<RichText
						tagName="h2"
						className="domio-related-posts__heading"
						value={ heading }
						onChange={ ( value ) => setAttributes( { heading: value } ) }
						placeholder={ __( 'Lue myös', 'domio' ) }
						allowedFormats={ [] }
					/>
					{ ! selected.length ? (
						<p className="domio-related-posts-editor__empty">
							{ __(
								'Lisää artikkeleita sivupalkista. Kortit näyttävät kuvan, päivämäärän, otsikon ja otteen.',
								'domio'
							) }
						</p>
					) : (
						<div className="domio-related-posts__grid">
							{ selected.map( ( item ) => (
								<article
									className="domio-related-posts__card"
									key={ item.id }
								>
									{ item.image ? (
										<div className="domio-related-posts__media">
											<img src={ item.image } alt="" />
										</div>
									) : null }
									{ item.date ? (
										<time className="domio-related-posts__date">
											{ dateI18n( 'j.n.Y', item.date ) }
										</time>
									) : null }
									<h3 className="domio-related-posts__title">
										<a href="#related-post">{ item.title }</a>
									</h3>
									{ item.excerpt ? (
										<p className="domio-related-posts__excerpt">
											{ item.excerpt }
										</p>
									) : null }
								</article>
							) ) }
						</div>
					) }
				</div>
			</section>
		</>
	);
}
