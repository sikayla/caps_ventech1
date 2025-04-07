<?php
require_once 'vendor/autoload.php';

session_start();

$fb = new Facebook\Facebook([
    'app_id' => 'YOUR_FACEBOOK_APP_ID',
    'app_secret' => 'YOUR_FACEBOOK_APP_SECRET',
    'default_graph_version' => 'v17.0',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email'];
$loginUrl = $helper->getLoginUrl('YOUR_REDIRECT_URI', $permissions);

if (isset($_GET['code'])) {
    try {
        $accessToken = $helper->getAccessToken();
        $response = $fb->get('/me?fields=id,name,email,picture', $accessToken);
        $user = $response->getGraphUser();

        $_SESSION['user'] = [
            'name' => $user['name'],
            'email' => $user['email'],
            'profile' => $user['picture']['url']
        ];

        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    header('Location: ' . $loginUrl);
    exit();
}
?>
