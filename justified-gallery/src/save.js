import { RawHTML } from '@wordpress/element';

export function saveDeprecated( { attributes } ) {
	const { images } = attributes;
	const shortcodeAttrs = 'ids="' + images.join() + '"';

	return <RawHTML>{ '[gallery ' + shortcodeAttrs + ']' }</RawHTML>;
}

export default function save( { attributes } ) {
	const {
		images,
		hover,
		lightbox,
		linkTo,
		lastRow,
		margin,
		rowHeight,
		maxRowHeight,
	} = attributes;

	let shortcodeAttrs = 'ids="' + images.join() + '"';

	if ( typeof lightbox !== 'undefined' && lightbox.length > 0 ) {
		shortcodeAttrs += ' lightbox="' + lightbox + '"';
	}

	if ( typeof hover !== 'undefined' && hover.length > 0 ) {
		shortcodeAttrs += ' hover="' + hover + '"';
	}

	// Attribute "link" could be used only when "lightbox" is disabled.
	if (
		typeof lightbox !== 'undefined' &&
		lightbox.length > 0 &&
		lightbox === 'none' &&
		typeof linkTo !== 'undefined' &&
		linkTo.length > 0 &&
		linkTo !== 'attachment'
	) {
		shortcodeAttrs += ' link="' + linkTo + '"';
	}

	if ( typeof lastRow !== 'undefined' && lastRow.length > 0 ) {
		shortcodeAttrs += ' lastrow="' + lastRow + '"';
	}

	if ( typeof margin !== 'undefined' && margin.length > 0 ) {
		shortcodeAttrs += ' margin="' + margin.replace( 'px', '' ) + '"';
	}

	if ( typeof rowHeight !== 'undefined' && rowHeight.length > 0 ) {
		shortcodeAttrs += ' rowheight="' + rowHeight.replace( 'px', '' ) + '"';
	}

	if ( typeof maxRowHeight !== 'undefined' && maxRowHeight.length > 0 ) {
		shortcodeAttrs +=
			' maxrowheight="' + maxRowHeight.replace( 'px', '' ) + '"';
	}

	return <RawHTML>{ '[gallery ' + shortcodeAttrs + ']' }</RawHTML>;
}
