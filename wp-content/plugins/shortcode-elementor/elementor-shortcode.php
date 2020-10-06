<?php
/*plugin name: Shortcode Elementor
 * Plugin URI:  https://rstheme.com/products/wordpress/elementor
 * Description: Insert your elementor pages and sections templates anywhere using shortcode
 * Version:     1.0.1
 * Author:      rs-theme
 * Author URI:  https://rstheme.com/
 * Text Domain: rselements
 */
define( 'RS_Elements__FILE__', __FILE__ );
define( 'RS_Elements_PLUGIN_BASE', plugin_basename( RS_Elements__FILE__ ) );
define( 'RS_Elements_URL', plugins_url( '/', RS_Elements__FILE__ ) );
define( 'RS_Elements_PATH', plugin_dir_path( RS_Elements__FILE__ ) );
define( 'RS_Elements_ASSETS_URL', RS_Elements_URL . 'includes/assets/' );

require_once( RS_Elements_PATH . 'includes/post-type.php' );
require_once( RS_Elements_PATH . 'includes/settings.php' );


class RSElements_Elementor_Shortcode{

	function __construct(){
		add_action( 'manage_rs_elements_posts_custom_column' , array( $this, 'rselements_rs_global_templates_columns' ), 10, 2);
		add_filter( 'manage_rs_elements_posts_columns', array($this,'rselements_custom_edit_global_templates_posts_columns' ));		
	}

	function rselements_custom_edit_global_templates_posts_columns($columns) {
		//unset( $columns['author'] );
		$columns['rs_shortcode_column'] = __( 'Shortcode', 'rs_elements_lite' );
		return $columns;
	}


	function rselements_rs_global_templates_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'rs_shortcode_column' :
				echo '<input type=\'text\' class=\'widefat\' value=\'[SHORTCODE_ELEMENTOR id="'.$post_id.'"]\' readonly="">';
				break;
		}
	}
	
}
new RSElements_Elementor_Shortcode();