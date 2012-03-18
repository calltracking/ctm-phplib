<?php
  assert_options(ASSERT_BAIL, 1);

  define("CTM_ACCESS_KEY", "a25d752daffdfdd2ef34cc7ced6dd31135e0");
  define("CTM_SECRET_KEY", "0281d0a7fb76f39549af803157f0ab78a2c0");
  define("CTM_TEST_MODE", true);
  require dirname(__FILE__) . "/../lib/ctm_number.php"; 

  $env = array('PATH_INFO' => '/test',
               'HTTP_HOST' => 'example.com',
               'QUERY_STRING' => '?foo=bar',
               'HTTP_REFERER' => 'http://www.google.com/url?q=term');
  $cookies = array('c' => 'b');

  $number = new CTMNumber($env, $cookies, dirname(__FILE__) . '/config.json');

  $n = $number->tracking_number_for_receiving_number("5554443333");
  assert($n);
  assert( ($n[0] == "5552223334") );

?>
