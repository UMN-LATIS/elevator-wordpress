<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   elevatorasset
 * @author    Colin McFadden <mcfa0086@umn.edu>
 * @license   GPL-2.0+
 * @link      http://github.com/umn-latis/
 * @copyright 2017
 */
?>
<h3><?php esc_attr_e( 'Elevator Asset Browser', 'elevator' ); ?></h3>

<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<!--h2><?php esc_attr_e( 'Plugin Setting', 'elevatorasset' ); ?></h2-->

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<div class="handlediv" title="Click to toggle"><br></div>
						<!-- Toggle -->

						<h3 class="hndle"><span><?php esc_attr_e( 'Plugin Settings', 'elevatorasset' ); ?></span>
						</h3>

						<div class="inside">
							<?php
							$sections = array(
								array(
									'id'    => 'elevatorasset_global_settings',
									'title' => __( 'Settings', 'elevatorasset' )
								)
							);


							//var_dump($roles);
							$fields = array(
								'elevatorasset_global_settings' => array(
									array(
										'name'      => 'endpoint',
										'label'     => __( 'Elevator API Endpoint', 'elevatorasset' ),
										'desc'      => __( 'Enter your Elevator API Endpoint','elevatorasset' ),
										'type'      => 'text',
										'default'   => 'http://',

									),
									array(
										'name'      => 'apikey',
										'label'     => __( 'Elevator API Key', 'elevatorasset' ),
										'desc'      => __( 'Enter your Elevator API Key','elevatorasset' ),
										'type'      => 'text',
										'default'   => '',
									),
									array(
										'name'      => 'apisecret',
										'label'     => __( 'Elevator API Secret', 'elevatorasset' ),
										'desc'      => __( 'Enter your Elevator API Secret','elevatorasset' ),
										'type'      => 'text',
										'default'   => '',
									),
									array(
										'name'      => 'includesummary',
										'label'     => __( 'Include Summary Text', 'elevatorasset' ),
										'desc'      => __( 'Include title and basic asset summary below asset','elevatorasset' ),
										'type'      => 'checkbox',
										'default'   => '',
									),
									array(
										'name'      => 'linktooriginalasset',
										'label'     => __( 'Link to Original Asset', 'elevatorasset' ),
										'desc'      => __( 'Include link to original asset (and title) below asset','elevatorasset' ),
										'type'      => 'checkbox',
										'default'   => '',
									)
								)

							);

							$settings_api->set_sections( $sections );
							$settings_api->set_fields( $fields );

							//initialize them
							$settings_api->admin_init();
							$settings_api->show_navigation();
							$settings_api->show_forms();

							?>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->



		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->


