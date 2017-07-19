<?php

include_once 'includes/application_top.php';

// Receive OAuth code parameter
$code = isset($_GET['code']) ? $_GET['code'] : "";

// Check whether the user has granted access
if ($code) {
  // Receive OAuth token object
  $data = $instagram->getOAuthToken($code);

  // Take a look at the API response. IF no username, redirect to index.php
  if (empty($data->user->username)) {
    header('Location: index.php');
  } else {
    	$_SESSION['userdetails'] = $data;

      // Sanitise values before adding to database.
    	$username = filter_var(trim($data->user->username), FILTER_SANITIZE_STRING);
    	$fullname = isset($data->user->full_name) ? filter_var(trim($data->user->full_name), FILTER_SANITIZE_STRING) : "";
    	$bio = isset($data->user->bio) ? filter_var(trim($data->user->bio), FILTER_SANITIZE_STRING) : "";
    	$website = isset($data->user->website) ? filter_var(trim($data->user->website), FILTER_SANITIZE_STRING) : "";
    	$id = intval($data->user->id);
    	$token = filter_var(trim($data->access_token), FILTER_SANITIZE_STRING);

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

      //Add a new user if not able to find an existing user in the database
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
