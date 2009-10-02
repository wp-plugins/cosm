<?php
	/*
	Plugin Name: COSM
	Plugin URI: http://enjoyinc.comoj.com
	Description: COSM is a wordpress plugin that 
		adds Coludmade's Open Street Map (though COSM) 
		services to wordpress. 
		Using: in the post or comment body add [map] tag.
		Attributes for this tag:
		* name - CRITICAL! MUST be UNIQUE for each map
		* width
		* height
		* apikey
		* zoom
		* lat
		* lon
		* style
	Version: 0.2
	Author: Artem Shybovych
	Author URI: http://enjoyinc.comoj.com
	*/
	
	/*  Copyright 2009  shybovycha  (email: shybovycha@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/


add_action('admin_menu', 'mt_add_pages');

function mt_add_pages() {
    add_menu_page('COSM', 'COSM Configuration', 8, __FILE__, 'mt_toplevel_page');
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function mt_toplevel_page() {

    // variables for the field and option names 
    $opt_name = 'cosm_apikey';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'cosm_apikey';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'COSM Options', 'mt_trans_domain' ) . "</h2>";

    // options form
    
    ?>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("API Key:", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p>

</form>
</div>

<?php
 
}


	function map_func($atts, $content = null) {
		extract(shortcode_atts(array(
			'name' => 'cosm_map1',
			'lat' => '0.0',
			'lon' => '0.0',
			'apikey' => get_option( 'cosm_apikey' ),
			'width' => '500',
			'height' => '500',
			'zoom' => '1.0',
			'style' => '10'
		), $atts));

		return "
                    <div id=\"" . $name . "\" style=\"width: " . $width . "px; height: " . $height . "px\"></div>
					<script type=\"text/javascript\" src=\"http://tile.cloudmade.com/wml/latest/web-maps-lite.js\"></script>
					<script type=\"text/javascript\">
						var cloudmade = new CM.Tiles.CloudMade.Web({key: '" . $apikey . "', styleId: " . intval($style) . "});
						var map = new CM.Map('" . $name . "', cloudmade);
						map.setCenter(new CM.LatLng(" . $lat . ", " . $lon . "), " . $zoom . ");
						map.addControl(new CM.LargeMapControl());
					</script>
		       ";
	}
	
	add_shortcode('map', 'map_func');
?>
