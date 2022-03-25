<?php
require 'vendor/autoload.php'; // change this to your vendor path

$client = new Google_Client();
$client->setAuthConfig('credentials.json'); // change this to the client secret json path
$client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY); // change this to the service needed
$client->setAccessType("offline");
$client->setApprovalPrompt("force");

$authCode = $_GET['code'];

$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

$client->setAccessToken($accessToken);

$token_path = 'token.json'; // change this to the preferred access token path if it doesn't exist yet

$access_token_str = json_encode($client->getAccessToken());
if (!file_exists(dirname($token_path))) {
    mkdir(dirname($token_path), 0700, true);
}
file_put_contents($token_path, $access_token_str);
// change redirect url to your preferred location
header("Location: " .  filter_var('http://localhost/wordpress-test/wp-admin/admin.php?page=google_auth.php'));
