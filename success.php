<?php

include_once 'includes/application_top.php';

// Receive OAuth code parameter
$code = isset($_GET['code']) ? $_GET['code'] : "";

// Check whether the user has granted access
if ($code) {
  // Receive OAuth token object
  $data = $instagram->getOAuthToken($code);

  // Take a look at the API response
  if (empty($data->user->username)) {
    header('Location: index.php');
  } else {
    	$_SESSION['userdetails'] = $data;
    	$username = $data->user->username;
    	$fullname = isset($data->user->full_name) ? $data->user->full_name : "";
    	$bio = isset($data->user->bio) ? $data->user->bio : "";
    	$website = isset($data->user->website) ? $data->user->website : "";
    	$id = intval($data->user->id);
    	$token = $data->access_token;

      $instagram_id = 0;
      $query = "SELECT `instagram_id`
          FROM `users`
          WHERE `instagram_id` = ?";
      $stmt = $DB->prepare($query);
      $stmt->bind_param('i', $id);
      $stmt->bind_result($instagram_id);
      $stmt->execute();
      $stmt->store_result();
      $stmt->fetch();
      $stmt->close();

      //Not able to find an existing user
      if (!$instagram_id) {
          $query = "INSERT INTO `users`(`username`, `name`, `bio`, `website`, `instagram_id`, `instagram_access_token`)
            VALUES (?, ?, ?, ?, ?, ?)";
          $stmt = $DB->prepare($query);
          $stmt->bind_param(
            'ssssis',
            $username,
            $fullname,
            $bio,
            $website,
            $id,
            $token
          );
          $stmt->execute();
          $stmt->close();
      }

      header('Location: index.php');
  }
} else {
  // Check whether an error occurred
  if (isset($_GET['error'])) {
      echo 'An error occurred: '.$_GET['error_description'];
  } else {
     header('Location: index.php');
  }
}
