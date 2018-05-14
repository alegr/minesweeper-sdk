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