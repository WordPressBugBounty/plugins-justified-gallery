/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';
import { fromMatch } from '@wordpress/shortcode';

const parseShortcodeIds = ( ids ) => {
	if ( ! ids ) {
		return [];
	}

	return ids.split( ',' ).map( ( id ) => parseInt( id, 10 ) );
};

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'core/gallery' ],
			transform: ( { ids } ) => {
				return createBlock( 'dgwt/justified-gallery', {
					images: ids,
				} );
			},
		},
		{
			type: 'block',
			blocks: [ 'core/shortcode' ],
			transform: ( { text } ) => {
				// For some reason using regex fails randomly.
				// const re = regexp( 'gallery' );
				const tag = 'gallery';
				const re = new RegExp(
					'\\[(\\[?)(' +
						tag +
						')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)',
					'g'
				);
				const match = re.exec( text );
				if ( match === null ) {
					return createBlock( 'dgwt/justified-gallery', {
						images: [],
					} );
				}
				const {
					attrs: {
						named: { ids },
					},
				} = fromMatch( match );

				return createBlock( 'dgwt/justified-gallery', {
					images: parseShortcodeIds( ids ),
				} );
			},
			isMatch: ( { text } ) => {
				// const re = regexp( 'gallery' );
				const tag = 'gallery';
				const re = new RegExp(
					'\\[(\\[?)(' +
						tag +
						')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)',
					'g'
				);
				const match = re.exec( text );
				if ( match === null ) {
					return false;
				}

				const {
					attrs: {
						named: { ids },
					},
				} = fromMatch( match );

				if ( typeof ids === 'undefined' ) {
					return false;
				}

				return ids.length !== 0;
			},
		},
	],
	to: [
		{
			type: 'block',
			blocks: [ 'core/gallery' ],
			transform: ( { images } ) => {
				return createBlock( 'core/gallery', {
					shortCodeTransforms: images.map( ( id ) => ( {
						id: parseInt( id ),
					} ) ),
				} );
			},
		},
	],
};

export default transforms;
