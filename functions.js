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
            $('#postRow3').after("<div class='col-lg-12' id='postRow7'><div class='form-group'><label for='fbPerson'><i class='fa fa-user'></i>&nbsp;Facebook ID (For your own profile type 'me', for a Facebook page enter what comes after 'www.facebook.com/' for your Facebook page. e.g. 'mfappsandweb'). Currently Facebook hasn't granted permission for page posting, this will be updated when this ability is available.</label><input type='text' class='form-control' id='fbPerson' name='fbPerson' placeholder='me'></div></div>");
        }
        else {
            $('#postRow7').remove();
        }
    });

    // On selecing Instagram, offer login field for IG
    $('#platformInstagram').change(function() {
        if(this.checked) {
            $('#postRow2').before("<div class='col-lg-12' id='igPostRules'><p><i class='fa fa-exclamation-circle'></i>&nbsp;If photo width or height is above 1080 pixels, photo will be scaled down. Photo ratios must also be between 0.800 and 1.910 according to Instagram posting rules.</p></div>");
            $('#postRow3').after("<div class='col-lg-12' style='margin-bottom:-25px;' id='igInfoP'><p><i class='fa fa-exclamation-circle'></i>&nbsp;Our Instagram API requires login details to be entered for posting (These will not be saved unless post is scheduled for future publishing. If they are saved they will be deleted immidiately upon posting.)</p></div><div class='col-md-6' id='postRow4'><div class='form-group'><label for='igUser' style='margin-top:2em;'><i class='fa fa-user'></i>&nbsp;Instagram Username: </label><input type='text' class='form-control' id='igUser' name='igUser'></div></div><div class='col-md-6' id='postRow5'><div class='form-group'><label for='igPassword' style='margin-top:2em;'><i class='fa fa-lock'>&nbsp;Instagram Password: </label></i>&nbsp;<input type='password' class='form-control' id='igPassword' name='igPassword'></div></div>");
        }
        else {
            $('#postRow4').remove();
            $('#postRow5').remove();
            $('#igInfoP').remove();
            $('#igPostRules').remove();
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

        $('nav').after("<div id='loader' style='width:100%;height:100%;position:fixed;background:rgba(0,0,0,.5) url(ajax-loader.svg) center center no-repeat;z-index:16;'></div>");
        $('.fa-check-circle').remove();
        $('.fa-times-circle').remove();

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
            success: function(result,status) {
                console.log(result);

                //Check results
                if($('#platformFacebook').is(':checked')) {
                    if(result.search('Facebook Successful') > -1)
                        $('label[for="platformFacebook"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'></i>");
                    else if(result.search('Post scheduled') > -1)
                        $('label[for="platformFacebook"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'>&nbsp;Scheduled</i>");
                    else if(result.search('Facebook Failed') > -1)
                        $('label[for="platformFacebook"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                    else
                        $('label[for="platformFacebook"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                }
                if($('#platformTwitter').is(':checked')) {
                    if(result.search('Twitter Successful') > -1)
                        $('label[for="platformTwitter"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'></i>");
                    else if(result.search('Post scheduled') > -1)
                        $('label[for="platformTwitter"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'>&nbsp; Scheduled</i>");
                    else if(result.search('Twitter Failed') > -1)
                        $('label[for="platformTwitter"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                    else
                        $('label[for="platformTwitter"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                }
                if($('#platformInstagram').is(':checked')) {
                    if(result.search('Instagram Successful') > -1)
                        $('label[for="platformInstagram"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'></i>");
                    else if(result.search('Post scheduled') > -1)
                        $('label[for="platformInstagram"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'>&nbsp;Scheduled</i>");
                    else if(result.search('Instagram Failed') > -1)
                        $('label[for="platformInstagram"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                    else
                        $('label[for="platformInstagram"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                }
                if($('#platformLinkedin').is(':checked')) {
                    if(result.search('LinkedIn Successful') > -1)
                        $('label[for="platformLinkedin"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'></i>");
                    else if(result.search('Post scheduled') > -1)
                        $('label[for="platformLinkedin"]').append("&nbsp<i class='fa fa-check-circle' style='color:limegreen;'>&nbsp;Scheduled</i>");
                    else if(result.search('LinkedIn Failed') > -1)
                        $('label[for="platformLinkedin"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                    else
                        $('label[for="platformLinkedin"]').append("&nbsp<i class='fa fa-times-circle' style='color:red;'></i>");
                }

                //Check status
                if(status == "success") {
                    $('#loader').remove();
                }
                else {
                    $('#loader').remove();
                    $('#postRow1').before("<div class='col-md-12'><p><i class='fa fa-exclamation-circle'></i>&nbsp;Couldn't post statuses. Please contact administrator at <a href='mailto:mfappsandweb@gmail.com'>mfappsandweb@gmail.com</a></p></div>");
                }
            }
        });
        $('#postPhoto').val('');
    });

    //Handle AJAX errors
    $('document').ajaxError(function(e,xhr,options) {
        $('#loader').remove();
        $('#nav').after("<div class='col-md-12 text-center'><p><i class='fa fa-exclamation-circle'></i>&nbsp;A problem occured. Please contact administrator at <a href='mailto:mfappsandweb@gmail.com'>mfappsandweb@gmail.com</a> and explain what you did before the error occured.</p></div>");
        err = JSON.stringify(e);
        $.post('error_log.php',
        {
            error: err
        },
        function(data,status) {
            console.log("Error logged.");
        });
    });

    //Register user
    $('#registerUserForm').submit(function() {
        $('.fa-exclamation-circle').remove();
        if($.trim($('#regUser').val()).length < 1) {
            $('label[for="regUser"]').before("<i class='fa fa-exclamation-circle' style='color:red;'></i> ");
        }
        else if($.trim($('#regEmail').val()).length < 5) {
            $('label[for="regEmail"]').before("<i class='fa fa-exclamation-circle' style='color:red;'></i> ");
        }
        else if($.trim($('#regPassword').val()).length < 1) {
            $('label[for="regPassword"]').before("<i class='fa fa-exclamation-circle' style='color:red;'></i> ");
        }
        else {
            $.post('check-user.php',
            {
                regUser: $('#regUser').val(),
                regEmail: $('#regEmail').val(),
                regPassword: $('#regPassword').val()
            },
            function(data,status) {
                console.log(data);
                if(data == "true") window.location.replace('index.php');
                else window.location.replace('admin.php?error=username-taken');
            });
        }
    });

    //Logout user
    $('#logoutLink').click(function() {
        $.get('logout.php', function(data,status) {
            if(data == "true") location.reload();
        });
    });

    //Login user
    $('#loginForm').submit(function() {
        $('.fa-exclamation-circle').remove();
        if($.trim($('#username').val()).length < 1) {
            $('label[for="username"]').before("<i class='fa fa-exclamation-circle' style='color:red;'></i> ");
        }
        else if($.trim($('#password').val()).length < 1) {
            $('label[for="password"]').before("<i class='fa fa-exclamation-circle' style='color:red;'></i> ");
        }
        else {
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
        }
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
