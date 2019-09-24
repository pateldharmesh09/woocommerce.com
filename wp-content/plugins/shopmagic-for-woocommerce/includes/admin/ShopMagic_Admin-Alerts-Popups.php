<?php
/**
 * ShopMagic's Admin Alerts and Popups
 *
 * Prepare admin area classes and variables
 *
 * @package ShopMagic
 * @version 1.0.0
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Display Get Started Admin Notice in Dashboard
 *
 * @since   1.1.1
 */

//Display request to view get started screen
function shopmagic_admin_notice_getstarted() {

    global $current_user;

    $user_id = $current_user->ID;

    if (!get_user_meta($user_id, 'shopmagic_ignore_notice')) {

        echo '<div class="updated notice"><p>'. __('<a href="edit.php?post_type=shopmagic_automation&page=shopmagic_welcome_page">Get Started</a> with ShopMagic <i>by Ristretto Apps</i>') .'<span style="float: right;"><a href="?shopmagic_nag_ignore_getstarted" class="dashicons dashicons-no-alt"></a></span></div>';

    }

}
add_action('admin_notices', 'shopmagic_admin_notice_getstarted');

//If user dismisses Getting Started Admin Notice, record in user settings
function shopmagic_admin_notice_getstarted_ignore() {

    global $current_user;

    $user_id = $current_user->ID;

    if (isset($_GET['shopmagic_nag_ignore_getstarted'])) {

        add_user_meta($user_id, 'shopmagic_ignore_notice', 'true', true);

    }
}
add_action('admin_init', 'shopmagic_admin_notice_getstarted_ignore');


/**
 * Display Pro Discount Offer Banner
 *
 * @since   1.1.1
 */

function shopmagic_admin_notice_pro_discount_offer() {
    global $current_user;
    $user_id = $current_user->ID;
    $current_screen = get_current_screen();

    if( $current_screen->parent_base == "edit" &&  $current_screen->post_type == "shopmagic_automation" && current_user_can( 'manage_options' )) {
        ?>
            <div class="updated notice shopmagic_pro_discount_offer">
                <div class='shopmagic_banner_logo'></div>
                <h2 class='title'>Wanna Do More to Increase Store Sales? Learn about ShopMagic Pro</h2>
                <p>ShopMagic Pro lets you boost your superpowers as an ecommerce seller by encouraging more sales from your best customers</p>
                <p style="font-size:12px;"><a href="http://shopmagic.app/" target="blank">Delayed Emails</a> | <a href="http://shopmagic.app/" target="blank">Review Requests</a> | <a href="http://shopmagic.app/" target="blank">Unique Customer Discounts</a> | <a href="http://shopmagic.app/" target="blank">Add-to Mailing List After Purchase</a> | <i><a href="http://shopmagic.app/" target="blank">And more</i> . . .</p></a>
            </div>
        <?php
    }
}
add_action('admin_notices', 'shopmagic_admin_notice_pro_discount_offer');

/**
 * Display Subscribe to ShopMagic Mailing List Popup in Admin
 *
 * @since   1.1.1
 */
function shopmagic_email_popup_content() {

    $pointer_is_on = true; //Is Pointer still showing up or if it's been dismissed. Used to hide other popups/banners so as not to overwhelm users

    $pointer_content  = '<h3>' . __( 'How To Turn Your One-Time Customers Into Repeat Customers', 'shopmagic' ) . '</h3>';
    $pointer_content .= '<p>' . __( 'Signup for WooCommerce Marketing Automation <b>Tips and Tricks</b></b>', 'shopmagic') . '</p>';
    $pointer_content .= file_get_contents( SHOPMAGIC_PLUGIN_DIR. '/assets/pointer-email-optin.txt');

    ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
            $('#wpadminbar').pointer({
                content: '<?php $pointer_content; ?>',
                position: {
                    edge: 'top',
                    align: 'center'
                },
                close: function() {
                    close_time = <?php echo(time()); ?>;

                    setUserSetting( 'shopmagic_email_subscribe_popup', '0' );
                    setUserSetting( 'shopmagic_email_subscribe_popup_close_time', close_time );
                }
            }).pointer('open');
        });
        //]]>
    </script>
