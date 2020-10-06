<?php
/*
Plugin Name: Theme Dummy Content Importer
Plugin URI: https://1.envato.market/quanticalabs-portfolio
Description: Import posts, pages, comments, custom fields, categories, tags and more from a WordPress export file.
Author: QuanticaLabs
Author URI: https://1.envato.market/quanticalabs-portfolio
Version: 1.6
Text Domain: ql_importer
*/

//translation
function ql_importer_load_textdomain()
{
	load_plugin_textdomain("ql_importer", false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'ql_importer_load_textdomain');
//admin
if(is_admin())
{
	function ql_importer_get_new_widget_name( $widget_name, $widget_index ) 
	{
		$current_sidebars = get_option( 'sidebars_widgets' );
		$all_widget_array = array( );
		foreach ( $current_sidebars as $sidebar => $widgets ) {
			if ( !empty( $widgets ) && is_array( $widgets ) && $sidebar != 'wp_inactive_widgets' ) {
				foreach ( $widgets as $widget ) {
					$all_widget_array[] = $widget;
				}
			}
		}
		while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
			$widget_index++;
		}
		$new_widget_name = $widget_name . '-' . $widget_index;
		return $new_widget_name;
	}
	function ql_importer_download_import_file($file, $themename)
	{	
		if($themename=="cleanmate")
			$url = "http://quanticalabs.com/wptest/cleanmate/files/2017/11/" . $file["name"] . "." . $file["extension"];
		else if($themename=="pressroom")
			$url = "http://quanticalabs.com/wptest/pressroom/files/2015/01/" . $file["name"] . "." . $file["extension"];
		else if($themename=="medicenter")
			$url = "http://quanticalabs.com/wptest/medicenter-new/files/2017/02/" . $file["name"] . "." . $file["extension"];
		else if($themename=="renovate")
			$url = "http://quanticalabs.com/wptest/renovate/files/2015/06/" . $file["name"] . "." . $file["extension"];
		else if($themename=="carservice")
			$url = "http://quanticalabs.com/wptest/carservice/files/2015/05/" . $file["name"] . "." . $file["extension"];
		else if($themename=="gymbase")
			$url = "http://quanticalabs.com/wptest/gymbase/files/2014/07/gymbase-" . $file["name"] . "." . $file["extension"];
		$attachment = get_page_by_title($file["name"], "OBJECT", "attachment");
		if($attachment!=null)
			$id = $attachment->ID;
		else
		{
			$tmp = download_url($url);
			$file_array = array(
				'name' => basename($url),
				'tmp_name' => $tmp
			);

			// Check for download errors
			if(is_wp_error($tmp))
			{
				@unlink($file_array['tmp_name']);
				return $tmp;
			}

			$id = media_handle_sideload($file_array, 0);
			// Check for handle sideload errors.
			if(is_wp_error($id))
			{
				@unlink($file_array['tmp_name']);
				return $id;
			}
		}
		return get_attached_file($id);
	}
	function ql_importer_import_shop_dummy()
	{
		ob_start();
		$result = array("info" => "");
		//import dummy content
		$fetch_attachments = true;
		$file = ql_importer_download_import_file(array(
			"name" => "dummy-shop.xml",
			"extension" => "gz"
		), $_POST["themename"]);
		
		if(is_wp_error($file))
		{
			$file = get_template_directory() . "/dummy_content_files/" . ($themename=="gymbase" ? "gymbase-" : "") . "dummy-shop.xml.gz";
		}
		if(is_file($file))
			require_once('importer.php');
		else
			$result["info"] = __("Import file dummy-shop.xml.gz not found! Please upload import file manually into Media library. You can find this file in 'dummy content files' directory inside zip archive downloaded from ThemeForest.", 'ql_importer');
		if($result["info"]=="")
			$result["info"] = __("dummy-shop.xml file content has been imported successfully!", 'ql_importer');
		$system_message = ob_get_clean();
		$result["system_message"] = $system_message;
		echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
		exit();
	}
	add_action('wp_ajax_ql_importer_import_shop_dummy', 'ql_importer_import_shop_dummy');
	function ql_importer_import_dummy()
	{
		ob_start();
		$themename = $_POST["themename"];
		$result = array("info" => "");
		$import_templates_sidebars = $_POST["import_templates_sidebars"];
		if($import_templates_sidebars)
		{
			echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
			exit();
		}
		//import dummy content
		$fetch_attachments = true;
		$file = ql_importer_download_import_file(array(
			"name" => "dummy-images.xml",
			"extension" => "gz"
		), $_POST["themename"]);
		if(is_wp_error($file))
		{
			$file = get_template_directory() . "/dummy_content_files/" . ($themename=="gymbase" ? "gymbase-" : "") . "dummy-images.xml.gz";
		}
		if(is_file($file))
			require_once('importer.php');
		else
			$result["info"] = __("Import file dummy-images.xml.gz not found! Please upload import file manually into Media library. You can find this file in 'dummy content files' directory inside zip archive downloaded from ThemeForest.", 'ql_importer');
		if($result["info"]=="")
			$result["info"] = __("dummy-images.xml file content has been imported successfully!", 'ql_importer');
		$system_message = ob_get_clean();
		$result["system_message"] = $system_message;
		echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
		exit();
	}
	add_action('wp_ajax_ql_importer_import_dummy', 'ql_importer_import_dummy');

	function ql_importer_import_dummy2()
	{
		ob_start();
		global $wp_filesystem;
		$creds = request_filesystem_credentials(admin_url('themes.php?page=ThemeOptions'), '', false, false, array());
		if(!WP_Filesystem($creds))
		{
			$result["info"] .= __("Filesystem initialization error.", 'ql_importer');
			if(is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->has_errors())
			{
				$error_codes = $wp_filesystem->errors->get_error_codes();
				foreach((array)$error_codes as $error_code)
				{
					if($error_code=="empty_hostname")
					{
						$result["info"] .= __("<br>FTP server details required. Please put below code in your <em>wp-config.php</em> file (replace example details with your FTP server login details):<pre>define('FTP_USER', 'example');<br>define('FTP_PASS', '*******');<br>define('FTP_HOST', 'example.com:21');<br>define('FTP_SSL', false);</pre>", 'ql_importer');
					}
					$result["info"] .= "<br>" . $wp_filesystem->errors->get_error_message($error_code) . " [" . $error_code . "]";
				}
			}
			echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
			exit();
		}
		$themename = $_POST["themename"];
		$theme_prefix = $themename;
		if($themename=="cleanmate")
			$theme_prefix = "cm";
		else if($themename=="pressroom")
			$theme_prefix = "pressroom";
		else if($themename=="medicenter")
			$theme_prefix = "medicenter";
		else if($themename=="renovate")
			$theme_prefix = "renovate";
		else if($themename=="carservice")
			$theme_prefix = "carservice";
		else if($themename=="gymbase")
			$theme_prefix = "gymbase";
		$result = array("info" => "");
		//import dummy content
		$import_templates_sidebars = (int)$_POST["import_templates_sidebars"];
		$fetch_attachments = false;
		$file = ql_importer_download_import_file(array(
			"name" => "dummy-data" . ($import_templates_sidebars ? '-templates-sidebars' : '') . ".xml",
			"extension" => "gz"
		), $themename);
		if(is_wp_error($file))
		{
			$file = get_template_directory() . "/dummy_content_files/" . ($themename=="gymbase" ? "gymbase-" : "") . "dummy-data" . ($import_templates_sidebars ? '-templates-sidebars' : '') . ".xml.gz";
		}
		if(is_file($file))
			require_once('importer.php');
		else
		{
			$result["info"] .= sprintf(__("Import file: dummy-data%s.xml.gz not found! Please upload import file manually into Media library. You can find this file in 'dummy content files' directory inside zip archive downloaded from ThemeForest.", 'ql_importer'), ($import_templates_sidebars ? '-templates-sidebars' : ''));
			echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
			exit();
		}
		if($import_templates_sidebars)
		{
			$result["info"] .= __("Template pages and sidebars has been imported successfully!", 'ql_importer');
			echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
			exit();
		}
		//set menu
		$locations = get_theme_mod('nav_menu_locations');
		$menus = wp_get_nav_menus();
		foreach($menus as $menu)
			$locations[$menu->slug] = $menu->term_id;
		
		set_theme_mod('nav_menu_locations', $locations);
		//set front page
		$home = get_page_by_title('HOME');
		update_option('page_on_front', $home->ID);
		update_option('show_on_front', 'page');
		//set blog description
		if($themename=="cleanmate")
			update_option("blogdescription", "Cleaning Company Maid Gardening Theme");
		else if($themename=="pressroom")
			update_option("blogdescription", "News and Magazine Theme");
		else if($themename=="medicenter")
			update_option("blogdescription", "Health Medical Clinic WordPress Theme");
		else if($themename=="renovate")
			update_option("blogdescription", "Construction Renovation Theme");
		else if($themename=="carservice")
			update_option("blogdescription", "Mechanic Auto Shop Theme");
		else if($themename=="gymbase")
			update_option("blogdescription", "Gym Fitness WordPress Theme");
		//set top and menu sidebars
		$theme_sidebars_array = get_posts(array(
			'post_type' => $theme_prefix . '_sidebars',
			'posts_per_page' => '-1',
			'nopaging' => true,
			'post_status' => 'publish',
			'orderby' => 'menu_order',
			'order' => 'ASC'
		));
		$theme_options = get_option($theme_prefix . "_options", true);
		$needed_id = 0;
		foreach($theme_sidebars_array as $theme_sidebar)
		{	
			if(($themename=="medicenter" && $theme_sidebar->post_title=="Sidebar Header Top") || (($themename=="cleanmate" || $themename=="pressroom" || $themename=="renovate" || $themename=="carservice") && $theme_sidebar->post_title=="Sidebar Header"))
			{
				$needed_id = $theme_sidebar->ID;
				break;
			}
		}
		$theme_options["header_top_sidebar"] = $needed_id;
		if($themename=="cleanmate")
		{
			$needed_id = 0;
			foreach($theme_sidebars_array as $theme_sidebar)
			{	
				if($theme_sidebar->post_title=="Sidebar Menu")
				{
					$needed_id = $theme_sidebar->ID;
					break;
				}
			}
			$theme_options["header_menu_sidebar"] = $needed_id;
		}
		else if($themename=="pressroom")
		{
			$needed_id = 0;
			foreach($theme_sidebars_array as $theme_sidebar)
			{	
				if($theme_sidebar->post_title=="Sidebar Header Right")
				{
					$needed_id = $theme_sidebar->ID;
					break;
				}
			}
			$theme_options["header_top_right_sidebar"] = $needed_id;
		}
		update_option($theme_prefix . "_options", $theme_options);
		
		if(class_exists("RevSlider") && ($themename=="cleanmate" || $themename=="medicenter" || $themename=="renovate" || $themename=="carservice" || $themename=="gymbase"))
		{
			//slider import
			$Slider=new RevSlider();
			$slider_file = ql_importer_download_import_file(array(
				"name" => ($themename=="renovate" || $themename=="carservice" ? "main" : ($themename=="gymbase" ? "gymbase-" : "") . "home"),
				"extension" => "zip"
			), $themename);
			if(is_wp_error($slider_file))
			{
				$slider_file = get_template_directory() . "/dummy_content_files/" . ($themename=="renovate" || $themename=="carservice" ? "main" : ($themename=="gymbase" ? "gymbase-" : "") . "home") . ".zip";
			}
			if(is_file($slider_file))
			{
				$Slider->importSliderFromPost(true, true, $slider_file);
			}
			if($themename=="cleanmate")
			{
				$slider_file_2 = ql_importer_download_import_file(array(
					"name" => "home-2",
					"extension" => "zip"
				), $themename);
				if(is_wp_error($slider_file_2))
				{
					$slider_file_2 = get_template_directory() . "/dummy_content_files/home-2.zip";
				}
				if(is_file($slider_file_2))
				{
					$Slider->importSliderFromPost(true, true, $slider_file_2);
				}
				//update default global grid size
				$revslider_global_settings = get_option('revslider-global-settings');
				$revslider_grid = array(
					'size' => array(
						'desktop' => 1240,
						'notebook' => 1173,
						'tablet' => 751,
						'mobile' => 463
					)
				);
				update_option('revslider-global-settings', json_encode(array_merge((array)json_decode($revslider_global_settings, true), $revslider_grid)));
			}
			else if($themename=="carservice" || $themename=="renovate")
			{
				//update default global grid size
				$revslider_global_settings = get_option('revslider-global-settings');
				$revslider_grid = array(
					'size' => array(
						'desktop' => 1240,
						'notebook' => 1173,
						'tablet' => 751,
						'mobile' => 463
					)
				);
				update_option('revslider-global-settings', json_encode(array_merge((array)json_decode($revslider_global_settings, true), $revslider_grid)));
			}
			else if($themename=="gymbase")
			{
				//update default global grid size
				$revslider_global_settings = get_option('revslider-global-settings');
				$revslider_grid = array(
					'size' => array(
						'desktop' => 1240,
						'notebook' => 1233,
						'tablet' => 751,
						'mobile' => 463
					)
				);
				update_option('revslider-global-settings', json_encode(array_merge((array)json_decode($revslider_global_settings, true), $revslider_grid)));
			}
			else if($themename=="medicenter")
			{
				//update default global grid size
				$revslider_global_settings = get_option('revslider-global-settings');
				$revslider_grid = array(
					'size' => array(
						'desktop' => 1240,
						'notebook' => 1233,
						'tablet' => 993,
						'mobile' => 751
					)
				);
				update_option('revslider-global-settings', json_encode(array_merge((array)json_decode($revslider_global_settings, true), $revslider_grid)));
			}
		}
		
		//widget import
		$response = array(
			'what' => 'widget_import_export',
			'action' => 'import_submit'
		);

		$widgets = isset( $_POST['widgets'] ) ? $_POST['widgets'] : false;
		$json_file = ql_importer_download_import_file(array(
			"name" => "widget_data",
			"extension" => "json"
		), $themename);
		if(is_wp_error($json_file))
		{
			$json_file = get_template_directory() . "/dummy_content_files/" . ($themename=="gymbase" ? "gymbase-" : "") . "widget_data.json";
		}
		$json_data = $wp_filesystem->get_contents($json_file);
		if($json_data!=false)
		{
			$json_data = json_decode( $json_data, true );
			$sidebars_data = $json_data[0];
			$widget_data = $json_data[1];
			$current_sidebars = get_option( 'sidebars_widgets' );
			//remove inactive widgets
			$current_sidebars['wp_inactive_widgets'] = array();
			update_option('sidebars_widgets', $current_sidebars);
			$new_widgets = array( );
			foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

				foreach ( $import_widgets as $import_widget ) :
					//if the sidebar exists
					//if ( isset( $current_sidebars[$import_sidebar] ) ) :
						$title = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
						$index = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
						$current_widget_data = get_option( 'widget_' . $title );
						$new_widget_name = ql_importer_get_new_widget_name( $title, $index );
						$new_index = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

						if ( !empty( $new_widgets[ $title ] ) && is_array( $new_widgets[$title] ) ) {
							while ( array_key_exists( $new_index, $new_widgets[$title] ) ) {
								$new_index++;
							}
						}
						$current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
						if ( array_key_exists( $title, $new_widgets ) ) {
							$new_widgets[$title][$new_index] = $widget_data[$title][$index];
							$multiwidget = $new_widgets[$title]['_multiwidget'];
							unset( $new_widgets[$title]['_multiwidget'] );
							$new_widgets[$title]['_multiwidget'] = $multiwidget;
						} else {
							$current_widget_data[$new_index] = $widget_data[$title][$index];
							$current_multiwidget = isset($current_widget_data['_multiwidget']) ? $current_widget_data['_multiwidget'] : "";
							$new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : "";
							$multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
							unset( $current_widget_data['_multiwidget'] );
							$current_widget_data['_multiwidget'] = $multiwidget;
							$new_widgets[$title] = $current_widget_data;
						}

					//endif;
				endforeach;
			endforeach;
			if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
				update_option( 'sidebars_widgets', $current_sidebars );

				foreach ( $new_widgets as $title => $content ) {
					$content["_multiwidget"] = 1;
					$content = apply_filters( 'widget_data_import', $content, $title );
					update_option( 'widget_' . $title, $content );
				}

			}
		}
		else
		{
			$result["info"] .= __("Widgets data file not found! Please upload widgets data file manually.", 'ql_importer');
			echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
			exit();
		}
		if($result["info"]=="")
		{
			//set shop page
			$shop = get_page_by_title('Shop');
			update_option('woocommerce_shop_page_id', $shop->ID);
			//set my-account page
			$myaccount = get_page_by_title('My Account');
			update_option('woocommerce_myaccount_page_id', $myaccount->ID);
			//set cart page
			$cart = get_page_by_title('Cart');
			update_option('woocommerce_cart_page_id', $cart->ID);
			//set checkout page
			$checkout = get_page_by_title('Checkout');
			update_option('woocommerce_checkout_page_id', $checkout->ID);
			
			$hide_notice = sanitize_text_field("install");
			$notices = array_diff(get_option('woocommerce_admin_notices', array()), array("install"));
			update_option('woocommerce_admin_notices', $notices);
			do_action('woocommerce_hide_install_notice');
			$result["info"] = sprintf(__("dummy-data%s.xml file content and widgets settings has been imported successfully!", 'ql_importer'), ($import_templates_sidebars ? '-templates-sidebars' : ''));;
			$system_message = ob_get_clean();
			$result["system_message"] = $system_message;
		}
		echo "dummy_import_start" . json_encode($result) . "dummy_import_end";
		exit();
	}
	add_action('wp_ajax_ql_importer_import_dummy2', 'ql_importer_import_dummy2');
}