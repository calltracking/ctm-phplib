<?php
  /* Call Tracking Metrics 2012, All Rights Reserved */

  // TODO: add your keys here.  you can get these in your account settings by clicking enable api access.
  //define("CTM_ACCESS_KEY", "");
  //define("CTM_SECRET_KEY", "");

  // optionally override the location of the tracking number configuration storage.   by default this will be in the same folder as the ctm_config.php file
  //define("CTM_TRACKING_NUMBERS_CONFIG_FILE", $_ENV['HOME'] . '.ctm_config.json');

  class CTMConfig {

    public function CTMConfig($post_body) {
      $this->ctm_signature = $_SERVER['HTTP_CONTENT_SIGNATURE'];
      $this->post_body = $post_body;
      $this->decoded = json_decode($this->post_body);

      if (defined('CTM_TRACKING_NUMBERS_CONFIG_FILE')) {
        $this->tracking_numbers_config_file = CTM_TRACKING_NUMBERS_CONFIG_FILE;
      } else {
        $this->tracking_numbers_config_file = dirname(__FILE__) . '/ctm_config.json';
      }
    }

    public function validate_request() {

      $computed_signature = base64_encode(hash_hmac("sha1", $this->post_body, CTM_SECRET_KEY, true));

      return $computed_signature == $this->ctm_signature && $this->decoded->access_key == CTM_ACCESS_KEY;
    }

    public function update() {
      $fh = fopen($this->tracking_numbers_config_file, 'w') or die("can't open file");
      fwrite($fh, $this->post_body);
      fclose($fh);
    }

    public function read() {
      return file_get_contents($this->tracking_numbers_config_file);
    }
  }

  if (!defined("CTM_TEST_MODE") || !CTM_TEST_MODE) {
    $config = new CTMConfig(file_get_contents('php://input'));
    if ($config->validate_request() ) {
      $config->update();
    }
  }

?>