<?php
}

/**
 * Display ShopMagic Popup Survey in Admin
 *
 * Pointer asks user to rate 1-5 stars. If 3 or less, display feedback form. If 4-5, display WP.org Review Link
 *
 * @since   1.1.1
 */
function shopmagic_popup_survey_content() {

    $shopmagic_wp_link='https://wordpress.org/support/plugin/shopmagic-for-woocommerce/reviews/#new-post';

    $pointer_content =  '<div id="shopmagic_survey_intro">'
        .'<h3>' . __( 'What Do You Think So Far?', 'shopmagic' ) . '</h3>'
        .'<p>' . __( 'Help us improve ShopMagic by letting us know what you think of the plugin so far:', 'shopmagic') . '</p>'
        .'<div id="shopmagic_survey_rating">'
        .'<span data-rating="1"></span>'
        .'<span data-rating="2"></span>'
        .'<span data-rating="3"></span>'
        .'<span data-rating="4"></span>'
        .'<span data-rating="5"></span>'
        .'</div>'
        .'</div>';
    //5 stars div
    $pointer_content .= '<div id="shopmagic_survey_4-5stars" style="display:none;">'
        .'<h3>' . __( 'Thank you!', 'shopmagic' ) . '</h3>'
        .'<p>Thank you for your feedback! Would you consider sharing your experience with ShopMagic by leaving a review on the Wordpress Directory?</p>'
        .'<p><a href="'.$shopmagic_wp_link.'" target="_blank">Leave a Review for ShopMagic</a></p>'
        .'<p><i>Good reviews helps encourage others to try ShopMagic and the more people who use it, the more can keep adding features to make it better!</i></p>'
        .'</div>';
    //4 stars or below div
    $pointer_content .= '<div id="shopmagic_survey_feedback" style="display:none;">'
        .'<h3>' . __( 'Thank you for your feedback!', 'shopmagic' ) . '</h3>'
        .'<p>We’re working hard to improve our plugin and make it the best possible experience for you. Would you mind helping us out by letting us know exactly what we could do better?</p>'
        .'<p>'
        .'<textarea name="shopmagic_feedback_text" id="shopmagic_survey_feedback_text" rows="4" placeholder="Your feedback here..." style="width: 100%;"></textarea>'
        .'</p>'
        .'<p><span id="shopmagic_survey_send" class="button-primary">Send feedback</span></p>'
        .'</div>';


    ?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {

            $('#wpadminbar').pointer({
                content: '<?php echo $pointer_content; ?>',
                position: {
                    edge: 'top',
                    align: 'center'
                },
                close: function() {
                    close_time = <?php echo(time()); ?>;

                    setUserSetting( 'shopmagic_survey_popup', '1' );
                    setUserSetting( 'shopmagic_survey_popup_close_time', close_time );
                },
                show:function(){
                    jQuery("#shopmagic_survey_rating [data-rating]").mouseenter(function(){
                        star_rated=jQuery(this).data("rating");

                        for (var i =1 ; i < star_rated+1; i++) {
                            //style
                            jQuery("#shopmagic_survey_rating [data-rating='"+i+"']").css('background-image','url(<?php echo SHOPMAGIC_PLUGIN_URL.'assets/images/yellowstar.png'; ?>)')
                        }
                    });
                    jQuery("#shopmagic_survey_rating [data-rating]").mouseleave(function(){
                        jQuery("#shopmagic_survey_rating [data-rating]").css('background-image','url(<?php echo SHOPMAGIC_PLUGIN_URL.'assets/images/greystar.png'; ?>)')
                    });

                    jQuery("#shopmagic_survey_rating [data-rating]").click(function(){
                        star_rated=jQuery(this).data("rating");
                        setUserSetting( 'shopmagic_survey_popup_rate', star_rated );

                        switch(star_rated){
                            case 5:
                                //5 stars
                                jQuery('#shopmagic_survey_intro').hide();
                                jQuery('#shopmagic_survey_4-5stars').show();
                                break;
                                case 4:
                                //4 stars
                                jQuery('#shopmagic_survey_intro').hide();
                                jQuery('#shopmagic_survey_4-5stars').show();
                                break;
                            default:
                                //4 stars or below
                                jQuery('#shopmagic_survey_intro').hide();
                                jQuery('#shopmagic_survey_feedback').show();

                                /* mail content to send */

                                jQuery('#shopmagic_survey_send').click(function(){
                                    /***update options***/
                                    jQuery.ajax({
                                        type:   'POST',
                                        url:    '<?php echo(bloginfo("url")); ?>/wp-admin/admin-ajax.php',
                                        data:   {
                                            action    : 'shopmagic_send_feedback',
                                            shopmagic_feedback_text : "Rating : "+star_rated+"\n\n"
                                            +"Feedback: \n"+jQuery('#shopmagic_survey_feedback_text').val(),
                                        },
                                        dataType: 'json'
                                    }).done(function( json ) {
                                        if( json.success ) {
                                            alert( "Thank you for your feedback!" );
                                            jQuery('.wp-pointer').hide();

                                        } else if( !json.success ) {
                                            alert( "Please try again !" );
                                        }
                                    }).fail(function() {
                                        //Ajax error
                                        console.log( "The Ajax call itself failed." );
                                    }).always(function() {
                                        //message to show either sent or not
                                    });
                                });
                        }
                    });
                        jQuery(document).on("click", ".close", function(){
                            //clicked
                            setUserSetting( 'shopmagic_survey_popup', '1' );
                        });

                }
            }).pointer('open');

        });
        //]]>
    </script>
