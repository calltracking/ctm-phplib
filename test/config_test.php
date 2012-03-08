<?php
  define("CTM_ACCESS_KEY", "a25d752daffdfdd2ef34cc7ced6dd31135e0");
  define("CTM_SECRET_KEY", "0281d0a7fb76f39549af803157f0ab78a2c0");
//  define("CTM_TRACKING_NUMBERS", $_ENV['HOME'] . '.ctm_config.json');
  define("CTM_TEST_MODE", true);

  $_SERVER = array("HTTP_CONTENT_SIGNATURE" => "invalid");

  require dirname(__FILE__) . "/../lib/ctm_config.php"; 

  $json_body = "{\"access_key\":\"a25d752daffdfdd2ef34cc7ced6dd31135e0\"}";

  $config = new CTMConfig($json_body);

  assert(!$config->validate_request());

  $_SERVER = array("HTTP_CONTENT_SIGNATURE" => base64_encode(hash_hmac("sha1", $json_body, CTM_SECRET_KEY, true)));

  $config = new CTMConfig($json_body);

  assert($config->validate_request());

  $config->update();

  $config = $config->read();

  assert($config == $json_body);
?>
