<?php
require plugin_dir_path(__FILE__) . '../vendor/autoload.php';

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail as Google_Service_Gmail;

session_start();

function get_gmail_service()
{
  $client = new Google\Client();
  $client->setAuthConfig(__DIR__ . '/../credentials.json');
  $client->addScope(Google_Service_Gmail::GMAIL_READONLY);
  $client->setRedirectUri(admin_url('admin-ajax.php?action=oauth2callback'));

  if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
  } else {
    if (!isset($_GET['code'])) {
      $authUrl = $client->createAuthUrl();
      echo json_encode(['auth_url' => $authUrl]);
      exit;
    } else {
      $accessToken = $client->fetchAccessTokenWithAuthCode($_GET['code']);
      $_SESSION['access_token'] = $accessToken;
    }
  }

  return new Google_Service_Gmail($client);
}

function fetch_gmail_emails($query)
{
  $service = get_gmail_service();
  $user = 'me';
  $results = $service->users_messages->listUsersMessages($user, ['q' => $query]);

  $emails = [];
  foreach ($results->getMessages() as $message) {
    $msg = $service->users_messages->get($user, $message->getId());
    $payload = $msg->getPayload();
    $headers = $payload->getHeaders();

    $email = [
      'subject' => '',
      'snippet' => $msg->getSnippet(),
      'sentiment' => 'Neutral', // Placeholder for sentiment analysis
      'summary' => 'Summary not available' // Placeholder for summary
    ];

    foreach ($headers as $header) {
      if ($header->getName() == 'Subject') {
        $email['subject'] = $header->getValue();
      }
    }

    $emails[] = $email;
  }

  echo json_encode($emails);
  exit;
}

add_action('wp_ajax_fetch_gmail_ai', function () {
  fetch_gmail_emails('subject:invoice');
});

add_action('wp_ajax_nopriv_fetch_gmail_ai', function () {
  fetch_gmail_emails('subject:invoice');
});

add_action('wp_ajax_oauth2callback', function () {
  get_gmail_service();
  wp_redirect(admin_url('admin-ajax.php?action=fetch_gmail_ai'));
  exit;
});
