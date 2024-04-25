<?php
/**
 * Sportlink Club.Dataservices API
 *
 * @package   Sportlink Club.Dataservices API
 * @author    Richard van der Meer
 * @link      http://richardvandermeer.nl/
 * @copyright Richard van der Meer
 * @license   GPLv2 or later
 * @version   1.1.1
 */
class GEMStemplate {

  private $template;

  public $merchant_key;
  public $api_endpoint;
  public $event_key;

  public function __construct($merchantKey, $apiEndpoint) {
    $this->merchant_key = $merchantKey;
    $this->api_endpoint = $apiEndpoint;

    if (!!$this->merchant_key) {
        $this->merchant_key = get_option('gems_merchant_key');
    }
    if (!!$this->api_endpoint) {
        $this->api_endpoint = get_option('gems_api_endpoint');
    }

    $this->template =  new GEMS_Template_Loader;
  }

  // Show form
  public function bookingform() {
    // Load the correct template
    $this->template
      ->set_template_data(array('merchant_key' => $this->merchant_key, 'img_endpoint' => $this->img_endpoint, 'api_endpoint' => $this->api_endpoint ))
      ->get_template_part('form', '');
  }

}
?>