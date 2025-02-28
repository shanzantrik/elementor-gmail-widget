<?php
class Gmail_AI_Widget extends \Elementor\Widget_Base
{

  public function get_name()
  {
    return 'gmail_ai_widget';
  }
  public function get_title()
  {
    return __('Gmail AI Insights', 'text-domain');
  }
  public function get_icon()
  {
    return 'eicon-envelope';
  }
  public function get_categories()
  {
    return ['basic'];
  }

  protected function register_controls()
  {
    wp_enqueue_script('gmail-widget-script', plugins_url('../assets/script.js', __FILE__), ['jquery'], null, true);
    $this->start_controls_section(
      'content_section',
      [
        'label' => __('Settings', 'text-domain'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'email_filter',
      [
        'label' => __('Filter Emails (e.g., "subject:invoice")', 'text-domain'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'subject:invoice',
      ]
    );

    $this->end_controls_section();
  }

  protected function render()
  {
    $settings = $this->get_settings_for_display();
    $email_filter = $settings['email_filter'];

    echo '<div id="gmail-ai-widget-container">';
    echo '<button id="fetch-emails-btn">Fetch Gmail Insights</button>';
    echo '<div id="gmail-ai-results"></div>';
    echo '</div>';
  }
}
add_action('wp_enqueue_scripts', function () {
  wp_localize_script('gmail-widget-script', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);
});
