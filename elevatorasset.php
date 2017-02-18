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
 * Version:           0.1.2
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
	$endpoint = $options["endpoint"];
	$key = $options["apikey"];
	$secret = $options["apisecret"];

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

	$embedURL = $elevatorAPI->getEmbedContent($a['fileobjectid']);

	if($a['includelink'] || $a['includesummary']) {
		$assetInfo = $elevatorAPI->assetPreview($a['objectid']);
	}
	
	$includeSummary = false;
	if($a['includesummary'] && $a['includesummary'] == "on") {
		$includeSummary = true;
	}

	$includeLink = false;
	if($a['includelink'] && $a['includelink'] == "on") {
		$includeLink = true;
	}

	$returnString = "<span class='elevatorEmbed' style='display:inline-block; width: " . $a['width'] . "px'><iframe width='" . $a['width'] . "' height='" . $a['height'] . "' allowfullscreen=yes frameborder=0 src='" . $embedURL . "'></iframe>";

	if($includeSummary || $includeLink) {
		$returnString .= "<div class='metadataSection'>";
		if($includeLink) {
			$returnString .= "<p class='metadataRow metadataLink'><a href='" . $a['sourceurl'] . "'>" . $assetInfo["title"] . "</a></p>";
		}

		if(!$includeLink && $includeSummary) {
			$returnString .= "<p class='metadataRow metadataLink'>" . $assetInfo['title'] . "</p>";
		}

		if($includeSummary) {
			foreach($assetInfo['entries'] as $entry) {
				$returnString .= "<p class='metadataRow metadataEntry'><span class='metadataLabel'>" . $entry["label"] . ":</span> <span class='metadataValue'>" . implode(", ", $entry["entries"]) . "</span></p>";


			}
		}

		$returnString .= "</div>";


	}

	$returnString .= "</span>";
	return $returnString;

}
add_shortcode( 'elevator', 'elevator_func' );
