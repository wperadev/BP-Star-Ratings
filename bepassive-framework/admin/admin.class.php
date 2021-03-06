<?php

/* -------------------------------------------------------------
----------------------------------------------------------------
|                                                              |
|  File name : admin.class.php                                 |
|  Usage     : Hooks the options/settings into wordpress       |
|  Class     : bepassivePlugin_Admin                            |
|  Version   : 0.1                                             |
|  Author    : WPEra                                      |
|  URI       : http://wp.bepassive.com/framework                |
|                                                              |
|  Description :                                               |
|  Creates the settings page and includes all the neccessary   |
|  HTML to generate the page, including scripts                |
|                                                              |
|  -CHANGELOG-                                                 |
|  ----------------------------------------------------------  |
|  0.1 - First release                                         |
|                                                              |
----------------------------------------------------------------
------------------------------------------------------------- */

if(!class_exists('bepassivePlugin_Admin')) :

    // Declare and define the class.
	class bepassivePlugin_Admin
	{
		private static $id;
		private static $dir;
		private static $ver;
		
		public static function init($id, $dir='', $ver='0.1')
		{
			self::$id = $id;
			self::$dir = $dir;
			self::$ver = $ver;
		}
		/** function/method
		* Usage: return file uri
		* Arg(1): string
		* Return: string
		*/
		public static function file_uri($path, $framework = true)
		{
			return plugins_url(($framework?self::$dir.'/':'').$path, __FILE__);
		}
		/** function/method
		* Usage: return absoulte file path
		* Arg(1): string
		* Return: string
		*/
		public static function file_path($path, $framework = true)
		{
			return dirname(__FILE__).'/'.($framework?self::$dir.'/':'').$path;
		}
		/** function/method
		* Usage: hook js
		* Arg(0): null
		* Return: void
		*/
		public static function js()
		{
			wp_enqueue_script('jquery');

			wp_enqueue_script('media-upload');
			wp_enqueue_script(
			    self::$id.'_colorpicker', 
				self::file_uri('js/colorpicker/js/colorpicker.js'), 
				array('jquery'),
				self::$ver
			);
			wp_enqueue_script(
			    self::$id.'_lightbox', 
				self::file_uri('js/lightbox.js'), 
				array('jquery'),
				self::$ver
			);
			wp_enqueue_script(
			    self::$id.'_admin', 
				self::file_uri('js/admin.js'), 
				array('jquery'),
				self::$ver
			);
			
			$nonce = wp_create_nonce(self::$id);
			$Params = array();
			$Params['nonce'] = $nonce;
			$Params['ajaxurl'] = admin_url('admin-ajax.php');
			$Params['func'] = 'bepassive_admin_ajax';
			
			// do_action
			
			wp_enqueue_script(
			    self::$id.'_script', 
				plugins_url('js/script.js', __FILE__), 
				array('jquery'),
				self::$ver
			);
			wp_localize_script(
			    self::$id.'_script', 
				str_replace('-','_',self::$id).'_script', 
			    $Params
			);
		}
		/** function/method
		* Usage: hook css
		* Arg(0): null
		* Return: void
		*/
		public static function css()
		{	
			wp_register_style(self::$id.'_admin', self::file_uri('css/admin.css'), false, self::$ver);
			wp_enqueue_style(self::$id.'_admin');
			
			wp_register_style(self::$id.'_colorpicker', self::file_uri('js/colorpicker/css/colorpicker.css'), false, self::$ver);
			wp_enqueue_style(self::$id.'_colorpicker');
			
			wp_enqueue_style('thickbox');
		}
		
		public static function scripts()
		{
			self::css();
			self::js();
		}
		
		public static function bepassive_admin_ajax()
		{
			header('content-type: application/json; charset=utf-8');
			check_ajax_referer(self::$id);
			
			unset($_POST['_wpnonce']);
			unset($_POST['action']);
			
			$Options = apply_filters('bf_admin_options',  $_POST);

			foreach($Options as $key => $value)
			{
				update_option($key, $value);
			}
			
			do_action('bf_admin_options_ajax', $Options);
			
			echo json_encode(array('success'=>'true'));
			die();
		}
		
		public static function lightbox_html($footer)
		{
		    ?>
            <div class="bepassive-lightbox">
                <div class="bppopup-bg"></div>
                <div class="bppopup-exit"><a href="#"><img src="<?php echo self::file_uri('images/error.png'); ?>" width="16" height="16" alt="Close" /></a></div>
                <div class="bppopup"></div>
                <span class="bppopup__processing"><img src="<?php echo self::file_uri('images/loading.gif'); ?>" width="50px" height="50px" alt="Proccessing!" class="" /></span>
            </div>
            <div class="bppopup-lightbox bepassive-lightbox"></div>
            <?php	
		}
	}
	
	bepassivePlugin_Admin::init('bepassiveplugin_admin', '', '0.1');
	add_action('admin_enqueue_scripts', array('bepassivePlugin_Admin', 'scripts'));
	add_filter('admin_footer', array('bepassivePlugin_Admin', 'lightbox_html'));
	add_action('wp_ajax_bepassive_admin_ajax', array('bepassivePlugin_Admin', 'bepassive_admin_ajax'));
	
endif;
?>