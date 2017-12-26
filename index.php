<?php
    session_start();
    include 'scripts/functions.php';
    if(!isset($_SESSION['username'])) headerLocation('login.php');
	loadHead();
    loadNav();
    beginContent();
?>
	<div class='col-lg-3'>
		<div class='list-group' style='padding-top:3.5em;'>
			<div class='list-group-item h3'>Platform</div>
			<div class='list-group-item'>
				<label for='platformFacebook'>
					<input type='checkbox' value='platformFacebook' id='platformFacebook' value='1'>&nbsp;<i class='fa fa-facebook-square'></i>&nbsp;Facebook
				</label>
			</div>
			<div class='list-group-item'>
				<label for='platformTwitter'>
					<input type='checkbox' value='platformTwitter' id='platformTwitter' value='1'>&nbsp;<i class='fa fa-twitter'></i>&nbsp;Twitter
				</label>
			</div>
			<div class='list-group-item'>
				<label for='platformInstagram'>
					<input type='checkbox' value='platformInstagram' id='platformInstagram' value='1'>&nbsp;<i class='fa fa-instagram'></i>&nbsp;Instagram
				</label>
			</div>
			<div class='list-group-item'>
				<label for='platformLinkedin'>
					<input type='checkbox' value='platformLinkedin' id='platformLinkedin' value='1'>&nbsp;<i class='fa fa-linkedin'></i>&nbsp;LinkedIn
				</label>
			</div>
		</div>
        <?php
            if(!isset($_SESSION['fb_access_token']) || $_SESSION['fb_access_token'] == "") {
                echo "<div><p><i class='fa fa-exclamation-circle'></i>&nbsp;Facebook is not logged in. If you wish to post to Facebook click <a href='facebook-login.php'>here.</a></p></div>";
            }
            if(!isset($_SESSION['tw_access_token']) || $_SESSION['tw_access_token'] == "") {
                echo "<div><p><i class='fa fa-exclamation-circle'></i>&nbsp;Twitter is not logged in. If you wish to post to Twitter click <a href='twitter-login.php'>here.</a></p></div>";
            }
            if(!isset($_SESSION['li_access_token']) || $_SESSION['li_access_token'] == "") {
                echo "<div><p><i class='fa fa-exclamation-circle'></i>&nbsp;LinkedIn is not logged in. If you wish to post to LinkedIn click <a href='linkedin-login.php'>here.</a></p></div>";
            }
        ?>
	</div>
	<div class='col-lg-9'>
		<div class='box'>
        	<form name="platformPost" id="platformPost" enctype="multipart/form-data" action="javascript:void(0)">
				<div class='row'>
					<div class='col-md-12' style='padding-right:0em;' id='postRow1'>
                		<div class='form-group' id='postTextformGroup'>
                			<label for='postText'>Social Media Post Text:</label>
                    		<textarea class='form-control' id='postText' name='postText' rows='10'></textarea>
                		</div>
					</div>
					<div class='col-md-9' id='postRow2'>
                		<div class='form-group'>
                			<label for='postDate'><i class='fa fa-calendar'></i>&nbsp;Publish Date (Example: 23/12/2017, 14:30)</label>
                    		<input type='datetime-local' class='form-control' id='postDate' name='postDate'>
                		</div>
					</div>
					<div class='col-md-3' id='postRow3'>
                		<div class='form-group'>
                			<label for='postPhoto' class='btn btn-secondary' style='margin-top:2em;'><i class='fa fa-picture-o'></i>&nbsp;Add Photo to Post</label>
                    		<input type='file' class='form-control' id='postPhoto' name='postPhoto' style='display:none;'>
                		</div>
					</div>
					<div class='col-md-12'>
                		<div class='form-group'>
                    		<button type='submit' class='btn btn-primary'>Submit</button>
						</div>
					</div>
				</div>
        	</form>
		</div>
	</div>
	<script type="text/javascript" src="Mobile-Notification.js"></script>
<?php
	loadFoot();
?>
