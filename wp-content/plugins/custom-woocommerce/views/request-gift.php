<?php


function custom_shortcode_request_gift(){
    
  ?>
     <!DOCTYPE html>
     <html>
     <head>
     	<script>
     		  function inputvalidation()
     		  {
     		  	var inputTxt = document.getElementById("text").value; 
                  
                  // Validate  message 
                  if(inputTxt==""){
                  	 document.getElementById("error_message").innerHTML="*plz enter a message !!";
                  	 document.getElementById("text").focus();
                  	 return false;
                  }

                  // Regular expression for basic text validation
                  var Txt_pattern = /^[a-zA-Z\s]+$/;
                  if(!inputTxt.match(Txt_pattern)){
                  	document.getElementById("error_message").innerHTML="*plz Enter Valid message !!";
                  	document.getElementById("text").focus();
                  	return false;
                  } 
     		  }
        </script>
     
     </head>
     <body>
     	  <span id=error_message style="color:red;"></span>
     	    <form action="" method="POST" onsubmit="return inputvalidation()">
     	        <input type="text" id="text" name="text" placeholder="send msg for gift">
     	        <input type="submit" name="submit" value="message for gift">
	        </form>
        </body>
     </html>    
  <?php  
 }
 add_shortcode('Request-gift','custom_shortcode_request_gift');
?>
