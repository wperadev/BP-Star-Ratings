<?php

// Make sure class does not already exist (Playing safe) and that the get function exists
if(!class_exists('bepassivePlugin_bpStarRatings_Widget') && function_exists('bp_star_ratings_get')) :

class bepassivePlugin_bpStarRatings_Widget extends WP_Widget
{
    // Runs when OBJECT DECLARED (Instanciated)
    public function __construct()
    {
        $widget_options = array(
        'classname' => 'bp-star-ratings-widget',
        'description' => 'Show top rated posts'
        );
        parent::__construct('bepassivePlugin_bpStarRatings_Widget', 'BP Star Ratings', $widget_options);
    }
    // USER INTERFACE
    public function widget($args, $instance)
    {
        extract( $args, EXTR_SKIP );
        $title = ( !empty($instance['title']) ) ? $instance['title'] : 'Top Posts';
        $total = ( !empty($instance['noofposts']) ) ? $instance['noofposts'] : '5';
        $category = ( $instance['category'] ) ? $instance['category'] : false;
        $sr = ($instance['showrating']) ? true : false;

        echo $before_widget;
        echo $before_title . $title . $after_title;

        // OUTPUT starts
        $posts = bp_star_ratings_get($total, $category);
        echo '<ul style="list-style: none; background: #fff; padding: 20px; box-shadow: 0px 1px 2px #0001;">';
        foreach ($posts as $post)
        {
           echo "<li><a href='".get_permalink($post->ID)."'>".$post->post_title."</a>";
           if($sr)
           {
               $best = get_option('bpsr_stars');
               echo " <span style='font-size:10px;'>(".$post->ratings."/".$best.")</span>";
           }
           echo "</li>";
        }
        echo '</ul>';
        // OUTPUT ends
        echo $after_widget;
    }
    // Updates OPTIONS
    /*
    public function update()
    {

    }
    */
    // The option FORM
    public function form( $instance )
    {
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:
            <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr(!empty($instance['title'])?$instance['title']: 'Top Posts'); ?>" /></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('noofposts'); ?>">No of Posts:
            <input id="<?php echo $this->get_field_id('noofposts'); ?>" name="<?php echo $this->get_field_name('noofposts'); ?>" type="text" value="<?php echo esc_attr(!empty($instance['noofposts'])?$instance['noofposts']: '5'); ?>" size="3" /></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('showrating'); ?>">Show Average?:
            <select id="<?php echo $this->get_field_id('showrating'); ?>" name="<?php echo $this->get_field_name('showrating'); ?>">
                <option value="0" <?php if(isset($instance['showrating']) && !esc_attr($instance['showrating'])){echo "selected='selected'";} ?>>No</option>
                <option value="1" <?php if(isset($instance['showrating']) && esc_attr($instance['showrating'])){echo "selected='selected'";} ?>>Yes</option>
            </select>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>">Filter by Category:
            <select id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
            <option value="0">Select</option>
            <?php
                foreach(get_categories(array()) as $category)
                {
                    echo '<option value="'.$category->term_id.'"';
                    if(isset($instance['category']) && esc_attr($instance['category'])==$category->term_id)
                    echo ' selected="selected"';
                    echo '>'.$category->name.'</option>';
                }
            ?>
            </select>
            </label>
        </p>
        <?php
    }
}

if(!function_exists('bp_star_ratings_widget_init'))
{
    function bp_star_ratings_widget_init()
    {
        register_widget('bepassivePlugin_bpStarRatings_Widget');
    }
    add_action('widgets_init', 'bp_star_ratings_widget_init');
}

endif;
