<?php include_once 'includes/application_top.php';
	$loginUrl = $instagram->getLoginUrl();

	//Fallback images
	$images = array(
		0 => array('url' => 'https://scontent-sin6-1.cdninstagram.com/t51.2885-15/s640x640/sh0.08/e35/16463967_1287222987988045_2957298103868194816_n.jpg', 'description' => 'The view 😍#eveningrun #fitness #fitnessmotivation #ﬁt #health #running #run #lake #nature #travel #travellife'),
		1 => array('url' => 'https://scontent-sin6-1.cdninstagram.com/t51.2885-15/s640x640/sh0.08/e35/15803579_732280540262545_5491124810446536704_n.jpg', 'description' => '#sydney #skyline #travel #operahouse'),
		2 => array('url' => 'https://scontent-sin6-1.cdninstagram.com/t51.2885-15/s640x640/sh0.08/e35/15875958_950897551679114_862054151679377408_n.jpg', 'description' => '#botanicalgardens #travel'),
		3 => array('url' => 'https://scontent-sin6-1.cdninstagram.com/t51.2885-15/s640x640/sh0.08/e35/16110940_646993518842125_6307315574762373120_n.jpg', 'description' => 'Last one 🤞🏼#lake #speerspointpark #fun #travel #pose')
	);

	$instagram_login = false;
	$next_max_id = 0;
	if (!empty($_SESSION['userdetails'])) {
		$instagram_login = true;
		$data = $_SESSION['userdetails'];

		// Store user access token
		$instagram->setAccessToken($data);

		$count = isset($_settings['instagram']['count']) ? $_settings['instagram']['count'] : 6;
		$popular = $instagram->getUserMedia($data->user->id, $count);

		// Check if our api call returned any pictures. Otherwise serves fallback images.
		if (isset($popular->data)) {
			$images = array();
			foreach ($popular->data as $data) {
			  $images[] =  array('url' => $data->images->standard_resolution->url, 'description' => $data->caption->text, 'link' => $data->link);
			}
			$next_max_id = isset($popular->pagination->next_max_id) ? $popular->pagination->next_max_id : 0;
		}
	}
?>
<!DOCTYPE HTML>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<!--Import Google Icon Font-->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!--Import materialize.css-->
	<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
	<link type="text/css" rel="stylesheet" href="css/main.css"  media="screen,projection"/>

	<!--Let browser know website is optimized for mobile-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Midtown Hipster Gallery</title>
</head>

<body>
	<header class="body">
		<nav class="white" role="navigation">
			<div class="nav-wrapper container">
				<a id="logo-container" href="index.php" class="brand-logo left">
					<img src="images/logo.png" height="180px">
				</a>
				<div class="right">
					<?php if (!$instagram_login) { ?>
						<a class="btn-instagram" href="<?php echo $loginUrl; ?>">
				      <b>Sign in</b> with Instagram
				  	</a>
					<?php } else { ?>
									<div id="username">
										<a id="log_out" href="?id=logout">
								      Logout, <?php echo $data->user->username; ?>?
								  	</a>
									</div>
									<img class="responsive-img" id="profile_picture" src="<?php echo $data->user->profile_picture; ?>">
					<?php } ?>
				</div>
				<a href="#" data-activates="nav-mobile" class="button-collapse">
					<i class="material-icons">menu</i>
				</a>
			</div>
		</nav>
    </header>

    <section class="body">
    	<div class="section grey lighten-5">
			<div class="container">
				<div class="row cards-container">
					<?php foreach ($images as $key => $image) { ?>
						<div class="col s6 l4">
							<div class="card hoverable">
								<div class="card-image">
									<img class="materialboxed" data-caption="<?php echo $image['description']; ?>" src="<?php echo $image['url']; ?>">
									<?php if (isset($image['link'])) { ?>
										<a target="_blank" class="open-on-instagram" href="<?php echo $image['link']; ?>"><i class="material-icons">open_in_browser</i></a>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php } ?>
			</div>
			<div class="clearfix"></div>
			<?php if ($next_max_id) { ?>
				<div class="center">
						<a class="waves-effect waves-light btn" id="load_more" data-csrf="<?php echo $_SESSION['csrf_token']?>" data-next-max-id="<?php echo $next_max_id; ?>">Load More</a>
				</div>
			<?php } ?>
		</div>
    	<div class="container" id="contact">
    		<div class="section">
    			<div class="row">
    				<div class="col s12 center">
    					<h4 class="contact-h4">Contact Midtown Hipster Gallery</h4>
				    	<form id="contact_form" method="post" action="php/contact_handler.php">
							<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $_SESSION['csrf_token']?>" />
							<div class="row">
								<div class="input-field col s6">
									<input id="first_name" name="first_name" type="text" class="validate">
									<label for="first_name">First Name*</label>
									<div id="first_name_error" class="error-label">Please tell us your name.</div>
								</div>
								<div class="input-field col s6">
									<input id="last_name" name="last_name" type="text" class="validate">
									<label for="last_name">Last Name</label>
								</div>
							</div>
							<div class="input-field">
								<input id="email" name="email" type="email" class="validate">
								<label for="email">Email*</label>
								<div id="email_error" class="error-label">Please enter a valid email ID, so that we can get in touch with you.</div>
							</div>

							<div class="input-field">
								<textarea id="message" name="message" class="materialize-textarea"></textarea>
								<label for="message">Message*</label>
								<div id="message_error" class="error-label">Please specify your message so that we can serve you better.</div>
							</div>

						    <button class="btn waves-effect waves-light" id="contact_form_submit" type="submit" name="action">Submit
						   		<i class="material-icons right">send</i>
							</button>
						</form>

						<div class="form-message">Thank you for contacting us. Please feel free to check the gallery any time while we try to resolve this at our earliest.</div>
					</div>
				</div>
			</div>
		</div>
    </section>

    <footer class="page-footer teal">
	    <div class="container">
	    </div>
  	</footer>

    <!--Import jQuery before materialize.js-->
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="js/materialize.min.js"></script>
	<script type="text/javascript" src="js/gallery.js"></script>
</body>

</html>
