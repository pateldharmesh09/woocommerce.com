<?php
/**
* ShopMagic's Survey Popup
*
*
* @package ShopMagic
* @version 1.0.0
* @since   1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Display survey popups with all options
 *
 * @since   1.1.1
 *
 *Write our JS below here
 *
*/
function feedback_javascript() { ?>
  <script type="text/javascript" >
  jQuery(document).ready(function($) {

    jQuery("#feedback_error").hide();
    jQuery("#feed_email").hide();
    jQuery("textarea.smg_description").removeClass("feedback_error");

    jQuery('[data-slug="shopmagic-for-woocommerce"] .deactivate > a').click(function(){

      var data = {
      'action': 'display_popup_survey'
    };
    // ajaxurl is always defined in the admin header and points to admin-ajax.php
    if(jQuery('[data-slug="shopmagic-for-woocommerce"] .deactivate > a').text() == "Deactivate"){
      jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response) {
        jQuery('#wpwrap').append(response);
      });
    }
  });
     ///Submit feedback Form data
  jQuery(document).on('click','#smg_submit',function(e){

    e.preventDefault();
    var description = jQuery("textarea.smg_description").val();
    var option = jQuery("input[name='smg_feedback']:checked").val();
      var postData = {
        Feedback:jQuery("input[name='smg_feedback']:checked").parent('li').text(),
        Description: description,
        Email : jQuery("#feedback_email").val()
      }
      var url = 'https://script.google.com/macros/s/AKfycbwkx3W7HMbDuI_G-kh0_8adlVv_Ezl4enoVvIdeVL_KXY1SciM/exec';
      if(description.length < 1){
        jQuery("#feedback_error").show();
        jQuery("textarea.smg_description").addClass("feedback_error");
        return false;
      }
      if(jQuery("input[name='smg_feedback']:checked").val() != undefined){

          jQuery.ajax({
          url: url,
          method: "GET",
          dataType: "json",
          data: postData,
          success:function(resp){
            if(resp.result=='success'){
              window.location.href=document.querySelector('[data-slug="shopmagic-for-woocommerce"] a').getAttribute('href');
            }
          },
          error:function(resp){
            console.log(resp);
          }
        });
      } 

    });
  });
  </script>
<?php
}
add_action('admin_footer', 'feedback_javascript');
//Dispaly Popup using Ajax
function display_survey_popup(){
  echo '<div id="feedbackModal" class="feedback_modal">

  <!-- Modal content -->
  <div class="feedback_modal-content">
    <div class="feedback_modal-header">
      <span class="feedback_close">&times;</span>
      <h2>Quick Feedback Survey</h2>
    </div>
    <div class="feedback_modal-body">
    <form method="post" action="#" id="smg_form">
      <strong class="feedback_heading">If you have a moment, Please let us know why you are deactivating</strong>
      <ul>
      <li><input type="radio" name="smg_feedback" value="1">I need a specific feature, which you do not support</li>
      <li><input type="radio" name="smg_feedback" value="2">I found a better plugin/solution for marketing automations</li>
      <li><input type="radio" name="smg_feedback" value="3">Could not understand how to get things working</li>
      <li><input type="radio" name="smg_feedback" value="4">The plugin is not working</li>
      <li><input type="radio" name="smg_feedback" value="5">Other</li>
      </ul>
      <label id="feedback_error">Please let us know more information!</label>
      <textarea class="smg_description" style="margin-top: 0px; margin-bottom: 0px; height: 55px;" name="smg_desc"></textarea>
      <br>
      <div id="feed_email">
      <label>Would you like us to reach out to you via email to help with this matter?</label>
      <input type="email" class="form-control" id="feedback_email" placeholder="Enter your Email" name="feedback_email">
      </div>
    </div>
    <div class="feedback_modal-footer">
      <a href="javascript:void(0);" id="smg_feed_cancel" class="smg_button1">Skip & Continue</a>
      <button type="submit" id="smg_submit" class="smg_button">Submit</button>
    </div>
    </form>
  </div>

</div>';

exit();

}
add_action('wp_ajax_display_popup_survey','display_survey_popup');
//Enqueue new scripts and css for survey
function feedback_survey_assets() {
  wp_enqueue_script( 'feedback-script', plugins_url( '/assets/js/feedback-script.js', SHOPMAGIC_BASE_FILE ), array( 'jquery' ), '1.0', true  );
  wp_enqueue_style( 'feedback-modal', plugins_url( '/assets/css/feedback_survey.css', SHOPMAGIC_BASE_FILE ), '1.0', true  );
  wp_localize_script('feedback-script', 'feedback_ajax', array(
   'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('admin_enqueue_scripts', 'feedback_survey_assets');


?>
