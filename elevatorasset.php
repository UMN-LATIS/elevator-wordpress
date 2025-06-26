<?php
/**
 *
 * @package   elevatorasset
 * @author    Colin McFadden <mcfa0086@umn.edu>
 * @license   GPL-2.0+
 * @link      http://github.com/umn-latis/
 * @copyright 2017
 *
 * @wordpress-plugin
 * Plugin Name:       Elevator Asset Browser
 * Plugin URI:        http://github.com/umn-latis/
 * Description:       Adds dropbox button before editor to choose an asset from Elevator
 * Version:           0.2.0
 * Author:           Colin McFadden
 * Author URI:       http://www.cmcfaddenc.om
 * Text Domain:       elevatorasset
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/


require_once( plugin_dir_path( __FILE__ ) . 'public/class-elevatorasset.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */

register_activation_hook( __FILE__, array( 'elevatorasset', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'elevatorasset', 'deactivate' ) );


add_action( 'plugins_loaded', array( 'elevatorasset', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/


if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-elevatorasset-admin.php' );
	add_action( 'plugins_loaded', array( 'elevatorasset_Admin', 'get_instance' ) );

}

require_once( plugin_dir_path( __FILE__ ) . 'elevatorAPI.php' );

function elevator_func( $atts ) {
	$options = get_option('elevatorasset_global_settings');
	
	// Add null checks for options
	if ( !is_array($options) ) {
		$options = array();
	}
	
	$endpoint = isset($options["endpoint"]) ? $options["endpoint"] : '';
	$key = isset($options["apikey"]) ? $options["apikey"] : '';
	$secret = isset($options["apisecret"]) ? $options["apisecret"] : '';

	// Check if required settings are configured
	if ( empty($endpoint) || empty($key) || empty($secret) ) {
		return '<p>Elevator Asset Browser is not properly configured. Please check the plugin settings.</p>';
	}

	$elevatorAPI = new elevatorAPI($endpoint . "/api/v1/",$key,$secret);

	$a = shortcode_atts( array(
		'width' => '',
		'height' => '',
		'fileobjectid' => '',
		'objectid' => '',
		'sourceurl' => '',
		'includelink' => '',
		'includesummary' => ''
		), $atts );

	// Check if required parameters are provided
	if ( empty($a['fileobjectid']) ) {
		return '<p>Missing required parameter: fileobjectid</p>';
	}

	$embedURL = $elevatorAPI->getEmbedContent($a['fileobjectid']);

	$returnString = "<span class='elevatorEmbed' style='display:inline-block; width: " . $a['width'] . "px'><iframe width='" . $a['width'] . "' height='" . $a['height'] . "' allowfullscreen=yes frameborder=0 src='" . $embedURL . "'></iframe>";

	$includeSummary = false;
	$includeLink = false;

	if($a['includesummary'] && $a['includesummary'] == "on") {
		$includeSummary = true;
	}

	if($a['includelink'] && $a['includelink'] == "on") {
		$includeLink = true;
	}

	if($includeSummary || $includeLink) {
		$assetInfo = $elevatorAPI->assetPreview($a['objectid']);
		$returnString .= "<div class='metadataSection'>";

		if($includeLink) {
			$title = isset($assetInfo["title"]) ? $assetInfo["title"] : 'Asset';
			$returnString .= "<p class='metadataRow metadataLink'><a href='" . $a['sourceurl'] . "'>" . $title . "</a></p>";
		}

		if(!$includeLink && $includeSummary) {
			$title = isset($assetInfo['title']) ? $assetInfo['title'] : 'Asset';
			$returnString .= "<p class='metadataRow metadataLink'>" . $title . "</p>";
		}

		if ($includeSummary && isset($assetInfo['entries']) && is_array($assetInfo['entries'])){
			foreach($assetInfo['entries'] as $entry) {
				if ( isset($entry["label"]) && isset($entry["entries"]) && is_array($entry["entries"]) ) {
					$returnString .= "<p class='metadataRow metadataEntry'><span class='metadataLabel'>" . $entry["label"] . ":</span> <span class='metadataValue'>" . implode(", ", $entry["entries"]) . "</span></p>";
				}
			}
		}

		$returnString .= "</div>";
	}

	$returnString .= "</span>";
	return $returnString;

}
add_shortcode( 'elevator', 'elevator_func' );


add_action( 'enqueue_block_editor_assets', 'block_editor_assets' );

function block_editor_assets() {
	// Scripts.

	wp_enqueue_script(
		'block-elevator-block-js',
		plugins_url( 'dist/blocks.build.js',  __FILE__  ),
		[ 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ],
		true
	);

	$options = get_option('elevatorasset_global_settings');
	
	// Add null checks for options
	if ( !is_array($options) ) {
		$options = array();
	}
	
	$endpoint = isset($options["endpoint"]) ? $options["endpoint"] : '';
	$key = isset($options["apikey"]) ? $options["apikey"] : '';
	$secret = isset($options["apisecret"]) ? $options["apisecret"] : '';
	$includeLink = isset($options["linktooriginalasset"]) ? $options["linktooriginalasset"] : '';
	$includeSummary = isset($options["includesummary"]) ? $options["includesummary"] : '';

	$script  = 'elevator_settings_endpoint = '. json_encode($endpoint) .'; ';
	$script .= 'key = '. json_encode($key) .'; ';
	$script .= 'secret = '. json_encode($secret) .'; ';
	$script .= 'elevator_settings_includeLink = '. json_encode($includeLink) .'; ';
	$script .= 'elevator_settings_includeSummary = '. json_encode($includeSummary) .'; ';
	
	wp_add_inline_script('block-elevator-block-js', $script, 'before');
	// Styles.
	wp_enqueue_style(
		'elevator-embed-block-editor',
		plugins_url( 'dist/blocks.editor.build.css', __FILE__ ),
		array( 'wp-edit-blocks' ),
		filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' )
	);
}
