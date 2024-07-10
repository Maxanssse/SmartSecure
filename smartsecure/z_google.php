<?php
require_once 'google/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('169125003134-5pbbcuigks7n7nfu9cmhoalj8ht3rgtj.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/smartsecure/z_google.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // Get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $_SESSION['id'] = $google_account_info->id;
    $_SESSION['email'] = $google_account_info->email;
    $_SESSION['name'] = $google_account_info->name;

    header('Location: z_login-google.php');
    exit();
} else {
    echo "Erreur lors de l'authentification Google.";
}
