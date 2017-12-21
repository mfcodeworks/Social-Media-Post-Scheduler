<?php
    session_start();
    include 'scripts/functions.php';
	loadHead();
    loadNav();
    beginContent();
?>
	<div class='col-lg-3'>
		<div class='list-group' style='padding-top:3.5em;'>
			<div class='list-group-item h3'>Templates</div>
			<a href='javascript:void(0)' class='list-group-item' id='template1' style='color:black;'><i class='fa fa-file-text'></i>&nbsp;Template 1</a>
			<div class='list-group-item h3'>Platform</div>
			<div class='list-group-item'>
				<label for='platformFacebook'>
					<input type='checkbox' value='platformFacebook' id='platformFacebook' value='1'>&nbsp;<i class='fa fa-facebook'></i>&nbsp;Facebook
				</label>
			</div>
			<div class='list-group-item'>
				<label for='platformTwitter'>
					<input type='checkbox' value='platformTwitter' id='platformTwitter' value='1'>&nbsp;<i class='fa fa-twitter'></i>&nbsp;Twitter
				</label>
			</div>
		</div>
	</div>
	<div class='col-lg-9'>
		<div class='box'>
        	<form name="platformPost" id="platformPost" enctype="multipart/form-data" action="javascript:void(0)">
				<div class='row'>
					<div class='col-md-12' style='padding-right:0em;'>
                		<div class='form-group' id='postTextformGroup'>
                			<label for='postText'>Social Media Post Text:</label>
                    		<textarea class='form-control' id='postText' name='postText' rows='10'></textarea>
                		</div>
					</div>
					<div class='col-md-10'>
                		<div class='form-group'>
                			<label for='postPhoto' class='btn btn-secondary'><i class='fa fa-picture-o'></i>&nbsp;Add Photo to Post</label>
                    		<input type='file' class='form-control' id='postPhoto' name='postPhoto' style='display:none;'>
                		</div>
					</div>
					<div class='col-md-2'>
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
