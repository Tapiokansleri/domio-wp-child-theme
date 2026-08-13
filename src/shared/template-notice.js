/**
 * Warning when Domio blocks are edited without Domio sivupohja.
 */
import { Notice, Button } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';
import { __ } from '@wordpress/i18n';

/**
 * @return {string} Domio page template path.
 */
export function getDomioPageTemplate() {
	if (
		typeof window !== 'undefined' &&
		window.domioBlockDefaults &&
		window.domioBlockDefaults.pageTemplate
	) {
		return window.domioBlockDefaults.pageTemplate;
	}

	if (
		typeof window !== 'undefined' &&
		window.domioLandingPreload &&
		window.domioLandingPreload.pageTemplate
	) {
		return window.domioLandingPreload.pageTemplate;
	}

	return 'page-templates/domio-sivupohja.php';
}

/**
 * @return {boolean} True when edited post uses Domio sivupohja.
 */
export function useIsDomioTemplate() {
	const pageTemplate = getDomioPageTemplate();

	return useSelect(
		( select ) =>
			select( editorStore ).getEditedPostAttribute( 'template' ) ===
			pageTemplate,
		[ pageTemplate ]
	);
}

/**
 * Inline editor notice + shortcut to switch template.
 *
 * @return {JSX.Element|null} Notice or null.
 */
export function DomioTemplateNotice() {
	const isDomioTemplate = useIsDomioTemplate();
	const { editPost } = useDispatch( editorStore );
	const pageTemplate = getDomioPageTemplate();

	if ( isDomioTemplate ) {
		return null;
	}

	return (
		<Notice
			status="warning"
			isDismissible={ false }
			className="domio-template-notice"
		>
			<p style={ { margin: '0 0 0.75rem' } }>
				{ __(
					'Domio-lohkojen tyylit toimivat oikein vain Domio sivupohjalla. Vaihda sivupohjaksi “Domio sivupohja” (Sivu → Sivupohja).',
					'domio'
				) }
			</p>
			<Button
				variant="secondary"
				onClick={ () => editPost( { template: pageTemplate } ) }
			>
				{ __( 'Käytä Domio sivupohjaa', 'domio' ) }
			</Button>
		</Notice>
	);
}
