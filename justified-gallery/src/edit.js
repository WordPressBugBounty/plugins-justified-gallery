import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	BlockControls,
	InspectorControls,
	MediaUpload,
	MediaPlaceholder,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	BaseControl,
	Disabled,
	DropZone,
	FormFileUpload,
	__experimentalNumberControl as NumberControl,
	__experimentalUnitControl as UnitControl,
	PanelBody,
	SelectControl,
	Spinner,
	ToolbarButton,
	ToolbarGroup,
	withNotices,
} from '@wordpress/components';
import { useDebounce } from '@wordpress/compose';
import { uploadMedia } from '@wordpress/media-utils';

import ResizableIframe from './components/resizable-iframe';
import './editor.scss';

/**
 * @param {Object} props
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
const Edit = ( props ) => {
	const blockProps = useBlockProps();

	const {
		attributes: {
			images,
			hover,
			lightbox,
			linkTo,
			lastRow,
			margin,
			rowHeight,
			maxRowHeight,
		},
		className,
		isSelected,
		noticeOperations,
		noticeUI,
		setAttributes,
	} = props;

	const [ isLoaderActive, setIsLoaderActive ] = useState( false );

	const onSelectImages = ( selectedImages ) => {
		const imagesIDs = [];

		if ( selectedImages.length > 0 ) {
			selectedImages.forEach( function ( el ) {
				imagesIDs.push( el.id );
			} );
		}

		setAttributes( { images: imagesIDs } );
	};

	const uploadFromFiles = ( event ) => {
		addFiles( event.target.files );
	};

	const addFiles = ( files ) => {
		const currentImages = props.attributes.images || [];
		uploadMedia( {
			allowedType: 'image',
			filesList: files,
			onFileChange: ( addedImages ) => {
				setIsLoaderActive( true );
				const newIds = [];
				addedImages.forEach( ( img ) => {
					if ( typeof img.id !== 'undefined' ) {
						newIds.push( img.id );
					}
				} );
				if ( newIds.length > 0 ) {
					setIsLoaderActive( false );
					setAttributes( { images: currentImages.concat( newIds ) } );
				}
			},
			onError: noticeOperations.createErrorNotice,
		} );
	};

	const createPreviewURL = () => {
		let url = window.dgwtJgGutenBlock.previewURL;
		let previewLimit = false;

		if ( images.length > 0 ) {
			images.forEach( function ( image, i ) {
				if (window.dgwtJgGutenBlock.previewLimit > 0 && i >= window.dgwtJgGutenBlock.previewLimit) {
					previewLimit = true;
					return;
				}
				url += `&${ encodeURIComponent( 'id[' + i + ']' ) }=${ image }`;
			} );
		}

		if ( hover.length > 0 ) {
			url += `&hover=${ hover }`;
		}

		if ( lightbox.length > 0 ) {
			url += `&lightbox=${ lightbox }`;
		}

		if ( lastRow.length > 0 ) {
			url += `&lastrow=${ lastRow }`;
		}

		if ( margin.length > 0 ) {
			url += `&margin=${ margin.replace( 'px', '' ) }`;
		}

		if ( rowHeight.length > 0 ) {
			url += `&rowheight=${ rowHeight.replace( 'px', '' ) }`;
		}

		if ( maxRowHeight.length > 0 ) {
			url += `&maxrowheight=${ maxRowHeight.replace( 'px', '' ) }`;
		}

		if ( previewLimit ) {
			url += '&previewLimit=1';
		}

		return url;
	};

	const DropZoneComp = () => {
		return <DropZone onFilesDrop={ addFiles } />;
	};
	const UploadLoader = () => {
		return (
			<div className="dgwt-jg-gutenberg-upload-loader">
				<div className="dgwt-jg-gutenberg-upload-loader__content">
					<Spinner />
				</div>
			</div>
		);
	};

	const Controls = () => {
		return (
			<BlockControls>
				<ToolbarGroup>
					{ !! images.length && (
						<MediaUpload
							onSelect={ onSelectImages }
							type="image"
							multiple
							gallery
							value={ images.map( ( img ) => img ) }
							render={ ( { open } ) => (
								<ToolbarButton
									className="components-toolbar__control"
									label={ __(
										'Edit Gallery',
										'justified-gallery'
									) }
									icon="edit"
									onClick={ open }
								/>
							) }
						/>
					) }
				</ToolbarGroup>
			</BlockControls>
		);
	};

	const handleRowHeightChange = ( value ) => {
		setAttributes( {
			rowHeight: value,
		} );
	};

	const handleRowHeightChangeDebounced = useDebounce(
		handleRowHeightChange,
		500
	);

	const handleMaxRowHeightChange = ( value ) => {
		setAttributes( {
			maxRowHeight: value,
		} );
	};

	const handleMaxRowHeightChangeDebounced = useDebounce(
		handleMaxRowHeightChange,
		500
	);

	if ( images.length === 0 ) {
		return (
			<div { ...blockProps }>
				<Controls />
				<MediaPlaceholder
					icon="format-gallery"
					className={ className }
					labels={ {
						title: __( 'Gallery' ),
						name: __( 'images' ),
					} }
					onSelect={ onSelectImages }
					accept="image/*"
					type="image"
					multiple
					notices={ noticeUI }
					onError={ noticeOperations.createErrorNotice }
				/>
			</div>
		);
	}

	return (
		<div { ...blockProps }>
			<Controls />
			<InspectorControls>
				<PanelBody
					title={ __( 'Gallery settings', 'justified-gallery' ) }
				>
					<SelectControl
						label={ __( 'Tiles style', 'justified-gallery' ) }
						value={ hover }
						onChange={ ( value ) =>
							setAttributes( { hover: value } )
						}
						options={ [
							{
								value: '',
								label: __(
									'Inherit From Plugin Settings',
									'justified-gallery'
								),
							},
							{
								value: 'none',
								label: __(
									'Without Styling',
									'justified-gallery'
								),
							},
							{
								value: 'simple',
								label: __( 'Simple', 'justified-gallery' ),
							},
							{
								value: 'jg_standard',
								label: __( 'JG Standard', 'justified-gallery' ),
							},
							{
								value: 'layla',
								label: __( 'Layla', 'justified-gallery' ),
							},
						] }
						__nextHasNoMarginBottom
					/>
					<SelectControl
						label={ __( 'Lightbox type', 'justified-gallery' ) }
						value={ lightbox }
						onChange={ ( value ) =>
							setAttributes( { lightbox: value } )
						}
						options={ [
							{
								value: '',
								label: __(
									'Inherit From Plugin Settings',
									'justified-gallery'
								),
							},
							{
								value: 'none',
								label: __(
									'Disable Lightbox',
									'justified-gallery'
								),
							},
							{
								value: 'swipebox',
								label: __( 'Swipebox', 'justified-gallery' ),
							},
							{
								value: 'photoswipe',
								label: __( 'Photoswipe', 'justified-gallery' ),
							},
						] }
						__nextHasNoMarginBottom
					/>
					{ lightbox === 'none' ? (
						<SelectControl
							label={ __( 'Link to', 'justified-gallery' ) }
							value={ linkTo }
							onChange={ ( value ) =>
								setAttributes( { linkTo: value } )
							}
							options={ [
								{
									value: 'attachment',
									label: __(
										'Attachment Page',
										'justified-gallery'
									),
								},
								{
									value: 'file',
									label: __(
										'Media File',
										'justified-gallery'
									),
								},
								{
									value: 'none',
									label: __( 'None', 'justified-gallery' ),
								},
							] }
							__nextHasNoMarginBottom
						/>
					) : null }
					<SelectControl
						label={ __( 'Last row', 'justified-gallery' ) }
						value={ lastRow }
						onChange={ ( value ) =>
							setAttributes( { lastRow: value } )
						}
						options={ [
							{
								value: '',
								label: __(
									'Inherit From Plugin Settings',
									'justified-gallery'
								),
							},
							{
								value: 'nojustify',
								label: __( 'Align left', 'justified-gallery' ),
							},
							{
								value: 'center',
								label: __(
									'Align center',
									'justified-gallery'
								),
							},
							{
								value: 'right',
								label: __( 'Align right', 'justified-gallery' ),
							},
							{
								value: 'justify',
								label: __( 'Justify', 'justified-gallery' ),
							},
							{
								value: 'hide',
								label: __( 'Hide', 'justified-gallery' ),
							},
						] }
						__nextHasNoMarginBottom
					/>
					<BaseControl
						label={ __( 'Margin', 'justified-gallery' ) }
						help={ __(
							'Leave blank to inherit from plugin Settings',
							'justified-gallery'
						) }
						id="dgwt-jg-margin"
						__nextHasNoMarginBottom
					>
						<UnitControl
							id="dgwt-jg-margin"
							onChange={ ( value ) => {
								setAttributes( {
									margin: value,
								} );
							} }
							value={ margin }
							units={ [
								{ value: 'px', label: 'px', default: 0 },
							] }
						/>
					</BaseControl>
					<BaseControl
						label={ __( 'Row height', 'justified-gallery' ) }
						help={ __(
							'Leave blank to inherit from plugin Settings',
							'justified-gallery'
						) }
						id="dgwt-jg-rowheight"
						__nextHasNoMarginBottom
					>
						<UnitControl
							id="dgwt-jg-rowheight"
							onChange={ handleRowHeightChangeDebounced }
							value={ rowHeight }
							units={ [
								{ value: 'px', label: 'px', default: 0 },
							] }
						/>
					</BaseControl>
					<BaseControl
						label={ __( 'Max row height', 'justified-gallery' ) }
						help={ __(
							'Leave blank to inherit from plugin Settings',
							'justified-gallery'
						) }
						id="dgwt-jg-maxrowheight"
						__nextHasNoMarginBottom
					>
						<UnitControl
							id="dgwt-jg-maxrowheight"
							onChange={ handleMaxRowHeightChangeDebounced }
							value={ maxRowHeight }
							units={ [
								{ value: 'px', label: 'px', default: 0 },
							] }
						/>
					</BaseControl>
				</PanelBody>
			</InspectorControls>
			<Disabled className="dgwt-jg-gutenberg-disabled-preview-wrapper">
				<ResizableIframe src={ createPreviewURL() } frameBorder="0" />
			</Disabled>
			{ isLoaderActive && <UploadLoader /> }
			<DropZoneComp />
			{ isSelected && (
				<div className="blocks-gallery-item has-add-item-button">
					<FormFileUpload
						multiple
						className="block-library-gallery-add-item-button"
						onChange={ uploadFromFiles }
						accept="image/*"
						icon="insert"
					>
						{ __( 'Upload an image' ) }
					</FormFileUpload>
				</div>
			) }
		</div>
	);
};

export default withNotices( Edit );
