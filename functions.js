/* Created by Musa Fletcher         *
 * Last Edit 21/12/17                *
 * Licensed under Apache2 license   *
 *                                  */

 // On document fully loaded, run the following
$(document).ready(function(){

	// On selecting Twitter, check character limit
    $('#platformTwitter').change(function() {
		if(this.checked && $('#postText').val().length > 280)
				hasTwitterWarning($('#postText'));
		else if($('#postText').hasClass('has-warning'))
				noTwitterWarning($('#postText'));
	});

    // On selecting Facebook, get place to send
    $('#platformFacebook').change(function() {
        if(this.checked) {
            $('#postRow3').after("<div class='col-lg-12' id='postRow7'><div class='form-group'><label for='fbPerson' style='margin-top:2em;'><i class='fa fa-user'></i>&nbsp;Facebook ID (For your own profile type 'me', for a Facebook page enter what comes after 'www.facebook.com/' for your Facebook page. e.g. 'mfappsandweb')</label><input type='text' class='form-control' id='fbPerson' name='fbPerson' placeholder='me'></div></div>");
        }
        else {
            $('#postRow7').remove();
        }
    });

    // On selecing Instagram, offer login field for IG
    $('#platformInstagram').change(function() {
        if(this.checked) {
            $('#postRow3').after("<div class='col-lg-12' style='margin-bottom:-25px;' id='igInfoP'><p><i class='fa fa-exclamation-circle'></i>&nbsp;Our Instagram API requires login details to be entered for posting (These will not be saved.)</p></div><div class='col-md-6' id='postRow4'><div class='form-group'><label for='igUser' style='margin-top:2em;'><i class='fa fa-user'></i>&nbsp;Instagram Username: </label><input type='text' class='form-control' id='igUser' name='igUser'></div></div><div class='col-md-6' id='postRow5'><div class='form-group'><label for='igPassword' style='margin-top:2em;'><i class='fa fa-lock'>&nbsp;Instagram Password: </label></i>&nbsp;<input type='password' class='form-control' id='igPassword' name='igPassword'></div></div>")
        }
        else {
            $('#postRow4').remove();
            $('#postRow5').remove();
            $('#igInfoP').remove();
        }
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
        else form.append("platformTwitter","0");
		if($('#platformFacebook').prop('checked')) form.append("platformFacebook","1");
        else form.append("platformFacebook","0");
		if($('#platformInstagram').prop('checked')) form.append("platformInstagram","1");
        else form.append("platformInstagram","0");
		if($('#platformLinkedin').prop('checked')) form.append("platformLinkedin","1");
        else form.append("platformLinkedin","0");
        $.ajax({
            url: "process-post.php",
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

    //Register user
    $('#registerUserForm').submit(function() {
        $.post('check-user.php',
        {
            regUser: $('#regUser').val(),
            regPassword: $('#regPassword').val()
        },
        function(data,status) {
            console.log(data);
            if(data == "true") window.location.replace('index.php');
            else window.location.replace('admin.php?error=username-taken');
        });
    });

    $('#logoutLink').click(function() {
        $.get('logout.php', function(data,status) {
            if(data == "true") location.reload();
        });
    });

    $('#loginForm').submit(function() {
        $.post('check-user.php',
        {
            username: $('#username').val(),
            password: $('#password').val()
        },
        function(data,status) {
            console.log(data);
            if(data == 'true') window.location.replace('index.php');
            else window.location.replace('login.php?error=login-incorrect');
        });
    });

    //Save LinkedIn Business Choice
    $('#linkedinBusinessChoice').click(function() {
        $.post('save-linkedin-choice.php',
        {
            business: $('#LinkedInBusiness').val()
        },
        function(data,status) {
            console.log(data);
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
