<?php

include_once '../includes/application_top.php';

$next_max_id = isset($_POST['next_max_id']) ? trim($_POST['next_max_id']) : 0;
$output = array();

// Validate csrf token
if (!isSet($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])
    || empty($_POST['csrf_token']) || !isSet($_POST['csrf_token'])
    || ($_SESSION['csrf_token'] != $_POST['csrf_token']) ) {
    $output['error'] = "Something went wrong, go back and try again!";
} else if ($next_max_id > 0 && !empty($_SESSION['userdetails'])) {
  $data = $_SESSION['userdetails'];

  $instagram->setAccessToken($data);

  // Get next n pictures using next max id
  $count = isset($_settings['instagram']['count']) ? $_settings['instagram']['count'] : 6;
  $popular = $instagram->getUserMedia($data->user->id, $count, $next_max_id);

  // Check if we get any more pictures.
  if (isset($popular->data)) {
    $images = array();
    foreach ($popular->data as $data) {
      $images[] =  array('url' => $data->images->standard_resolution->url, 'description' => $data->caption->text, 'link' => $data->link);
    }

    $new_next_max_id = isset($popular->pagination->next_max_id) ? $popular->pagination->next_max_id : 0;

    $output['images'] = $images;
    $output['next_max_id'] = $new_next_max_id;
    $output['success'] = true;
  }

}


returnJSON($output);

function returnJSON($out = array()) {
    header("Content-Type: application/json");
    echo json_encode($out);
    exit(0);
}