<?php

}

/**
 * Trigger ShopMagic Email or Survey Popups in Admin depending on conditions
 *
 * @since   1.1.1
 */
function shopmagic_trigger_admin_popups( $hook_suffix ) {
    // Don't run on WP < 3.3
    if ( get_bloginfo( 'version' ) < '3.3' )
        return;

    //Define Variables
    $current_screen = get_current_screen();
    $screen_id = $current_screen->id;
    $enqueue = FALSE;
    $admin_bar_pointer_on = get_user_setting( 'shopmagic_email_subscribe_popup', 1 ); // check settings on user
    $survey_hidden = get_user_setting( 'shopmagic_survey_popup', 0 );
    $now = time();
    $email_pointer_close_time = get_user_setting( "shopmagic_email_subscribe_popup_close_time" , 0 );
    $one_hour=60*60;

    if(  $current_screen->post_type == "shopmagic_automation" && current_user_can( 'manage_options' )) {
        // check if admin bar is active and default filter for wp pointer is true
        if ( apply_filters( 'show_wp_pointer_admin_bar', TRUE ) ) {
            $enqueue = TRUE;
            if( $admin_bar_pointer_on ) {

                //email subscribe popup
                add_action( 'admin_print_footer_scripts', 'shopmagic_email_popup_content' );

            } else if ( ( $now-$email_pointer_close_time > ( $one_hour*24 )) && !$survey_hidden ) { //if after 1 day

                //popup survey pointer
                add_action( 'admin_print_footer_scripts', 'shopmagic_popup_survey_content' );
            }
        }

        // If true, include the scripts
        if ( $enqueue ) {
            wp_enqueue_style( 'wp-pointer' );
            wp_enqueue_script( 'wp-pointer' );
            wp_enqueue_script( 'utils' ); // for user settings
        }
    }
}
add_action( 'admin_enqueue_scripts', 'shopmagic_trigger_admin_popups' );

/**
 * Email Feedback to ShopMagic team if feedback form filled out on survey popup
 *
 * @since   1.1.1
 */
function shopmagic_send_feedback() {
    $shopmagic_feedback_text = $_POST['shopmagic_feedback_text'];

    wp_mail( "feedback@ristrettoapps.com", "ShopMagic Feedback", $shopmagic_feedback_text);
    die(
    json_encode(
        array(
            'success' => true,
            'message' => 'Feedback sent ...'
        )
    )
    );
}
add_action( 'wp_ajax_shopmagic_send_feedback', 'shopmagic_send_feedback' );
add_action( 'wp_ajax_nopriv_shopmagic_send_feedback', 'shopmagic_send_feedback' );
