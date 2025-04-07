<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('YOUR_REDIRECT_URI');
$client->addScope('email');
$client->addScope('profile');

if (isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header("Location: " . $auth_url); // Corrected header syntax
    exit();
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $_SESSION['user'] = [
        'name' => $google_account_info->name,
        'email' => $google_account_info->email,
        'profile' => $google_account_info->picture,
    ];

    header("Location: dashboard.php"); // Corrected header syntax
    exit();
}
?>