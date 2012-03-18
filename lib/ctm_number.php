<?php
  /* Call Tracking Metrics 2012, All Rights Reserved */

  class CTMNumber {
    //
    // $ctm_numbers = new CTMNumber($_SERVER, $_COOKIES, "/path/to/your/config/ or null");
    // $ctm_numbers->tracking_number_for_request();
    //
    public function CTMNumber($env, $cookies, $config_path) {
      $this->env = $env;
      $this->cookies = $cookies;

      if (array_key_exists('PATH_INFO', $env)) {
        $path = $env["PATH_INFO"];
      } else if (array_key_exists('REQUEST_URI', $env)) {
        $path = $env["REQUEST_URI"];
      } else {
        $path = "/"; // assume root?
      }
      $location = 'http://' . $env['HTTP_HOST'] . $path . $env['QUERY_STRING'];
      $oloc = $location;
      if (array_key_exists('HTTP_REFERER', $env)) {
        $referrer = $env["HTTP_REFERER"];
      } else {
        $referrer = "";
      }
      $oref = $referrer;

      $this->set_cookie_host($location);

      $this->config = $this->load_config($config_path);

      $this->cookie_path_name = "__ctm_" . $this->config->account_id . '_' . $this->config->cookie_duration;
      $this->cookie_ref_name  = "__ctm2_" . $this->config->account_id . '_' . $this->config->cookie_duration;

      $cookie = array_key_exists($this->cookie_path_name,$cookies) ? $cookies[$this->cookie_path_name] : null;
      $cookie2 = array_key_exists($this->cookie_ref_name,$cookies) ? $cookies[$this->cookie_ref_name] : null;

      if ($cookie != null && $cookie != "null") { $referrer = $cookie; }
      if ($cookie2 != null && $cookie2 != "null") { $location = $cookie2; }

      if ($location == "null") { $location = null; }
      if ($referrer == "null") { $referrer = null; }
     
      // avoid nulls
      if ($referrer == null) { $referrer = $oref; }
      if ($location == null) { $location = $oloc; }

      if ($cookie2 == null && $cookie == null) {
        $this->set_cookie($this->cookie_ref_name, $referrer, $this->config->cookie_duration == 0 ? null : $this->config->cookie_duration);
        $this->set_cookie($this->cookie_path_name, $location, $this->config->cookie_duration == 0 ? null : $this->config->cookie_duration);
      }

      $this->referrer = $referrer;
      $this->location = $location;
    }

    public function tracking_number_for_receiving_number($receiving_number) {
      foreach ($this->config->replacement_rules as $patterns) {
        $target_number = array_shift($patterns);
        $custom_display = array_key_exists($target_number, $this->config->custom_formats) ? $this->config->custom_formats[$target_number] : null;
        $new_number_ext = $this->find_replacement_number(array($patterns));
        if ($new_number_ext) { return $new_number_ext; }
      }
      return $receiving_number;
    }
    private function find_replacement_number($patterns) {
      foreach ($patterns as $pattern) {
        $num = $pattern[2];
        $ext = $pattern[3];

        $ref_pattern = $pattern[0] ? '/' . $pattern[0] . '/i' : '/.*/';
        $loc_pattern = $pattern[1] ? '/' . $pattern[1] . '/i' : '/.*/';
        $not_ref_pattern = $pattern[4] ? '/' . $pattern[4] . '/i' : null;
        $not_loc_pattern = $pattern[5] ? '/' . $pattern[5] . '/i' : null;

        if (!$pattern[0] && !$pattern[1]) {
          if ($this->referrer == '' && !preg_match($not_ref_pattern, $this->referrer) && !preg_match($not_loc_pattern,$this->location)) {
            return array($num, $ext);
          }
        }
        else {
          if (preg_match($ref_pattern, $this->referrer) && preg_match($loc_pattern, $this->location) && 
             (!$not_ref_pattern || !preg_match($not_ref_pattern, $this->referrer)) &&
             (!$not_loc_pattern || !preg_match($not_loc_pattern,$this->location))) {
            return array($num, $ext);
          }
        }
      }
      return null;
    }

    private function load_config($config_path) {
      return json_decode(file_get_contents($config_path));
    }

    private function deflate($value) {
      return "b64" . base64_encode(urlencode($value));
    }
    private function inflate($value) {
      if ($value && preg_match("^b64", $value)) {
        return urldecode(base64_decode(preg_replace("^b64","", $value)));
      }
      return $value;
    }

    private function set_cookie_host($location) {
      $url = parse_url($location);
      $hostparts = explode('.', $url['host']);
      if (count($hostparts) > 2 && count($hostparts) <= 3) {
        array_shift($hostparts);
        $this->hostname = "." . implode('.', $hostparts);
      }
      else {
        $this->hostname = $url['host'];
      }
    }

    private function get_cookie($name) {
      $value = $this->cookies[$name];
      return $this->inflate($value);
    }

    private function set_cookie($name, $value) {
      $cookie_value = $value;
      if ($value && count($value) > 0) {
        $cookie_value = $this->deflate($value);
      }
      $expires = time()+60*60*24*$this->config->cookie_duration;
      setcookie($name, $cookie_value, $expires, '/', $this->hostname);
    }

  }

?>
