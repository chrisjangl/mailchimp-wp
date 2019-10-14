<?php 

$form_class = $form_class ? $form_class : 'dc-mailchimp-wp-signup';

?>

<!-- sign-up form container -->
<div class="<?php echo $form_class; ?>" >

    <form class="validate" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form">

        <input name="NAME" type="text" id="dc-advanced-mailchimp-signup-name" placeholder="Name">
        <input name="EMAIL" type="email" id="dc-advanced-mailchimp-signup-email" placeholder="Email">
        <input name="subscribe" type="submit" id="dc-advanced-mailchimp-signup-submit" value="Sign Up!" />
        <div id="dc-advanced-mailchimp-signup-response"></div>
        <div class="loading" id="dc-advanced-mailchimp-signup-loading" style="display: none; text-align: center; margin-top:15px;">
            <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span>
        </div>

    </form>

    <?php
    $script = "
        jQuery('#dc-advanced-mailchimp-signup-submit').click(function(event) {
            event.preventDefault();
            var loadingArea = jQuery('#dc-advanced-mailchimp-signup-loading');
            var responseArea = jQuery('#dc-advanced-mailchimp-signup-response');
            var email = jQuery('#dc-advanced-mailchimp-signup-email').val();
            if ( validateEmail( email ) ) {
                responseArea.empty();
                loadingArea.slideDown();
                var data = {
                    'action': 'dc_outside_class',
                    'email': email
                };

                jQuery.post('" . admin_url("admin-ajax.php") . "', data, function(response) {
                    loadingArea.slideUp();
                    responseArea.html(response).slideDown();

                });
            } else {
                responseArea.empty().html('That doesn\'t look to be a valid email address. Please double check your email address and try again.').slideDown();
            }

        });
        function validateEmail(sEmail) {
            var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            if (filter.test(sEmail)) {
                return true;
            }
            else {
                return false;
            }
        }
        ";
        wp_enqueue_script( 'jquery' );
        wp_add_inline_script( 'jquery', $script );
        ?>

</div>
<!-- end sign-up form container -->