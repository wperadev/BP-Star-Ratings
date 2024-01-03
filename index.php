<?php

/*
Plugin Name: BP Star Ratings
Plugin URI: https://github.com/wperadev/bp-star-ratings/
Description: BP Star Ratings help you to animated and light weight ratings feature for your blog. With BP Star Ratings, you can <strong>allow your blog posts,pages,archives,store,product to be rated by your blog visitors</strong>. It also includes a <strong>widget</strong> which you can add to your sidebar to show the top rated post. Enjoy the extensive options you can set to customize this plugin.
Version: 1.8
Author: WPEra
Author URI: https://wpera.com/
License: GPLv2 or later
*/

require_once 'bepassive-framework/plugin.php';

if(!class_exists('bepassivePlugin_bpStarRatings')) :

    class bepassivePlugin_bpStarRatings extends bepassivePlugin
    {
        private $_Menus;

        public function __construct($id, $nick, $ver)
        {
            parent::__construct($id, $nick, $ver);
            $this->_Menus = array();
        }
        /**
          * File uri
          *
          * @since 1.0 Initially defined
          *
          * @param string $path Path to file.
          *
          * @return string full uri.
          */
        public static function file_uri($path)
        {
            return plugins_url($path, __FILE__);
        }
        /**
          * File path
          *
          * @since 1.0 Initially defined
          *
          * @param string $path Path to file.
          *
          * @return string full path.
          */
        public static function file_path($path)
        {
            return dirname(__FILE__).'/'.$path;
        }
        /** function/method
        * Usage: hook js frontend
        * Arg(0): null
        * Return: void
        */
        public function js()
        {
            $nonce = wp_create_nonce($this->id);
            $Params = array();
            $Params['nonce'] = $nonce; //for security
            $Params['grs'] = parent::get_options('bpsr_grs') ? true : false;
            $Params['ajaxurl'] = admin_url('admin-ajax.php');
            $Params['func'] = 'bpsr_ajax';
            $Params['msg'] = parent::get_options('bpsr_init_msg');
            $Params['suffix_votes'] = parent::get_options('bpsr_suffix_votes');
            $Params['fuelspeed'] = (int) parent::get_options('bpsr_js_fuelspeed');
            $Params['thankyou'] = parent::get_options('bpsr_js_thankyou');
            $Params['error_msg'] = parent::get_options('bpsr_js_error');
            $Params['tooltip'] = parent::get_options('bpsr_tooltip');
            $Params['tooltips'] = parent::get_options('bpsr_tooltips');
            $this->enqueue_js('js', self::file_uri('assets/js/js.min.js'), $this->ver, array('jquery'), $Params, false, true);
        }
        /** function/method
        * Usage: hook js admin - helper
        * Arg(0): null
        * Return: void
        */
        public function js_admin()
        {
            $nonce = wp_create_nonce($this->id);
            $Params = array();
            $Params['nonce'] = $nonce;
            $Params['func_reset'] = 'bpsr_admin_reset_ajax';
            $this->enqueue_js('js_admin', self::file_uri('assets/js/js_admin.js'), $this->ver, array('jquery', 'bepassiveplugin_admin_script'), $Params);
        }
        /** function/method
        * Usage: hook admin scripts
        * Arg(0): null
        * Return: void
        */
        public function admin_scripts()
        {
            foreach($this->_Menus as $menu)
            {
                add_action('admin_print_scripts-'.$menu, array(&$this, 'js_admin'));
            }
        }
        /** function/method
        * Usage: hook css
        * Arg(0): null
        * Return: void
        */
        public function css()
        {
            $this->enqueue_css('', self::file_uri('assets/css/css.css'));
        }
        /** function/method
        * Usage: hook custom css
        * Arg(0): null
        * Return: void
        */
        public function css_custom()
        {
            $stars = parent::get_options('bpsr_stars') ? parent::get_options('bpsr_stars') : 5;

            $star_w = parent::get_options('bpsr_stars_w') ? parent::get_options('bpsr_stars_w') : 24;
            $star_h = parent::get_options('bpsr_stars_h') ? parent::get_options('bpsr_stars_h') : 24;
            $star_rat_leg_inline = parent::get_options('bpsr_rating_legend_inline');
            $star_style = parent::get_options('bpsr_rating_strs_style');

            echo '<style>';
            echo '.bp-star-ratings .bpsr-star.gray{background-size:'.$star_h.'px '.$star_w.'px !important;width:'.($star_w*$stars).'px !important;}';
            echo '.bp-star-ratings .bpsr-star.orange{background-size:'.$star_h.'px '.$star_w.'px !important;}';
            echo $star_rat_leg_inline? '.bp-star-ratings {display:flex;width:'.($star_w*$stars+150).'px !important;}' : '.bp-star-ratings {width:'.($star_w*$stars+150).'px !important;}';
            echo '.bp-star-ratings .bpsr-stars a {width:'.($star_w).'px; height:'.($star_h).'px;}';
            echo '.bp-star-ratings .bpsr-stars, .bp-star-ratings .bpsr-stars .bpsr-fuel, .bp-star-ratings .bpsr-stars a { height:'.($star_h).'px; }';
            echo $star_style ? '.bp-star-ratings .bpsr-star.yellow { background-image: url('.plugins_url('assets/images/'.$star_style, __FILE__).'.png); background-size:'.$star_h.'px '.$star_w.'px; }' : '';

            echo '</style>';
        }
        /** function/method
        * Usage: Setting defaults and backwards compatibility
        * Arg(0): null
        * Return: void
        */
        public function activate()
        {
            $ver_current = $this->ver;
            $ver_previous = parent::get_options('bpsr_ver') ? parent::get_options('bpsr_ver') : false;
            $Old_plugin = parent::get_options('bp-ratings');

            $opt_enable = 1; // 1|0
            $opt_clear = 0; // 1|0
            $opt_show_in_home = 0; // 1|0
            $opt_show_in_archives = 1; // 1|0
            $opt_show_in_posts = 1; // 1|0
            $opt_show_in_pages = 1; // 1|0
            $opt_google_snippets = 1; // 1|0
            $opt_unique = 0; // 1|0
            $bpsr_only_login_user_vote = 0; // 1|0
            $opt_reting_str = 'yellow_star'; // 1|0
            $bpsr_rating_legend_inline = 1; // 1|0
            $opt_position = 'top-left'; // 'top-left', 'top-right', 'bottom-left', 'bottom-right'
	        $bpsr_sufix_votes = 's'; // 's' in english for voteS
            $opt_legend = '[avg] / [best] ( [total] vote[suffix] )';
            $opt_init_msg = 'Rate this post'; // string
            $opt_column = 1; // 1|0

            $Options = array();
            $Options['bpsr_enable'] = isset($Old_plugin['enable']) ? $Old_plugin['enable'] : $opt_enable;
            $Options['bpsr_clear'] = isset($Old_plugin['clear']) ? $Old_plugin['clear'] : $opt_clear;
            $Options['bpsr_show_in_home'] = isset($Old_plugin['show_in_home']) ? $Old_plugin['show_in_home'] : $opt_show_in_home;
            $Options['bpsr_show_in_archives'] = isset($Old_plugin['show_in_archives']) ? $Old_plugin['show_in_archives'] : $opt_show_in_archives;
            $Options['bpsr_show_in_posts'] = isset($Old_plugin['show_in_posts']) ? $Old_plugin['show_in_posts'] : $opt_show_in_posts;
            $Options['bpsr_show_in_pages'] = isset($Old_plugin['show_in_pages']) ? $Old_plugin['show_in_pages'] : $opt_show_in_pages;
            $Options['bpsr_unique'] = isset($Old_plugin['unique']) ? $Old_plugin['unique'] : $opt_unique;
            $Options['bpsr_only_login_user_vote'] = isset($Old_plugin['only_login_user_vote']) ? $Old_plugin['only_login_user_vote'] : $bpsr_only_login_user_vote;
            $Options['bpsr_grs'] = isset($Old_plugin['grs']) ? $Old_plugin['grs'] : $opt_google_snippets;
            $Options['bpsr_rating_strs_style'] = isset($Old_plugin['rating_strs_style']) ? $Old_plugin['rating_strs_style'] : $opt_reting_str;
            $Options['bpsr_rating_legend_inline'] = isset($Old_plugin['rating_legend_inline']) ? $Old_plugin['rating_legend_inline'] : $bpsr_rating_legend_inline;
            $Options['bpsr_position'] = isset($Old_plugin['position']) ? $Old_plugin['position'] : $opt_position;
	        $Options['bpsr_suffix_votes'] = isset($Old_plugin['bpsr_suffix_votes']) ? $Old_plugin['bpsr_suffix_votes'] : $bpsr_sufix_votes;
            $Options['bpsr_legend'] = isset($Old_plugin['legend']) ? $Old_plugin['legend'] : $opt_legend;
            $Options['bpsr_init_msg'] = isset($Old_plugin['init_msg']) ? $Old_plugin['init_msg'] : $opt_init_msg;
            $Options['bpsr_column'] = isset($Old_plugin['column']) ? $Old_plugin['column'] : $opt_column;

            // Upgrade from old plugin(<1.3)
            if(!$ver_previous || version_compare($ver_previous, '1.3', '<'))
            {
                // Delete old options
                parent::delete_options('bp-ratings');
                // Update previous ratings
                global $wpdb;
                $table = $wpdb->prefix . 'postmeta';
                $Posts = $wpdb->get_results("SELECT a.ID, b.meta_key, b.meta_value
                                             FROM " . $wpdb->posts . " a, $table b
                                             WHERE a.ID=b.post_id AND
                                             (
                                                 b.meta_key='_bp_ratings_ratings' OR
                                                 b.meta_key='_bp_ratings_casts' OR
                                                 b.meta_key='_bp_ratings_ips'
                                             ) ORDER BY a.ID ASC");
                $Wrap = array();
                foreach ($Posts as $post)
                {
                    $Wrap[$post->ID]['id'] = $post->ID;
                    $Wrap[$post->ID][$post->meta_key] = $post->meta_value;
                }
                foreach($Wrap as $p)
                {
                    update_post_meta($p['id'], '_bpsr_ratings', $p['_bp_ratings_ratings']);
                    update_post_meta($p['id'], '_bpsr_casts', $p['_bp_ratings_casts']);
                    $Ips = array();
                    $Ips = explode('|', $p['_bp_ratings_ips']);
                    $ip = $Ips;
                    update_post_meta($p['id'], '_bpsr_ips', $ip);
                    update_post_meta($p['id'], '_bpsr_avg', round($p['_bp_ratings_ratings']/$p['_bp_ratings_casts'],1));
                }
            }
            if(!parent::get_options('bpsr_ver'))
            {
                $Options['bpsr_ver'] = $ver_current;
                $Options['bpsr_stars'] = 5;
                $Options['bpsr_stars_w'] = 24;
                $Options['bpsr_stars_h'] = 24;
                $Options['bpsr_stars_gray'] = 0;
                $Options['bpsr_stars_yellow'] = 0;
                $Options['bpsr_stars_orange'] = 0;
                $Options['bpsr_js_fuelspeed'] = 400;
                $Options['bpsr_js_thankyou'] = 'Thank you for your vote';
                $Options['bpsr_js_error'] = 'An error occurred';
                $Options['bpsr_tooltip'] = 0;
                $Opt_tooltips = array();
                $Opt_tooltips[0]['color'] = 'red';
                $Opt_tooltips[0]['tip'] = 'Poor';
                $Opt_tooltips[1]['color'] = 'brown';
                $Opt_tooltips[1]['tip'] = 'Fair';
                $Opt_tooltips[2]['color'] = 'orange';
                $Opt_tooltips[2]['tip'] = 'Average';
                $Opt_tooltips[3]['color'] = 'blue';
                $Opt_tooltips[3]['tip'] = 'Good';
                $Opt_tooltips[4]['color'] = 'green';
                $Opt_tooltips[4]['tip'] = 'Excellent';
                $Options['bpsr_tooltips'] = $Opt_tooltips;
                parent::update_options($Options);
            }
            parent::update_options(array('bpsr_ver'=>$ver_current));
        }
        /** function/method
        * Usage: helper for hooking (registering) the menu
        * Arg(0): null
        * Return: void
        */
        public function menu()
        {
            // Create main menu tab
            $this->_Menus[] = add_menu_page(
                $this->nick.' - Settings',
                $this->nick,
                'manage_options',
                $this->id.'_settings',
                array(&$this, 'options_general'),
                self::file_uri('assets/images/icon.png')
            );
            // Create images menu tab
            $this->_Menus[] = add_submenu_page(
                $this->id.'_settings',
                $this->nick.' - Settings',
                'General',
                'manage_options',
                $this->id.'_settings',
                array(&$this, 'options_general')
            );
            // Create images menu tab
            $this->_Menus[] = add_submenu_page(
                $this->id.'_settings',
                $this->nick.' - Stars',
                'Stars',
                'manage_options',
                $this->id.'_settings_stars',
                array(&$this, 'options_stars')
            );
            // Create tooltips menu tab
            $this->_Menus[] = add_submenu_page(
                $this->id.'_settings',
                $this->nick.' - Tooltips',
                'Tooltips',
                'manage_options',
                $this->id.'_settings_tooltips',
                array(&$this, 'options_tooltips')
            );
            // Create reset menu tab
            $this->_Menus[] = add_submenu_page(
                $this->id.'_settings',
                $this->nick.' - Reset',
                'Reset',
                'manage_options',
                $this->id.'_settings_reset',
                array(&$this, 'options_reset')
            );
            // Create info menu tab
            $this->_Menus[] = add_submenu_page(
                $this->id.'_settings',
                $this->nick.' - Help',
                'Help',
                'manage_options',
                $this->id.'_settings_info',
                array(&$this, 'options_info')
            );
        }
        /** function/method
        * Usage: show options/settings form page
        * Arg(0): null
        * Return: void
        */
        public function options_page($opt)
        {
            if (!current_user_can('manage_options'))
            {
                wp_die( __('You do not have sufficient permissions to access this page.') );
            }
            $sidebar = true;
            $bp_Title = 'BP Star Ratings';
            $Url = array(
                // array(
                //     'title' => 'Github Repository',
                //     'link' => 'https://github.com/wperadev/bp-star-ratings'
                // ),
                // array(
                // 	'title' => 'Changelog',
                //     'link' => '#'
                // )
            );
            include self::file_path('admin.php');
        }
        /** function/method
        * Usage: show general options
        * Arg(0): null
        * Return: void
        */
        public function options_general()
        {
            $this->options_page('general');
        }
        /** function/method
        * Usage: show images options
        * Arg(0): null
        * Return: void
        */
        public function options_stars()
        {
            $this->options_page('stars');
        }
        /** function/method
        * Usage: show tooltips options
        * Arg(0): null
        * Return: void
        */
        public function options_tooltips()
        {
            $this->options_page('tooltips');
        }
        /** function/method
        * Usage: show reset options
        * Arg(0): null
        * Return: void
        */
        public function options_reset()
        {
            $this->options_page('reset');
        }
        /** function/method
        * Usage: show info options
        * Arg(0): null
        * Return: void
        */
        public function options_info()
        {
            $this->options_page('info');
        }


        public function bpsr_admin_reset_ajax()
        {
            header('content-type: application/json; charset=utf-8');
            check_ajax_referer($this->id);

            $Reset = $_POST['bpsr_reset'];
            if(is_array($Reset))
            {
                foreach($Reset as $id => $val)
                {
                    if($val=='1')
                    {
                        delete_post_meta($id, '_bpsr_ratings');
                        delete_post_meta($id, '_bpsr_casts');
                        delete_post_meta($id, '_bpsr_ips');
                        delete_post_meta($id, '_bpsr_avg');
                    }
                }
            }

            $Response = array();
            $Response['success'] = 'true';
            echo json_encode($Response);
            die();
        }
        public function extract_ratings($id)
        {
            $best = (int) parent::get_options('bpsr_stars');
            $score = get_post_meta($id, '_bpsr_ratings', true) ? ((int) get_post_meta($id, '_bpsr_ratings', true)) : 0;
            $votes = get_post_meta($id, '_bpsr_casts', true) ? ((int) get_post_meta($id, '_bpsr_casts', true)) : 0;
            $avg = $score && $votes ? round((float)(($score/$votes)*($best/5)), 1) : 0;
            $per = $score && $votes ? round((float)((($score/$votes)/5)*100), 2) : 0;

            return compact('best', 'score', 'votes', 'avg', 'per');
        }
        public function ratings_as_legend($id, $ratings = null)
        {
            $ratings = $ratings ? $ratings : $this->extract_ratings($id);

            return apply_filters(
                'bpsr_legend',
                parent::get_options('bpsr_legend'),
                $id,
                $ratings['best'],
                $ratings['score'],
                $ratings['votes'],
                $ratings['avg'],
                $ratings['per']
            );
        }
        public function bpsr_ajax()
        {
            header('Content-type: application/json; charset=utf-8');
            check_ajax_referer($this->id);

            if (empty($_POST['id'])) {
                die();
            }
            $Response = array();

            $total_stars = is_numeric(parent::get_options('bpsr_stars')) ? parent::get_options('bpsr_stars') : 5;

            $stars = is_numeric($_POST['stars']) && ((int)$_POST['stars']>0) && ((int)$_POST['stars']<=$total_stars)
                    ? $_POST['stars']:
                    0;
            // GDPR: Create SHA256 hash of ip address before storing it
            $ip = hash('sha256', $_SERVER['REMOTE_ADDR']);

            $Ids = explode(',', sanitize_text_field($_POST['id']));

            foreach($Ids as $pid) :

            $ratings = get_post_meta($pid, '_bpsr_ratings', true) ? get_post_meta($pid, '_bpsr_ratings', true) : 0;
            $casts = get_post_meta($pid, '_bpsr_casts', true) ? get_post_meta($pid, '_bpsr_casts', true) : 0;

            if($stars==0 && $ratings==0)
            {
                $Response[$pid]['legend'] = parent::get_options('bpsr_init_msg');
                $Response[$pid]['disable'] = 'false';
                $Response[$pid]['fuel'] = '0';
                do_action('bpsr_init', $pid, false, false);
            }
            else
            {
                $nratings = $ratings + ($stars/($total_stars/5));
                $ncasts = $casts + ($stars>0);
                $avg = $nratings && $ncasts ? number_format((float)($nratings/$ncasts), 2, '.', '') : 0;
                $per = $nratings && $ncasts ? number_format((float)((($nratings/$ncasts)/5)*100), 2, '.', '') : 0;
                $Response[$pid]['disable'] = 'false';
                if($stars)
                {
                    $Ips = get_post_meta($pid, '_bpsr_ips', true) ? get_post_meta($pid, '_bpsr_ips', true) : array();
                    if(!in_array($ip, $Ips))
                    {
                        $Ips[] = $ip;
                    }
                    $ips = $Ips;
                    update_post_meta($pid, '_bpsr_ratings', $nratings);
                    update_post_meta($pid, '_bpsr_casts', $ncasts);
                    update_post_meta($pid, '_bpsr_ips', $ips);
                    update_post_meta($pid, '_bpsr_avg', $avg);
                    $Response[$pid]['disable'] = parent::get_options('bpsr_unique') ? 'true' : 'false';
                    do_action('bpsr_rate', $pid, $stars, $ip);
                }
                else
                {
                    do_action('bpsr_init', $pid, number_format((float)($avg*($total_stars/5)), 2, '.', '').'/'.$total_stars, $ncasts);
                }
                $Response[$pid]['meta'] = $this->extract_ratings($pid);
                $Response[$pid]['legend'] = $this->ratings_as_legend($pid, $Response[$pid]['meta']);
                $Response[$pid]['fuel'] = (float) $per;
            }

            $Response[$pid]['success'] = true;
            endforeach;
            echo json_encode($Response);
            die();
        }
        protected function trim_csv_cb($value)
        {
            if(trim($value)!="")
                return true;
            return false;
        }
        protected function exclude_cat($id)
        {
            $excl_categories = parent::get_options('bpsr_exclude_categories');
            $Cat_ids = $excl_categories ? array_filter(array_map('trim', explode(",", $excl_categories)), array(&$this, 'trim_csv_cb')) : array();
            $Post_cat_ids = wp_get_post_categories($id);
            $Intersection = array_intersect($Cat_ids, $Post_cat_ids);
            return count($Intersection);
        }
        public function markup($id=false)
        {
            $id = !$id ? get_the_ID() : $id;
            if($this->exclude_cat($id))
            {
                return '';
            }

            $disabled = false;
            if(get_post_meta($id, '_bpsr_ips', true))
            {
                $Ips = get_post_meta($id, '_bpsr_ips', true);
                // GDPR: Create SHA256 hash of ip address before storing it
                $ip = hash('sha256', $_SERVER['REMOTE_ADDR']);
                if(in_array($ip, $Ips))
                {
                    $disabled = parent::get_options('bpsr_unique') ? true : false;
                }
            }
            // Check Archive and get id
            if (is_archive()) {
                $id = get_queried_object_id();
            }

            $pos = parent::get_options('bpsr_position');

            $markup = '
            <div class="bp-star-ratings '.($disabled || (is_archive() && parent::get_options('bpsr_disable_in_archives')) ? 'disabled ' : ' ').$pos.($pos=='top-right'||$pos=='bottom-right' ? ' rgt' : ' lft').'" data-id="'.$id.'">
                <div class="bpsr-stars bpsr-star gray">
                    <div class="bpsr-fuel bpsr-star '.($disabled ? 'orange' : 'yellow').'" style="width:0%;"></div>
                    <!-- bpsr-fuel -->';
                    $total_stars = parent::get_options('bpsr_stars');
                    $bpsr_only_login_user_vote = parent::get_options('bpsr_only_login_user_vote');

                    for($ts = 1; $ts <= $total_stars; $ts++)
                    {
                        if ($bpsr_only_login_user_vote){
                            if ( is_user_logged_in() ) {
                                $markup .= '<a href="#'.$ts.'"></a>';
                            }
                        }else{
                          $markup .= '<a href="#'.$ts.'"></a>';
                        }
                    }
                    $markup .='
                        </div>
                        <!-- bpsr-stars -->
                        <div class="bpsr-legend">';

                    $markup .= $this->ratings_as_legend($id);

                    $markup .=
                '</div>
                <!-- bpsr-legend -->
            </div>
            <!-- bp-star-ratings -->
            ';
            $markup .= parent::get_options('bpsr_clear') ? '<br clear="both" />' : '';
            return $markup;
        }
        public function manual($atts)
        {
            extract(shortcode_atts(array('id' => false), $atts));
            if(!is_admin() && parent::get_options('bpsr_enable'))
            {
                if(
                    ((parent::get_options('bpsr_show_in_home')) && (is_front_page() || is_home()))
                    || ((parent::get_options('bpsr_show_in_archives')) && (is_archive()))
                  )
                    return $this->markup($id);
                else if(is_single() || is_page())
                    return $this->markup($id);
            }
            else
            {
                remove_shortcode('bpratings');
                remove_shortcode('bpstarratings');
            }
            return '';
        }
        public function filter($content)
        {
            if(parent::get_options('bpsr_enable')) :
            if(
                ((parent::get_options('bpsr_show_in_home')) && (is_front_page() || is_home()))
                || ((parent::get_options('bpsr_show_in_archives')) && (is_archive()))
                || ((parent::get_options('bpsr_show_in_posts')) && (is_single()))
                || ((parent::get_options('bpsr_show_in_pages')) && (is_page()))
              ) :
                remove_shortcode('bpratings');
                remove_shortcode('bpstarratings');
                $markup = $this->markup();
                if (strpos($content, '[bpratings]') !== false
                    || strpos($content, '[bpstarratings]') !== false
                ) {
                    $markup = '<div style="display: inline-block">' . $markup . '</div>';
                    $content = str_replace('[bpratings]', $markup, $content);
                    $content = str_replace('[bpstarratings]', $markup, $content);
                    return $content;
                }
                switch(parent::get_options('bpsr_position'))
                {
                    case 'bottom-left' :
                    case 'bottom-right' : return $content . $markup;
                    default : return $markup . $content;
                }
            endif;
            endif;
            return $content;
        }
        public function bp_star_rating($pid=false)
        {
            if(parent::get_options('bpsr_enable'))
                return $this->markup($pid);
            return '';
        }
        public function bp_star_ratings_get($total=5, $cat=false)
        {
            global $wpdb;
            $table = $wpdb->prefix . 'postmeta';
            $best = (int) parent::get_options('bpsr_stars');
            $q = "SELECT a.ID, a.post_title, ROUND(b.meta_value * %f, 1) AS 'ratings' FROM " . $wpdb->posts . " a, $table b, ";
            if(!$cat) {
                $query = $wpdb->prepare("$q $table c WHERE a.post_status='publish' AND a.ID=b.post_id AND a.ID=c.post_id AND b.meta_key='_bpsr_avg' AND c.meta_key='_bpsr_casts' ORDER BY CAST(b.meta_value AS UNSIGNED) DESC, CAST(c.meta_value AS UNSIGNED) DESC LIMIT %d", $best / 5, $total);
                $rated_posts = $wpdb->get_results($query);
            } else
            {
                $table2 = $wpdb->prefix . 'term_taxonomy';
                $table3 = $wpdb->prefix . 'term_relationships';
                $query = $wpdb->prepare("$q $table2 c, $table3 d, $table e WHERE c.term_taxonomy_id=d.term_taxonomy_id AND c.term_id=$cat AND d.object_id=a.ID AND a.post_status='publish' AND a.ID=b.post_id AND a.ID=e.post_id AND b.meta_key='_bpsr_avg' AND e.meta_key='_bpsr_casts' ORDER BY CAST(b.meta_value AS UNSIGNED) DESC, CAST(e.meta_value AS UNSIGNED) DESC LIMIT %d", $best / 5, $total);
                $rated_posts = $wpdb->get_results($query);
            }

            return $rated_posts;
        }
        public function add_column($Columns)
        {
            if(parent::get_options('bpsr_column'))
                $Columns['bp_star_ratings'] = 'Ratings';
            return $Columns;
        }
        public function add_row($Columns, $id)
        {
            if(! parent::get_options('bpsr_column'))
            {
                return;
            }

            $row = 'No ratings';

            $ratings = $this->extract_ratings($id);
            if ($ratings['score']) {
                $row = $this->sanitize_legend(
                    parent::get_options('bpsr_legend'),
                    $id,
                    $ratings['best'],
                    $ratings['score'],
                    $ratings['votes'],
                    $ratings['avg'],
                    $ratings['per']
                );
            }

            switch($Columns)
            {
                case 'bp_star_ratings' : echo $row; break;
            }
        }
        /** function/method
        * Usage: Allow sorting of columns
        * Arg(1): $Args (array)
        * Return: (array)
        */
        public function sort_columns($Args)
        {
            $Args = array_merge($Args,
                array('bp_star_ratings' => 'bp_star_ratings')
            );
            return wp_parse_args($Args);
        }
        /** function/method
        * Usage: Allow sorting of columns - helper
        * Arg(1): $Query (array)
        * Return: null
        */
        public function sort_columns_helper($Query)
        {
            if(!is_admin())
            {
                return;
            }
            $orderby = $Query->get( 'orderby');
            if($orderby=='bp_star_ratings')
            {
                $Query->set('meta_key','_bpsr_avg');
                $Query->set('orderby','meta_value_num');
            }
        }
        public function sanitize_legend($legend, $id, $best, $score, $votes, $avg, $per)
        {
            if(!$score)
            {
                return parent::get_options('bpsr_init_msg');
            }

            $pluralSuffix = parent::get_options('bpsr_suffix_votes');

            $leg = str_replace('[total]', '<span itemprop="ratingCount">'.$votes.'</span>', $legend);
            $leg = str_replace('[avg]', '<span itemprop="ratingValue">'.$avg.'</span>', $leg);
            $leg = str_replace('[per]',  $per .'%', $leg);
            $leg = str_replace('[suffix]', $votes == 1 ? '' : $pluralSuffix, $leg);
            $leg = str_replace('[best]', $best, $leg);

            return $leg;
        }
        public function grs_legend($legend, $id, $best, $score, $votes, $avg, $per)
        {
            if(!parent::get_options('bpsr_grs') || !$score)
            {
                return $legend;
            }

            $title = get_the_title($id);
            if(empty($title)){
                $title = "Rating ";
            }

            $snippet = '<div itemscope itemtype="https://schema.org/Product">';
            $snippet .= '  <span itemprop="name" class="bpsr-title">' . $title . '</span>';
            $snippet .= '  <link itemprop="image" href="#" />';
            $snippet .= '  <meta itemprop="mpn" content="'.$id.'" />';
            $snippet .= '  <meta itemprop="description" content="'.$title.'" />';
            $snippet .= '  <span itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">';
            $snippet .=      $legend;
            $snippet .= '  </span>';
            $snippet .= '  <meta itemprop="sku" content="'.$id.'" />';
            $snippet .= '  <div itemprop="brand" itemtype="http://schema.org/Brand" itemscope>';
            $snippet .= '    <meta itemprop="name" content="'.$title.'" />';
            $snippet .= '  </div>';
            $snippet .= '  <div itemprop="review" itemtype="http://schema.org/Review" itemscope>';
            $snippet .= '    <div itemprop="author" itemtype="http://schema.org/Person" itemscope>';
            $snippet .= '      <meta itemprop="name" content="'.get_the_author().'" />';
            $snippet .= '    </div>';
            $snippet .= '  </div>';
            $snippet .= '  <div itemprop="offers" itemtype="http://schema.org/AggregateOffer" itemscope>';
            $snippet .= '    <meta itemprop="lowPrice" content="0" />';
            $snippet .= '    <meta itemprop="highPrice" content="0" />';
            $snippet .= '    <meta itemprop="offerCount" content="6" />';
            $snippet .= '    <meta itemprop="priceCurrency" content="USD" />';
            $snippet .= '  </div>';
            $snippet .= '</div>';

            return $snippet;
        }
    }
    $bpStarRatings_obj = new bepassivePlugin_bpStarRatings('bepassive_plugin_bpsr', 'BP Star Ratings', '1.4');

    // Setup
    register_activation_hook(__FILE__, array($bpStarRatings_obj, 'activate'));

    //Uninstall
	// TODO: include 'register_uninstall_hook() '

    // Scripts
    add_action('wp_enqueue_scripts', array($bpStarRatings_obj, 'js'));
    add_action('wp_enqueue_scripts', array($bpStarRatings_obj, 'css'));
    add_action('wp_head', array($bpStarRatings_obj, 'css_custom'));
    add_action('admin_init', array($bpStarRatings_obj, 'admin_scripts'));

    // Menu
    add_action('admin_menu', array($bpStarRatings_obj, 'menu'));

    // AJAX
    add_action('wp_ajax_bpsr_admin_reset_ajax', array($bpStarRatings_obj, 'bpsr_admin_reset_ajax'));
    add_action('wp_ajax_bpsr_ajax', array($bpStarRatings_obj, 'bpsr_ajax'));
    add_action('wp_ajax_nopriv_bpsr_ajax', array($bpStarRatings_obj, 'bpsr_ajax'));

    // Main Hooks
    add_filter('the_content', array($bpStarRatings_obj, 'filter'));
    add_shortcode('bpratings', array($bpStarRatings_obj, 'manual'));
    add_shortcode('bpstarratings', array($bpStarRatings_obj, 'manual'));

    // Google Rich Snippets
    add_filter('bpsr_legend', array($bpStarRatings_obj, 'sanitize_legend'), 10, 7);
    add_filter('bpsr_legend', array($bpStarRatings_obj, 'grs_legend'), 10, 7);

    // Posts/Pages Column
    add_filter( 'manage_posts_columns', array($bpStarRatings_obj, 'add_column') );
    add_filter( 'manage_pages_columns', array($bpStarRatings_obj, 'add_column') );
    add_filter( 'manage_posts_custom_column', array($bpStarRatings_obj, 'add_row'), 10, 2 );
    add_filter( 'manage_pages_custom_column', array($bpStarRatings_obj, 'add_row'), 10, 2 );
    add_filter( 'manage_edit-post_sortable_columns', array($bpStarRatings_obj, 'sort_columns') );
    add_filter( 'pre_get_posts', array($bpStarRatings_obj, 'sort_columns_helper') );

    // For use in themes
    if(!function_exists('bp_star_ratings'))
    {
        function bp_star_ratings($pid=false)
        {
            global $bpStarRatings_obj;
            return $bpStarRatings_obj->bp_star_rating($pid);
        }
    }
    if(!function_exists('bp_star_ratings_get'))
    {
        function bp_star_ratings_get($lim=5, $cat=false)
        {
            global $bpStarRatings_obj;
            return $bpStarRatings_obj->bp_star_ratings_get($lim, $cat);
        }
    }

    require_once 'shortcode/shortcode.php';
    require_once 'widget.php';

endif;
