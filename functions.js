/* Created by Musa Fletcher         *
 * Last Edit 21/12/17                *
 * Licensed under Apache2 license   *
 *                                  */

 // On document fully loaded, run the following
$(document).ready(function(){

	/*** FACEBOOK LOGIN API BEGIN ***/
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '325572871258177',
      cookie     : true,
      xfbml      : true,
      version    : '2.11'
    });
    FB.AppEvents.logPageView();
  };
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "https://connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
   /*** FACEBOOK LOGIN API END ***/

	// On selecting Twitter, check character limit
    $('#platformTwitter').change(function() {
		if(this.checked && $('#postText').val().length > 280)
				hasTwitterWarning($('#postText'));
		else if($('#postText').hasClass('has-warning'))
				noTwitterWarning($('#postText'));
	});

	// When post text change, check for any Twitter limit breach
	$('#postText').change(function() {
		if($('#platformTwitter').prop('checked')) {
			if($('#postText').val().length < 281 && $(this).hasClass('has-warning'))
				noTwitterWarning($('#postText'));
			else if($('#postText').val().length > 280 && !$(this).hasClass('has-warning'))
				hasTwitterWarning($('#postText'));
			else if($('#postText').val().length > 280 && $(this).hasClass('has-warning')) {
				noTwitterWarning($('#postText'));
				hasTwitterWarning($('#postText'));
			}
		}
	});

	// Post content with AJAX
	$('#platformPost').submit(function(e) {
        e.preventDefault();
        console.log('Post Sent\n\n');
        var form = new FormData($('#platformPost')[0]);
		if($('#platformTwitter').prop('checked')) form.append("platformTwitter","1");
		if($('#platformFacebook').prop('checked')) form.append("platformFacebook","1");
        $.ajax({
            url: "scripts/process-post.php",
            method: "POST",
            data: form,
            processData: false,
            contentType: false,
            success: function(result){
                console.log(result);
                //location.reload();
            }
        });
	});
});

// Twitter warning functions
function hasTwitterWarning(e) {
	e.addClass('has-warning');
	textLength = e.val().length;
	e.after("<div class='p' id='twitterWarningText'><i class='fa fa-exclamation-circle'></i>&nbsp;Twitters character limit is 280 characters. Your text is currently "+textLength+" characters and cannot be sent to twitter.</div>");
};
function noTwitterWarning(e) {
	e.removeClass('has-warning');
	$('#twitterWarningText').remove();
};
