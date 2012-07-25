<?php
require_once ('lib/OAuth.php');

// Check for dependencies
if (!function_exists('curl_init'))
  throw new Exception('yelp-api-php needs the CURL PHP extension.');

if (!function_exists('json_decode'))
  throw new Exception('yelp-api-php needs the JSON PHP extension.');

/**
 * Base yelp exception class.
 */
class YelpException extends Exception {}

/**
 * Wrapper for yelp api.
 */
class Yelp
{
  const API_BASE_URL = 'http://api.yelp.com/v2/business/';

  // Yelp credentials
  private $_consumer_key;
  private $_consumer_secret;
  private $_token;
  private $_token_secret;
  private $_signature_method;

  public function __construct($options) {
    if (!isset(
      $options['consumer_key'],
      $options['consumer_secret'],
      $options['token'],
      $options['token_secret']
    ))
      throw new YelpException('Some options are missing.');

    $this->_consumer_key    = $options['consumer_key'];
    $this->_consumer_secret = $options['consumer_secret'];
    $this->_token           = $options['token'];
    $this->_token_secret    = $options['token_secret'];

    // Token object built using the OAuth library
    $this->_token = new OAuthToken($this->_token, $this->_token_secret);

    // Consumer object built using the OAuth library
    $this->_consumer = new OAuthConsumer($this->_consumer_key, $this->_consumer_secret);

    // Yelp uses HMAC SHA1 encoding
    $this->_signature_method = new OAuthSignatureMethod_HMAC_SHA1();
  }

  /**
   * Get data from yelp.
   *
   * @param string $name name of business.
   * @return json yelp response data.
   */
  public function get($name) {
    $unsigned_url = self::API_BASE_URL . $name;
    $signed_url   = $this->_getSignedUrl($unsigned_url);

    // Send Yelp API Call
    $ch = curl_init($signed_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch); // Yelp response
    curl_close($ch);

    return json_decode($data);
  }

  /**
   * Sign the url.
   *
   * @param string $url unsinged url.
   * @return string signed url.
   */
  private function _getSignedUrl($url) {
    $oauthrequest = OAuthRequest::from_consumer_and_token($this->_consumer, $this->_token, 'GET', $url);

    // Sign the request
    $oauthrequest->sign_request($this->_signature_method, $this->_consumer, $this->_token);

    // Get the signed URL
    return $oauthrequest->to_url();
  }
}
