<div class="bepassive-framework">
    <?php if(isset($sidebar)) : ?>
	<div class="bf-wrap-small _right">

        <!-- Github star -->
        <div class="bf_box">
            <a class="github-button" href="https://github.com/wperadev/bp-star-ratings" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star bepassive/bp-star-ratings on GitHub">
                BP Star Ratings
            </a>
            <script async defer src="https://buttons.github.io/buttons.js"></script>
        </div>

        <!-- Github star -->
        <div class="bf_box">
            <a href="https://wpera.com/docs/bp-star-ratings/" class="bf-save " target="_blank">
                <b>Documentation</b>
            </a>
        </div>

        <!-- Email Subscribe -->
        <div class="bf_box">
            <form action="" method="post">
                <div class="form-control">
                    <input type="email" name="emailaddress" placeholder="Enter Email Address" class="input-control" required>
                    <br>
                    <br>
                    <input type="submit" value="Subscribe" class="input-group" name="subscribebtn">
                </div>
            </form>
            <?php
            if(isset($_POST['subscribebtn'])){
            //user posted variables
              $email = sanitize_email($_POST['emailaddress']);
              $message = "New Subscribe Mail Account is: ".$email;

            //php mailer variables
              $to = 'rakib@wpera.com';
              $subject = "BP Stare Rating New Subscribe!";
              $headers = 'From: '. $email . "\r\n" .
                'Reply-To: ' . $email . "\r\n";

            //Here put your Validation and send mail
            $sent = wp_mail($to, $subject, strip_tags($message), $headers);
                  if($sent) {
                    echo 'Thank You for Subscribe!';
                  }//message sent!
                  else  {
                      echo 'Message Send Failed.';
                  }//message wasn't sent
            }
            ?>
        </div>
        <br><br>
    </div>
    <!-- bf-wrap-small -->
    <?php endif; ?>
	<div class="bf-wrap">
        <div class="bf_logo">
            <h3>
                <?php echo $bp_Title; ?>
            </h3>
        </div>
        <!--bf_logo-->
    <div class="bf_head">
        <ul class="bf_navs">
            <li<?php echo ($opt=='general')?' class="active"':''; ?>><a href="#Settings">General</a></li>
            <li<?php echo ($opt=='stars')?' class="active"':''; ?>><a href="#Stars">Stars</a></li>
            <li<?php echo ($opt=='tooltips')?' class="active"':''; ?>><a href="#Tooltips">Tooltips</a></li>
            <li<?php echo ($opt=='reset')?' class="active"':''; ?>><a href="#Reset">Reset</a></li>
            <li<?php echo ($opt=='info')?' class="active"':''; ?>><a href="#Help">Help</a></li>
        </ul>
        <ul class="bf_navs _right">
            <li class="bf-save "><a href="#" rel="save-options">Save</a></li>
        </ul>
        <!--bf_navs-->
    </div>
    <!--bf_head-->
    <form method="post" action="" name="bf_form">
    <!--    General Option    -->
    <div class="bf_container __settings <?php echo ($opt=='general')?'__active':''; ?>">
        <?php

            bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Active Rating',
				'description' => 'Choose Whether You Want to Enable or Disable the Plugin',
				'obj' => array(
					array(
						'field' => 'bpsr_enable',
						'label' => 'Enable',
						'value' => get_option('bpsr_enable')
				    )
				)
			));
			bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Auto Placement',
				'description' => 'Choose Where You Want the Ratings to Be Auto Placed',
				'obj' => array(
					array(
						'field' => 'bpsr_show_in_homebpsr_show_in_home',
					    'label' => 'Show on Home Page',
					    'value' => get_option('bpsr_show_in_home')
				    ),
				    array(
						'field' => 'bpsr_show_in_archives',
					    'label' => 'Show in Archives',
					    'value' => get_option('bpsr_show_in_archives')
				    ),
				    array(
						'field' => 'bpsr_show_in_posts',
					    'label' => 'Show in Posts',
					    'value' => get_option('bpsr_show_in_posts')
				    ),
				    array(
						'field' => 'bpsr_show_in_pages',
					    'label' => 'Show in Pages',
					    'value' => get_option('bpsr_show_in_pages')
				    ),
					array(
						'field' => 'bpsr_disable_in_archives',
					    'label' => 'Disable voting in archives',
					    'value' => get_option('bpsr_disable_in_archives')
				    )
				)
			));
            bepassivePlugin_AdminMarkup::select(array(
                'title' => 'Rating Position',
                'description' => 'Choose the Right Position of the Ratings, Where You Want to Show',
                'field' => 'bpsr_position',
                'options' => array(
                    array('top-left','Top Left'),
                    array('top-right','Top Right'),
                    array('bottom-left','Bottom Left'),
                    array('bottom-right','Bottom Right'),
                ),
                'value' => get_option('bpsr_position')
            ));
			bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Exclude Following Category',
				'description' => 'Comma seperated Ids of categories.<br />Example: <em>10,33,12</em>',
				'field' => 'bpsr_exclude_categories',
				'value' => get_option('bpsr_exclude_categories')
			));
			bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Google Rich Snippets',
				'description' => 'Do You Want Google Index the Ratings and Hopefully Show It in the Search Results',
				'obj' => array(
					array(
						'field' => 'bpsr_grs',
					    'label' => 'Enable',
					    'value' => get_option('bpsr_grs')
				    )
			    )
		    ));
			bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Unique Voting',
				'description' => 'Choose Whether You Want Unique Votings Based on Ip Address',
				'obj' => array(
					array(
						'field' => 'bpsr_unique',
					    'label' => 'Unique based on User IP',
					    'value' => get_option('bpsr_unique')
				    )
			    )
		    ));
			bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Only Login User Voting',
				'description' => 'Choose Whether You Want Voting Only Login User',
				'obj' => array(
					array(
						'field' => 'bpsr_only_login_user_vote',
					    'label' => 'Only Login User Can Vote',
					    'value' => get_option('bpsr_only_login_user_vote')
				    )
			    )
		    ));
		    bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Clear line & Space Bottom',
				'description' => 'Choose Whether You Want the Ratings to Be on Its Own Line Rather Than Floated and Space Bottom',
				'obj' => array(
					array(
						'field' => 'bpsr_clear',
					    'label' => 'Clear',
					    'value' => get_option('bpsr_clear')
				    )
			    )
		    ));
		    bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Admin Screen Column',
				'description' => 'Choose Whether You Want a Ratings Column in the Admin Post/Page Screen',
				'obj' => array(
					array(
						'field' => 'bpsr_column',
					    'label' => 'Admin Posts/Pages Column',
					    'value' => get_option('bpsr_column')
				    )
			    )
		    ));
		    bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Legend (Rating Values)',
				'description' => '
									How Do You Want the Legend of the Ratings to Be Shown? <br />
									<strong>Variables</strong> <br />
                                    <code>[best]</code> = Maximum Stars <br />
									<code>[total]</code> = Total Ratings <br />
									<code>[avg]</code> = Average Rating<br />
									<code>[per]</code> = Rating Percentage <br />
									<code>[suffix]</code> = For Plural Vote Text <br />
									<strong>NOTE</strong> <br />
									<code>[total]</code> and <code>[avg]</code> Is Mandatory for Google Rich Snippets to Work, So Shuld Be Use Those 
								',
				'field' => 'bpsr_legend',
				'value' => get_option('bpsr_legend')
			));
          bepassivePlugin_AdminMarkup::input(array(
                    'title' => 'Plural Suffix for Number of Votes',
                    'description' => 'Adjust the Suffix for <code>[suffix]</code> Placeholder.',
                    'field' => 'bpsr_suffix_votes',
                    'value' => get_option('bpsr_suffix_votes')
                ));
			bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Speed of Fueling (in Milliseconds)',
				'description' => 'Adjust the Speed of the Fueling of Stars in Milliseconds With Animation',
				'field' => 'bpsr_js_fuelspeed',
				'value' => get_option('bpsr_js_fuelspeed')
			));
			bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Initial Text',
				'description' => 'Text to Be Displayed When There Are No Ratings',
				'field' => 'bpsr_init_msg',
				'value' => get_option('bpsr_init_msg')
			));
			bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Thank You Message',
				'description' => 'Text to Be Displayed When User Places a Vote',
				'field' => 'bpsr_js_thankyou',
				'value' => get_option('bpsr_js_thankyou')
			));
			bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Error Message',
				'description' => 'Text to Be Displayed When Something Goes Wrong Unexpectidly',
				'field' => 'bpsr_js_error',
				'value' => get_option('bpsr_js_error')
			));
	    ?>
    </div>
    <!--bf_container __general-->

    <!--    Stars Option    -->
    <div class="bf_container __stars <?php echo ($opt=='stars')?'__active':''; ?>">
        <?php
        	bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Total Stars',
				'description' => 'How Many Stars Do You Want to Show in the Ratings? Enter a Numeric Value',
				'field' => 'bpsr_stars',
				'value' => get_option('bpsr_stars')
			));
        	bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Width of Single Star',
				'description' => 'Set the Width of a Single Star in Pixels(Px).',
				'field' => 'bpsr_stars_w',
				'value' => get_option('bpsr_stars_w')
			));
			bepassivePlugin_AdminMarkup::input(array(
				'title' => 'Height of single star',
				'description' => 'Set the Height of a Single Star in Pixels(Px).',
				'field' => 'bpsr_stars_h',
				'value' => get_option('bpsr_stars_h')
			));
            bepassivePlugin_AdminMarkup::checkbox(array(
                'title' => 'Inline Rating Style',
                'description' => 'Choose When You Want to Show Stars and Legend Inline',
                'obj' => array(
                    array(
                        'field' => 'bpsr_rating_legend_inline',
                        'label' => 'Enable',
                        'value' => get_option('bpsr_rating_legend_inline')
                    )
                )
            ));
            bepassivePlugin_AdminMarkup::selectStarStyle(array(
                'title' => 'Start Style',
                'description' => 'Choose the Rating Color',
                'field' => 'bpsr_rating_strs_style',
                'options' => array(
                    array('green_star','★★★★★ (Green)'),
                    array('blue_star','★★★★★ (Blue)'),
                    array('orange_star','★★★★★ (Orange)'),
                    array('violet_star','★★★★★ (Violet)'),
                    array('red_star','★★★★★ (Red)'),
                    array('dark_star','★★★★★ (Dark)'),
                    array('yellow_star','★★★★★ (Yellow)')
                ),
                'value' => get_option('bpsr_rating_strs_style')
            ));
	    ?>
    </div>
    <!--bf_container __stars-->

    <!--    Tooltips Option    -->
    <div class="bf_container __tooltips <?php echo ($opt=='tooltips')?'__active':''; ?>">
        <?php
	        bepassivePlugin_AdminMarkup::checkbox(array(
				'title' => 'Tooltips',
				'description' => 'Choose Whether You Want to Enable or Disable the Tooltips',
				'obj' => array(
					array(
						'field' => 'bpsr_tooltip',
						'label' => 'Enable Tooltips',
						'value' => get_option('bpsr_tooltip')
				    )
				)
			));
			$Tooltips = get_option('bpsr_tooltips');
			for($tooltip_i=0;$tooltip_i<get_option('bpsr_stars');$tooltip_i++)
			{
				bepassivePlugin_AdminMarkup::input(array(
					'title' => 'Tooltip - star '.($tooltip_i+1),
					'description' => 'Displayed When Mouse is Hovered Over Star '.($tooltip_i+1),
					'field' => 'bpsr_tooltips['.($tooltip_i).'][tip]',
					'value' => isset($Tooltips[$tooltip_i]['tip']) ? $Tooltips[$tooltip_i]['tip'] : ''
				));
				bepassivePlugin_AdminMarkup::color(array(
					'title' => 'Tooltip Color - star '.($tooltip_i+1),
					'description' => 'Choose Color for Tooltip of Star '.($tooltip_i+1),
					'field' => 'bpsr_tooltips['.($tooltip_i).'][color]',
					'label' => 'Choose a color',
					'value' => isset($Tooltips[$tooltip_i]['color']) ? $Tooltips[$tooltip_i]['color'] : '#ffffff'
				));
			}
	    ?>
    </div>
    <!--bf_container __tooltips-->

    <!--   Reset Option    -->
    <div class="bf_container __reset <?php echo ($opt=='reset')?'__active':''; ?>">
        <?php
		    global $wpdb;
			$table = $wpdb->prefix . 'postmeta';
			$Posts = $wpdb->get_results("SELECT a.ID, a.post_title
										 FROM " . $wpdb->posts . " a, $table b
										 WHERE a.ID=b.post_id AND
										 b.meta_key='_bpsr_ratings'
										 ORDER BY a.ID ASC");
			if(is_array($Posts))
			{
				$Obj = array();
				foreach($Posts as $post)
				{
					$Obj[] = array(
						'field' => 'bpsr_reset['.$post->ID.']',
						'label' => $post->post_title,
						'class' => '_bpsr_reset'
				    );
				}
				if(count($Obj))
				{
					bepassivePlugin_AdminMarkup::html('<p>Select the Posts/Pages and Click the Reset Button to Reset Their Ratings.</p>
												<p>
												<a href="#" rel="bpsr-reset-all" class="button">Select All</a>
												<a href="#" rel="bpsr-reset-none" class="button">Unselect All</a>
												<a href="#" rel="bpsr-reset" class="button-primary" style="color:white;">Reset</a>
												</p>'
												);
					bepassivePlugin_AdminMarkup::checkbox(array(
						'title' => '',
						'description' => '',
						'pclass' => '_left',
						'obj' => $Obj
					));
				}
				else
				{
					bepassivePlugin_AdminMarkup::html('No Ratings Have Been Placed.');
				}
			}
			else
			{
				bepassivePlugin_AdminMarkup::html('No Ratings Have Been Placed.');
			}
	    ?>
    </div>
    <!--bf_container __reset-->

    <!--    Help Option    -->
    <div class="bf_container __help <?php echo ($opt=='info')?'__active':''; ?>">
    	<?php
    		bepassivePlugin_AdminMarkup::html(
    			'<p>
				    <strong>To manually use in your post/page using admin screen, use the star icon in your post/page editor</strong>
                    <br /><br />
                    <strong>For use in theme files:</strong>
                    <br /> <code class="_block">&lt;?php if(function_exists("bp_star_ratings")) : echo bp_star_ratings(); endif; ?&gt;</code>
					<br /><br />
                    <strong>Use ShortCode In Post, Page or Other Text:</strong>
                    <br /> <code class="_block"> [bpstarratings] </code>
					<br /><br />
                    <strong>Use Widget:</strong>
                    <br />
                    <span class="_block">Use "BP Star Ratings" Widget For Show Top Rated Post. <br />For Use Widget Goto: <i><code> Appearance > Widgets > BP Star Ratings </code></i> 
                    </span>
				 </p>'
    		);
    	?>
    </div>
    <!--bf_container __help-->
	</form>
    </div>

    <!-- bf-wrap -->
</div>
<!--bepassive-framework-->
