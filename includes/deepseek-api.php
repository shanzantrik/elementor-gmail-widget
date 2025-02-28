<?php
function analyze_emails_with_deepseek($emails)
{
  $api_url = "https://api.deepseek.com/analyze";
  $api_key = "sk-9e79e352e3ea4c4ca55ffb1ba111600e";

  $email_texts = array_map(fn($email) => $email['snippet'], $emails);

  $response = wp_remote_post($api_url, [
    'headers' => [
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type' => 'application/json',
    ],
    'body' => json_encode([
      'text' => implode("\n\n", $email_texts),
      'analysis' => 'summary,sentiment',
    ]),
  ]);

  if (is_wp_error($response)) {
    return ['error' => 'DeepSeek API failed'];
  }

  return json_decode(wp_remote_retrieve_body($response), true);
}
