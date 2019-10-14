<?php

function register_dc_advanced_mailchimp_widget() {
    register_widget( 'dc_mailchimp_advanced_signup' );
}
add_action( 'widgets_init', 'register_dc_advanced_mailchimp_widget' );

global $MailChimp;
function wtf() {

        add_action( 'wp_ajax_nopriv_dc_outside_class',  'outside_handler' );
        add_action( 'wp_ajax_dc_outside_class',  'outside_handler' );
    
}

add_action( 'init', 'wtf' );
function outside_handler() {

    $email = $_POST['email'];
    $dummy = new dc_mailchimp_advanced_signup();
    $settings = $dummy->get_settings();
    $settings = reset($settings);
    $api_key = $settings['api_key'];
    $list_id = $settings['list_id'];

    $list_name = $dummy->get_list_name( $api_key, $list_id );
    
    $results = $dummy->add_subscriber( $api_key, $list_id, $email );

    switch( $results['status'] ) {
        case 'subscribed' :
            echo "Thanks! We've added you to our email list.";
            break;
        case '400':
            if ( $results['title'] == 'Member Exists' ){
                echo 'Looks like you\'re already on our mailing list. If you\'re not receiving emails, try checking your Junk or Spam folder. If you\'re still not receiving them, <a href="' . site_url() . '/contact/" data-remodal-target="dc-contact">get in touch with us</a>, and we\'ll see if we can\'t figure it out!';
            } else if ( $results['title'] == 'Invalid Resource' ) {
                echo "Whoops, looks like you may have entered an invalid email address - why don't you try again?";
            }
            break;

        default: 
            echo "Unsure what happened. Here's a printout: " . var_dump($results, 1);
    }    

    wp_die();
}

class dc_mailchimp_advanced_signup extends WP_Widget {

    var $api_key;
    static $test;
    
	public function __construct() {
        $widget_ops = array('classname' => 'widget_text-DC col-md-12 ', 'description' => __('MailChimp signup form, with a block of text.'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('dc-advanced-mailchimp-signup', __('Advanced MailChimp signup & text'), $widget_ops, $control_ops);
        add_action( 'wp_ajax_dc_inside_class',  array( $this, 'add_subscriber' ) ) ;
    }
    
    public function get_settings() {
        $settings['api_key'] = '48654c2255a0989c231108fd68b37fdb-us10';
        $settings['list_id'] = 'fe5db3739d';

        return $settings;
    }
    
    function get_lists( $api_key='' ) {
        global $MailChimp;
        if ( $MailChimp ) {

            $lists = $MailChimp->get('lists');
            return $lists['lists'];
            
        } else if ( $api_key ) {
            
            include( PLUGIN_PATH . 'libs/MailChimp/MailChimp.php' );
            $MailChimp = new MailChimp( $api_key );
                
                $lists = $MailChimp->get('lists');
                return $lists['lists'];
                
        }
    }

    function get_list_name($api_key, $list_id ){
        global $MailChimp;
        if ( $MailChimp ) {

            
            $list = $MailChimp->get('lists/'.$list_id);
            return $list['name'];
            
        } else if ( $api_key ) {
            include( PLUGIN_PATH . 'libs/MailChimp/MailChimp.php' );
            $MailChimp = new MailChimp( $api_key );
            $list = $MailChimp->get('lists/'.$list_id);
            return $list['name'];
            
        }
    }

    function add_subscriber( $api_key='', $list_id='', $email='' ) {

        global $MailChimp;

        if ( $MailChimp ) {
            
            $result = $MailChimp->post("lists/$list_id/members", [
                    'email_address' => $email,
                    'status'        => 'subscribed',
                ]);
            return $result;
            
        } else if ( $api_key && $list_id && $email ) {
            
            //include( PLUGIN_PATH . 'libs/MailChimp/MailChimp.php' );
            $MailChimp = new MailChimp( $api_key );
            $result = $MailChimp->post("lists/$list_id/members", [
                    'email_address' => $email,
                    'status'        => 'subscribed',
                ]);
            return $result;

        } else return "Nothing at all!";
    }
    
	public function widget( $args, $instance ) {

		$text_class = apply_filters( 'dc-advanced-mailchimp-signup', empty( $args['text_container_class'] ) ? '' : $args['text_container_class'], $args );
		$form_class = apply_filters( 'dc-advanced-mailchimp-signup', empty( $args['form_container_class'] ) ? '' : $args['form_container_class'], $args );
		$title = empty( $instance['title'] ) ? '' : $instance['title'];
        $list_id = $instance['list_id'] ? $instance['list_id'] : false;
		$before_text = apply_filters( 'dc_advanced_mailchimp_signup-before_text', empty( $args['before_text'] ) ? '<div class="dc-advanced-mailchimp-signup widget-text">' : $args['before_text'], $args );
		$after_text = apply_filters( 'dc_advanced_mailchimp_signup-after_text', empty( $args['after_text'] ) ? '</div>' : $args['after_text'], $args );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
        $email_placeholder = apply_filters( 'dc_advanced_mailchimp-email_placeholder', empty( $args['email_placeholder'] ) ? 'Email Address' : $args['email_placeholder'], $args );

		echo $args['before_widget'];

        $settings = $this->get_settings();

        // var_dump($settings, 1);
        
        ?>
                
        <!-- text container -->
        <div class="<?php echo $text_class; ?>">
            <?php
                if ( ! empty( $title ) ) {
                    echo $args['before_title'] . $title . $args['after_title'];
                } ?>
            <?php echo $before_text; ?>
            <?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?>
            <?php echo $after_text; ?>
        </div>
        <!-- end text container -->
        
        <?php 

        if ( $list_id ) { ?>
            <!-- sign-up form container -->
            <div class="<?php echo $form_class; ?>" >

                <?php include( PLUGIN_PATH . 'templates/signup-form.php' ); ?>

            </div>
            <!-- end sign-up form container -->
        
            <?php
        }
   
        echo $args['after_widget'];
        
	}

	public function update( $new_instance, $old_instance ) {

        include( PLUGIN_PATH . 'libs/MailChimp/MailChimp.php');

        global $MailChimp;
		$instance = $old_instance;
        $instance['api_key'] = $new_instance['api_key'];
        $instance['list_id'] = $new_instance['list_id'];
        $instance['list_name'] = $new_instance['list_name'];
		$instance['title'] = $new_instance['title'];
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = ! empty( $new_instance['filter'] );
        if ( $instance['api_key'] ) {
            $MailChimp = new MailChimp( $instance['api_key'] );
        }

		return $instance;
	}
        
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'form_action' => '', 'api_key' => '', 'list_id' => '', 'list_name' => '') );
		$title = $instance['title'];
		$text = esc_textarea($instance['text']);
        $form_action = $instance['form_action'];
        $api_key = $instance['api_key'];
        $list_id = $instance['list_id'];
        $list_name = $instance['list_name'];
        ?>

