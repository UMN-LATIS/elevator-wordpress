/**
 * BLOCK: block
 *
 * Registers a block to handle the Brightcove Video Connect shortcode in Gutenberg.
 */

//  Import CSS.
// import './style.scss';
// import './editor.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n

const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const {
  TextControl, CheckboxControl, Text, Button, ExternalLink, PanelBody, PanelRow
} = wp.components;
const { InspectorControls } = wp.blockEditor;

const getShortcodeString = attrs => {

	if ( ! attrs.fileobjectid ) {
		return;
	}

	// Canonicalize order of attributes to ensure that saved content matches previous value.
	const shortcodeAtts = Object.entries( attrs )
		.sort( ( a, b ) => a[0].localeCompare( b[0] ) )
		.reduce( ( atts, [ k, v ] ) => {
			atts[ k ] = v;
			return atts;
		}, {} );

		// // [elevator width=640 height=480 includelink="' + includeLink +'" includesummary="'+includeSummary+'" fileobjectid="' + e.fileObjectId + '" objectid="' + e.objectId + '" sourceurl="' + e.currentLink + '"]
		console.log(shortcodeAtts);
	return wp.shortcode.string( { tag: 'elevator', attrs: shortcodeAtts } );
}

var openedElevatorWindow = null;

/**
 * Register a Gutenberg Block.
 *
 * Registers a new block to provide UI for the [bc_video] shortcode in Gutenberg.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'umn-latis/elevator-embed', {

	title: __( 'Elevator Embed', 'elevator-embed' ),

	icon: 'format-video',

	category: 'embed',

	keywords: [
		__( 'Brightcove', 'brightcove-gutenberg' ),
		__( 'video', 'brightcove-gutenberg' ),
	],

	attributes: {
		'width': {
			type: 'string',
		},
		'height': {
			type: 'string',
		},
		'includeLink': {
			type: 'string',
		},
		'includeSummary': {
			type: 'string',
		},
		'fileobjectid': {
			type: 'string',
		},
		'objectid': {
			type: 'string',
		},
		'sourceurl': {
			type: 'string',
		}
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	edit( { attributes, setAttributes, className, isSelected } ) {

		const {
			width,
			height,
			fileobjectid,
			objectid,
			sourceurl,
			includeLink,
			includeSummary
		} = attributes;


		if(!attributes.width) {
			setAttributes({"width": "640"});
		}
		if(!attributes.height) {
			setAttributes({"height": "480"});
		}

		if(!attributes.includeLink) {
			setAttributes({"includeSummary": elevator_settings_includeSummary});
		}
		
		if(!attributes.includeSummary) {
			setAttributes({"includeLink": elevator_settings_includeLink});
		}

// [elevator width=640 height=480 includelink="' + includeLink +'" includesummary="'+includeSummary+'" fileobjectid="' + e.fileObjectId + '" objectid="' + e.objectId + '" sourceurl="' + e.currentLink + '"]

		var callback = (data) => {
			console.log(data);
		;}
		window.addEventListener("message", (event) => {
			if(event.data == "parentLoaded") {
				openedElevatorWindow.postMessage(
				{
					pluginSetup: true, 
					elevatorPlugin:"WordPress", 
					includeMetadata: includeSummary,
					elevatorCallbackType:"JS", 
					apiKey: "", 
					timeStamp:"", 
					entangledSecret: ""
				}, "*");  
			}
			if(typeof event.data.pluginResponse !== "undefined") {
				console.log(event.data);
				setAttributes( {"fileobjectid":event.data.fileObjectId, "objectid": event.data.objectId, "sourceurl": event.data.currentLink});
				callback(event.data);
			}
		}, false);
		

		return (
						<div className={ className  }>
				

				
				<div class="components-placeholder">
					<span>Embed Elevator Asset</span>
				<Button isSecondary onClick={ () => {
							openedElevatorWindow = window.open(elevator_settings_endpoint, "elevatorPlugin");
						} }>Browse for Asset</Button>

				<CheckboxControl
			label="Include a Link"
			help="Should the rendered embed include a link to the source asset?"
			checked={ includeLink=="on"}
			onChange={ (includeLink) => setAttributes({ 'includeLink': includeLink?"on":"off"}) }
		/>

		<CheckboxControl
			label="Include a Summary"
			help="Should a metadata summary be displayed below the asset?"
			checked={ includeSummary=="on"}
			onChange={ (includeSummary) => setAttributes({ 'includeSummary': includeSummary?"on":"off"}) }
		/>
				
				 <TextControl
                label="Width of the embed"
                value={ width }
                onChange={ ( width ) => setAttributes( { width } ) }
              />
			  <TextControl
                label="Height of the embed"
                value={ height }
                onChange={ ( height ) => setAttributes( { height } ) }
              />
				<ExternalLink href={ sourceurl }>Asset Link</ExternalLink>


					
					
				</div>


	  </div>
		);
	},

	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save: function( { attributes, className } ) {
		console.log(attributes);
			console.log("Hey");
		return (
			<div className={ className }>
				
				{ getShortcodeString( attributes ) }
			</div>
		);
	},
} );