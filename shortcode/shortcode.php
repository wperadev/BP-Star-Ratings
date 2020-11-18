<?php

if(!class_exists('bepassivePlugin_bpStarRatings_Shortcode')) :
    // Declare and define the class.
	class bepassivePlugin_bpStarRatings_Shortcode
	{	
		
		static public function tinymce_add_button()
		{
			if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
				return;

			if ( get_user_option('rich_editing') == 'true') 
			{
				add_filter("mce_external_plugins", array("bepassivePlugin_bpStarRatings_Shortcode","tinymce_custom_plugin"));
				add_filter('mce_buttons', array("bepassivePlugin_bpStarRatings_Shortcode",'tinymce_register_button'));
			}
		}
			 
		static public function tinymce_register_button($buttons) 
		{
			array_push($buttons, "|", "bpstarratings");
			return $buttons;
		}
			 
		static public function tinymce_custom_plugin($plugin_array) 
		{
			//echo WP_PLUGIN_URL.'/bp-star-ratings/shortcode/mce/bpstarratings/editor_plugin.js';
			//$plugin_array['bpstarratings'] = WP_PLUGIN_URL.'/bp-star-ratings/shortcode/mce/bpstarratings/editor_plugin.js';
			$plugin_array['bpstarratings'] = bepassivePlugin_bpStarRatings::file_uri('shortcode/mce/bpstarratings/editor_plugin.js');
			return $plugin_array;
		}
	}
	
	add_action('init', array('bepassivePlugin_bpStarRatings_Shortcode','tinymce_add_button'));

endif;
?>