        <!-- instructions -->
        <h4>Instructions</h4>
        <p>
            Enter your MailChimp API key below. You can get your API Key <a href="http://admin.mailchimp.com/account/api-key-popup" target="_blank" >here</a>. After entering the key, you'll need to save the widget in order to grab the lists on your MailChimp account. After the widget refeshes, choose the list that you'll want to add subscribers to. Once you've filled out the rest of the info in this widget, hit save. If all went well, you'll see the List ID &amp; List Name populated below the dropdown menu.
        </p>

        <hr />
        
        <!-- API key-->
        <p>
            <label for="<?php echo $this->get_field_id('api_key') ?>"><?php _e('MailChimp API Key:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo $api_key; ?>" />
            
        </p>

        <!--Lists-->
        <?php if ( $api_key ){
            $lists = $this->get_lists( $api_key );
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('list_id') ?>"><?php _e('List:'); ?></label> 
                <select id="<?php echo $this->get_field_id('list_id'); ?>" name="<?php echo $this->get_field_name('list_id'); ?>" >
                    <option>------</option>
                    <?php
                    if ( $lists ) {
                        foreach ($lists as $key => $index ) {
                            $name = $index['name'];
                            $id = $index['id'];
                            echo "<option value=\"$id\" " . ($list_id == $id ? 'selected="selected"' : '' ) . ">$name</option> ";
                        }
                    } ?>
                </select>
                <input type="hidden" id="<?php echo $this->get_field_id('list_name'); ?>" name="<?php echo $this->get_field_id('list_name'); ?>" value="<?php echo $list_name; ?>" />
            </p>

            <hr />

            <p>
                List ID: <span id="list-test"><?php echo $list_id; ?></span><br />
                
                List Name: <?php echo $this->get_list_name( $api_key, $list_id ); ?>
            </p>
            
            <hr />

            <?php 
        } // end if ( $api_key) ?>
        
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>

        <?php
    }
    
}