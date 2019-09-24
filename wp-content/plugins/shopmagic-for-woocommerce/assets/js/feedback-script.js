window.onload = function(){
    if(document.querySelector('[data-slug="shopmagic-for-woocommerce"] a') !=null){
        document.querySelector('[data-slug="shopmagic-for-woocommerce"] .deactivate > a').addEventListener('click', function(event){

            event.preventDefault();
            event.stopPropagation();

            var urlRedirect = document.querySelector('[data-slug="shopmagic-for-woocommerce"] a').getAttribute('href');
            if(jQuery('[data-slug="shopmagic-for-woocommerce"] .deactivate > a').text() == "Deactivate"){
               jQuery.ajax({
              url: feedback_ajax.ajaxurl,
              data: {
                  action: 'my_ajax_action_function'
              },
              type:"post",
              success:function(response){
                jQuery('#wpwrap').append(response);
              }
          });
             }
               return false;
        })
    }
}
// Close Feedback popup
jQuery(document).on('click','.feedback_close',function(){
    jQuery('#feedbackModal').remove();
});
//Continue deactivating without Feedback
jQuery(document).on('click','#smg_feed_cancel',function(){
    window.location.href=document.querySelector('[data-slug="shopmagic-for-woocommerce"] a').getAttribute('href');
});
//Display texarea when click on any radio button
jQuery(document).on('change','input[name="smg_feedback"]',function(){
    var option = jQuery("input[name='smg_feedback']:checked").val();
      var str = '';
      if(option =='1'){
        str = "Please let us know which features you'd like";
        jQuery("#feed_email").show();
      }else if(option == "2"){
        jQuery("#feed_email").hide();
        jQuery("#feedback_error").hide();
        jQuery("textarea.smg_description").removeClass("feedback_error");
        str = "Please let us know which solution you're going with";
      }else if(option == "3"){
        jQuery("#feed_email").show();
        jQuery("#feedback_error").hide();
        jQuery("textarea.smg_description").removeClass("feedback_error");
        str = "Please let us know what you had trouble with";
      }else if(option == "4"){
        jQuery("#feed_email").show();
        jQuery("#feedback_error").hide();
        jQuery("textarea.smg_description").removeClass("feedback_error");
        str = "Please let us know what you had trouble with";
      }else if(option == "5"){

        jQuery("#feed_email").hide();
        jQuery("#feedback_error").hide();
        jQuery("textarea.smg_description").removeClass("feedback_error");
        str = "Please let us know what you had trouble with";
      }
      jQuery("textarea.smg_description").attr("placeholder",str);
    jQuery('textarea[name="smg_desc"]').show();
});
/// When type message then remove error message 
jQuery(document).on("keypress",".smg_description",function(){
  
  if(jQuery("textarea.smg_description").val().length > 5){
    jQuery("#feedback_error").hide();
    jQuery("textarea.smg_description").removeClass("feedback_error");    
  }

});
