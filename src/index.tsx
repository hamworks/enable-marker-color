/**
 * External dependencies
 */
import { registerBlockExtension } from '@10up/block-components';

/**
 * WordPress dependencies
 */
import {
	// @ts-ignore
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// @ts-ignore
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
	InspectorControls,
	withColors,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import type { BlockEditProps } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import type { BlockAttributes, WithColorProps } from './types';

registerBlockExtension( `core/list`, {
	extensionName: 'src',
	attributes: {
		markerColor: {
			type: 'string',
		},
	},
	classNameGenerator: generateClassName,
	inlineStyleGenerator: () => ( {} ),
	Edit: withColors( {
		markerColor: 'src',
	} )( Edit ),
	order: 'after',
} );

function generateClassName( attributes: BlockAttributes ) {
	const { markerColor } = attributes;
	let className = '';
	if ( markerColor ) {
		className = `has-marker-color has-${ markerColor }-marker-color`;
	}
	return className;
}

function Edit( {
	markerColor,
	setMarkerColor,
	clientId,
}: BlockEditProps< BlockAttributes > & WithColorProps ) {
	const colorGradientSettings = useMultipleOriginColorsAndGradients();
	return (
		<>
			{ /* @ts-ignore */ }
			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					settings={ [
						{
							label: __( 'Marker', 'enable-marker-color' ),
							clearable: true,
							disableCustomColors: true,
							disableCustomGradients: true,
							colorValue: markerColor.color,
							onColorChange: ( value: string ) => {
								setMarkerColor( value );
							},
						},
					] }
					panelId={ clientId }
					hasColorsOrGradients={ false }
					disableCustomColors={ true }
					__experimentalIsRenderedInSidebar
					{ ...colorGradientSettings }
				/>
			</InspectorControls>
		</>
	);
}
