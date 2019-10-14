<?php
/**
 * Shortcode to display a MailChimp sign up form.
 *
 * @version		1.0.0
 * @package		MailChimp for WP
 * @subpackage  shortcode
 * @category	supporting functionality
 * @author 		Digitally Cultured
 */

add_shortcode('mailchimp-wp','mailchimp_signup'); 

function mailchimp_signup( $atts ) {
    
    // extract(shortcode_atts(array(
    //     'api_key'  => '',
    //     'list_id'   =>  ''
    // ), $atts));

    include( PLUGIN_PATH . 'templates/signup-form.php' ); 
    
}


function widget ( $atts ) {
    
    global $wp_widget_factory;

    extract(shortcode_atts(array(
        'api_key'  => '',
        'list_id'   =>  ''
    ), $atts));

    $instance['api_key'] = $api_key;
    $instance['list_id'] = $list_id;
   
    $widget_name = wp_specialchars( 'dc_mailchimp_advanced_signup' );
    
    if (!is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')):
        $wp_class = 'WP_Widget_'.ucwords(strtolower($class));
        
        if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
            return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
        else:
            $class = $wp_class;
        endif;
    endif;
    

    

    ob_start();
    the_widget($widget_name, $instance, array('widget_id'=>'arbitrary-instance-'.$id,
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
    
}