<?php

define('HOST', 'http://www.xxxxxx.com/', false);
define('ROOT', 'xxx/xxx/xxx/public_html/', false);

$_settings = array();

// Mailgun settings
$_settings['mailgun']['key'] = 'xxxxxx';
$_settings['mailgun']['domain'] = 'xxxxxx';

// Airtable settings
$_settings['airtable'] = 'xxxxxx';

// MySQL settings
$_settings['mysql']['host'] = 'xxxxxx';
$_settings['mysql']['user'] = 'xxxxxx';
$_settings['mysql']['password'] = 'xxxxxxx';
$_settings['mysql']['database'] = 'xxxxxx';

// Customer care email configuration
$_settings['customer_care'] = 'xx@xx.com';

// Instagram client settings.
$_settings['instagram']['key'] = 'xxxxxx';
$_settings['instagram']['secret'] = 'xxxxxx';
$_settings['instagram']['callback'] = HOST.'success.php';
