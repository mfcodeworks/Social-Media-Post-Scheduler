<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
require_once 'functions.php';

$fb = new Facebook\Facebook([
  'app_id' => '325572871258177',
  'app_secret' => '5f3caf15f63c8b3a7ba230e64af770d5',
  'default_graph_version' => 'v2.11',
]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['publish_actions','manage_pages','publish_pages']; // Optional permissions
$loginUrl = $helper->getLoginUrl((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]".'/Social-Media-Post-Scheduler/fb-callback.php', $permissions);

header("Location: ".$loginUrl);
?>
