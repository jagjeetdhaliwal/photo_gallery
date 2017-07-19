<?php

include_once '../includes/application_top.php';

$output = array();

// Validate CSRF token
if (!isSet($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])
    || empty($_POST['csrf_token']) || !isSet($_POST['csrf_token'])
    || ($_SESSION['csrf_token'] != $_POST['csrf_token']) ) {
    $output['error'] = "Something went wrong, go back and try again!";
} else if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { //Validate email
    $output['source'] = 'email';
    $output['error'] = "Please enter a valid email ID, so we that can get in touch with you.";
} else if (!isset($_POST['message']) || $_POST['message'] == "") { //Validate message
    $output['source'] = 'message';
    $output['error'] = "Please specify your message so that we can serve you better.";
} else if (!isset($_POST['first_name']) || $_POST['first_name'] == "") { //Validate first name
    $output['source'] = 'first_name';
    $output['error'] = "Please tell us your name.";
} else {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

    $first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    $last_name = isset($_POST['last_name']) ? filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING) : "";
    $name = $last_name == "" ? $first_name : $first_name.' '.$last_name;

    $message = nl2br($message);


    $from = 'From: '.$name;
    $to = $_settings['customer_care'];
    $subject = 'Customer Query '.$from;

    $body = "From: $name </br> E-Mail: $email </br> Message: $message";


    // Send email using mailgun
    if (sendMail($to, $subject, $body, 'support@'.$_settings['mailgun']['domain'])) {
        $output['success'] = true;
        $output['message'] = 'Your message has been sent. We will be in touch soon!';

        $data = array(
            'table' => 'Queries', //for airtable
            'name' => $name,
            'email' => $email,
            'message' => $message
        );

        sendRecordToAirtable($data); // Add Record to Airtable
        logToDatabase($data); // Log to Database
    } else {
        $output['message'] = "Something went wrong, go back and try again!";
    }
}

returnJSON($output);

// Send mail using mailgun
function sendMail($to, $subject, $message, $from) {
    global $_settings;

    // Check if mailgun settings are set.
    if (!isset($_settings['mailgun']['key']) || !$_settings['mailgun']['domain']) {
        return false;
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, 'api:'.$_settings['mailgun']['key']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $plain = strip_tags(nl2br($message));

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/'.$_settings['mailgun']['domain'].'/messages');
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'from' => $from,
        'to' => $to,
        'subject' => $subject,
        'html' => $message,
        'text' => $plain)
    );

    $j = json_decode(curl_exec($ch));

    $info = curl_getinfo($ch);

    if($info['http_code'] != 200) {
        return false;
    }

    curl_close($ch);

    return $j;
}

// Send our record to Airtable using Curl
function sendRecordToAirtable($data) {
    global $_settings;

    if (!isset($_settings['airtable'])) {
        return false;
    }

    $ch = curl_init();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://api.airtable.com/v0/appSWMgHgFb47jkoD/".$data['table']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

    $data_string =  json_encode(array(
            'fields' => ['Name' => $data['name'], 'Email' => $data['email'], 'Notes' => $data['message']]
    ));

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Content-Length: ' . strlen($data_string);
    $headers[] = 'Authorization: Bearer '.$_settings['airtable'];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $j = json_decode(curl_exec($ch));

    $info = curl_getinfo($ch);

    if($info['http_code'] != 200) {
        return false;
    }

    curl_close ($ch);

    return $j;
}

// Insert query into MySQL database
function logToDatabase($data) {
    global $DB;

    $query = "INSERT INTO `customer_queries`(`name`, `email`, `message`) VALUES (?, ?, ?)";

    $stmt = $DB->prepare($query);
    $stmt->bind_param('sss', $data['name'], $data['email'], $data['message']);

    $stmt->execute();

    $affected = false;
    if ($stmt->affected_rows) {
        $affected = true;
    }

    $stmt->close();

    return $affected;
}

function returnJSON($out = array()) {
    header("Content-Type: application/json");
    echo json_encode($out);
    exit(0);
}
