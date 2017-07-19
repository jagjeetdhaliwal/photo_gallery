<?php session_start();
//TO DO - use session_set_save_handler to store session data in database/memcache etc.

require_once 'settings.php';
require_once ROOT.'modules/Instagram.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
}

// Connect to Database
$DB = new mysqli($_settings['mysql']['host'], $_settings['mysql']['user'], $_settings['mysql']['password']);

// Check connection
if ($DB->connect_error) {
    return false;
}

$DB->select_db($_settings['mysql']['database']);

if (isset($_GET['id']) && $_GET['id'] == 'logout') {
  unset($_SESSION['userdetails']);
  session_destroy();
}

$instagram = new Instagram(array(
   'apiKey'      => $_settings['instagram']['key'],
   'apiSecret'   => $_settings['instagram']['secret'],
   'apiCallback' => $_settings['instagram']['callback']
));
