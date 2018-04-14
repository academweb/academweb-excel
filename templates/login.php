<?php
/*
Template Name: LogIn
*/
	if( is_user_logged_in() ) wp_redirect('/');
wp_head();
?>
<div class="container">
	  <div class="jumbotron">
  
    <h3>Please log in!</h3> 
  </div>

	<div class="col-lg-4"></div>
 <div class="col-lg-4">
		<form name="loginform" id="loginform" action="/wp-login.php" method="post">
				

			<div class="form-group">
				<label for="user_login">Username or Email Address</label>
				<input type="text" name="log" id="user_login" class="form-control input" value="" size="20">
			</div>
	  		<div class="form-group">
				<p class="login-password">
					<label for="user_pass">Password</label>
					<input type="password" name="pwd" id="user_pass" class="form-control input" value="" size="20">
				</p>
			</div>
			<div class="form-group">
				<p class="login-remember"><label><input name="rememberme" class="form-control" type="checkbox" id="rememberme" value="forever"> Remember Me</label></p>
			</div>
			
			<div class="form-group">
				<p class="login-submit">
					<input type="submit" name="wp-submit" id="wp-submit" class="form-control button button-primary" value="Log In">
					<input type="hidden" name="redirect_to" value="/login/">
				</p>
			</div>
				
			</form>
	</div>
	<div class="col-lg-3"></div>
</div>
<?php wp_footer();?>
