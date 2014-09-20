<?php
require_once dirname(__FILE__) . '/event_functions.php';
require_once dirname(__FILE__) . '/db_connect.php';
require_once dirname(__FILE__) . '/JSONResponseHandler.php';
require_once dirname(__FILE__) . '/user_functions.php';

$user = new User();
$user->get('1234');
