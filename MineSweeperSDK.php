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

}