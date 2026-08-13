/**
 * Shared Domio icon set (inline SVG, no external library).
 */

export const DOMIO_ICON_KEYS = [
	'clock',
	'shield',
	'heart',
	'key',
	'snowflake',
	'check',
	'pin',
];

export const DOMIO_ICON_LABELS = {
	clock: 'Kello',
	shield: 'Kilpi',
	heart: 'Sydän',
	key: 'Avain',
	snowflake: 'Lumihiutale',
	check: 'Tarkistusmerkki',
	pin: 'Sijainti',
};

/**
 * @param {Object} props Props.
 * @param {string} props.name Icon key.
 * @return {JSX.Element|null} Icon element.
 */
export function DomioIcon( { name, ...props } ) {
	const common = {
		xmlns: 'http://www.w3.org/2000/svg',
		viewBox: '0 0 24 24',
		fill: 'none',
		stroke: 'currentColor',
		strokeWidth: '2',
		strokeLinecap: 'round',
		strokeLinejoin: 'round',
		'aria-hidden': true,
		focusable: 'false',
		...props,
	};

	switch ( name ) {
		case 'clock':
			return (
				<svg { ...common }>
					<circle cx="12" cy="12" r="9" />
					<path d="M12 7v5l3 2" />
				</svg>
			);
		case 'shield':
			return (
				<svg { ...common }>
					<path d="M12 3l8 3v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-3z" />
				</svg>
			);
		case 'heart':
			return (
				<svg { ...common }>
					<path d="M12 20s-7-4.5-7-10a4 4 0 017-2.5A4 4 0 0119 10c0 5.5-7 10-7 10z" />
				</svg>
			);
		case 'key':
			return (
				<svg { ...common }>
					<circle cx="8" cy="14" r="3" />
					<path d="M10.5 12.5L20 3m-2 2l2 2" />
				</svg>
			);
		case 'snowflake':
			return (
				<svg { ...common }>
					<path d="M12 2v20M4.9 6.5l14.2 11M4.9 17.5l14.2-11M2 12h20" />
				</svg>
			);
		case 'check':
			return (
				<svg { ...common }>
					<path d="M20 6L9 17l-5-5" />
				</svg>
			);
		case 'pin':
			return (
				<svg { ...common }>
					<path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z" />
					<circle cx="12" cy="10" r="2.5" />
				</svg>
			);
		default:
			return null;
	}
}
