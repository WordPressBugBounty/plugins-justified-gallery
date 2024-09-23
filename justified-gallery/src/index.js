/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { Icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save, { saveDeprecated } from './save';
import jgIcon from './icons/jg';
import transforms from './transforms';

import './editor.scss';

const attributes = {
	images: {
		type: 'array',
		default: [],
	},
	hover: {
		type: 'string',
		default: '',
		enum: [ '', 'none', 'simple', 'jg_standard', 'layla' ],
	},
	lightbox: {
		type: 'string',
		default: '',
		enum: [ '', 'none', 'photoswipe', 'swipebox' ],
	},
	linkTo: {
		type: 'string',
		default: '',
		enum: [ '', 'attachment', 'file', 'none' ],
	},
	lastRow: {
		type: 'string',
		default: '',
		enum: [ '', 'nojustify', 'center', 'right', 'justify', 'hide' ],
	},
	margin: {
		type: 'string',
		default: '',
	},
	rowHeight: {
		type: 'string',
		default: '',
	},
	maxRowHeight: {
		type: 'string',
		default: '',
	},
};

registerBlockType( 'dgwt/justified-gallery', {
	title: __( 'Justified Gallery', 'justified-gallery' ),
	description: __(
		'Display multiple images in a responsive justified image grid and a pretty lightbox',
		'justified-gallery'
	),
	icon: {
		src: <Icon icon={ jgIcon } />,
	},
	category: 'media',
	attributes,
	keywords: [
		__( 'Justified Gallery', 'justified-gallery' ),
		__( 'gallery' ),
		__( 'images' ),
	],
	edit: Edit,
	save,
	transforms,
	deprecated: [
		{
			attributes,
			save: saveDeprecated,
		},
	],
} );
