<?php 

/**
 * MineSweeper wrapper for API communication
 *
 * @package MineSweeperSDK
 * @author Alejandro Garcia del Rio <alejandro.garciadelrio@gmail.com>
 */
class MineSweeperSDK 
{

  /**
   * @version 1.0.0
   */
  const VERSION  = "1.0.0";

  /**
   * @var array Default settings
   */
  private $defaultSettings = [
    'url'           => "",
    'version'       => null,
    'language'      => 'en-En',
    'content_type'  => 'json',
  ];

  /**
   * @var boolean Include debug information in response
   */
  private $debug = false;

  /**
   * @var $curl_opts Curl options
   */
  public $curl_opts = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => "MINESWEEPER-PHP-SDK-1.0.0", 
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_CONNECTTIMEOUT => 10, 
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPHEADER => [],
  ];

  /**
   * @var array Permitted http methods
   */
  private $httpMethods = ['GET', 'POST', 'PUT', 'DELETE'];

  /**
   * @var array Predefined actions
   */
  private $predefinedActions = [
    'get'       => 'GET',
    'new'       => 'POST',
    'update'    => 'PUT',
    'destroy'   => 'DELETE',
  ];

  /**
   * @var array Available content types
   */
  private $contentTypes = [
    'form' => 'application/x-www-form-urlencoded',
    'json' => 'application/json',
  ];

  /**
   * @var $namespace Namespace to make the request to
   */
  protected $namespaces = [];

  /**
   * @var $params Stores params for next request
   */
  protected $params = [];

  /**
   * @var $id Stores id for next request
   */
  protected $id = null;

  /**
   * @var $action Action to execute for a resource
   */
  protected $action = null;

  /**
   * @var $methodOverwrite Manually overwrite http method
   */
  protected $methodOverwrite = null;

  /**
   * Constructor method. Set all variables to connect to API
   *
   * @param array $settings Settings to overwrite
   * @return object
   */
  public function __construct($settings = null) 
  {
    // Store settings
    $this->settings($settings);
  }

  /**
   * Overwrite default settings
   *
   * @param array $settings Settings to overwrite
   * @return array Current stored settigns
   */
  public function settings($settings=null) 
  {
    if ($settings) {
      foreach ($this->defaultSettings as $keySetting => $valSetting) {
        if (isset($settings[$keySetting])) {
          $this->defaultSettings[$keySetting] = $settings[$keySetting];
        }
      }
    }

    // Add default language
    array_push($this->curl_opts[CURLOPT_HTTPHEADER], "Accept-Language: ".$this->defaultSettings['language']);

    return $this->defaultSettings;
  }

  /**
   * Activate / deactivate debug response
   *
   * @param boolean $debug
   * @return object $this
   */
  public function debug($debug=true) 
  {
    $this->debug = (bool)$debug;
    return $this;
  }

  /**
   * Set id for next request
   *
   * @param integer $id
   * @return object
   */
  public function id($id) 
  {
    $this->namespaces[] = $id;
    return $this;
  }

  /**
   * Overwrites method
   *
   * @param string $method
   * @return object
   */
  public function method($method) 
  {
    if (in_array(strtoupper($method), $this->httpMethods)) {
      $this->methodOverwrite = $method;
    }
    return $this;
  }

  /**
   * Makes request to server and retrieves response object
   *
   * @param $params
   * @return object
   */
  public function params($params) 
  {
    $this->params = array_merge($this->params, $params);
    return $this;
  }

  /**
   * Capture non existing variables and turn it into namespace
   *
   * @param $name
   * @return object
   */
  public function __get($namespace) 
  {
    $this->namespaces[] = strtolower($namespace);
    return $this;
  }

  /**
   * Build request url with information provided
   *
   * @return string Request url
   */
  private function buildRequestUrl() 
  {

    // Build request url
    $request = '';

    if ($this->namespaces) {
      $request .= '/'.implode('/', $this->namespaces);
    }

    // Add action if set
    if ($this->action) {
      $request .= '/'.$this->action;
    }

    return $request;
  }

  /**
   * Creates error object to return
   *
   * @param $message
   * @param $code HTTP error code
   * @return object
   */
  private function error($message='Invalid request', $code=400) 
  {
    return [
      'success' => false,
      'error' => [
        'status' => $code,
        'type' => 'Bad Request',
        'userMessage' => $message,
      ]
    ];
  }

  /**
   * Makes request to server and retrieves response object
   *
   * @param string $type HTTP verb (GET, POST, PUT, DELETE supported)
   * @param string $url Request url
   * @param array $params Parameters array if needed
   * @return object
   */
  private function execute($type='GET', $url=null, $params=[]) 
  {

    if ($this->methodOverwrite) {
      $type = $this->methodOverwrite;
    }

    $type = strtoupper($type);
    $urlParams = '';

    // Add params
    if ($params) {
      $this->params($params);
    }

    // No raw url, build request
    if (!$url) {
      $url = $this->buildRequestUrl();
    }

    // Add version if defined
    if ($this->defaultSettings['version']) {
      $url = 'v'.$this->defaultSettings['version'].'/'.$url;
    }

    // Build curl opts
    $opts = $this->curl_opts;
    if (strtoupper($type) == 'GET') {
      $urlParams = http_build_query($this->params);
      $url .= '?'.$urlParams;
    }
    else {
      $opts[CURLOPT_POST] = true;
      $postdata = ($this->defaultSettings['content_type'] == 'json')? json_encode($this->params) : http_build_query($this->params);
      $opts[CURLOPT_POSTFIELDS] = $postdata;
    }

    // Add corresponding content type header
    if (!isset($opts[CURLOPT_HTTPHEADER])) {
      $opts[CURLOPT_HTTPHEADER] = [];
    }
    $opts[CURLOPT_HTTPHEADER] = array_merge(
      $opts[CURLOPT_HTTPHEADER], 
      ["Content-Type: ".$this->contentTypes[$this->defaultSettings['content_type']]]
    );

    $opts[CURLOPT_CUSTOMREQUEST] = strtoupper($type);

    // Add API domain
    $url = $this->defaultSettings['url'].$url;

    // Set CURL options and execute curl request
    $ch = curl_init($url);
    if (!empty($opts)) {
      curl_setopt_array($ch, $opts);
    }
    $response = json_decode(curl_exec($ch));
    curl_close($ch);

    if ($this->debug) {
      $response = [
        'params' => $this->params,
        'url' => $url,
        'postdata' => isset($postdata)? $postdata : [],
        'method' => $type,
        'response' => $response,
      ];
    }

    $this->reset();

    return $response;
  }

}