<?php
/**
* EmailManagerBehavior - 														
* Connects to the EmailManager api http://api.emailmanager.com/1.0/?            
*																				
*/												
class EmailManagerBehavior extends ModelBehavior
{

/**
 * Base API url
 *
 * @var string
 */
	private $baseUrl = 'http://api.emailmanager.com/1.0/?';

/**
 * Setup the behavior and obtain the API KEY
 *
 * @param object $Model Model using the behavior
 * @param array $settings Settings with basic configuration.
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
	    if (!isset($this->settings[$Model->alias])) {
	        $this->settings[$Model->alias] = Configure::read('EmailManager.config');
	    }
	    $this->settings[$Model->alias] = array_merge(
        $this->settings[$Model->alias], (array)$settings);

        $this->settings[$Model->alias]['apikey'] = $this->getApiKey($Model);
	}

/**
 * Main function to interact with EmailManager API
 *
 * @param object $Model Model using the behavior
 * @param string $method EmailManager method to be used
 * @param array $params EmailManager method parameters
 * @return array $response EmailManager response
 */
	public function callMethod(Model $Model, $method = '', $params = array()) {
		if(empty($method)) {
			throw new Exception('Unexistent method');
		}
		$default = array(
			'apikey' => $this->settings[$Model->alias]['apikey'],
			'method' => $method,			
		);
		$params = array_merge($params, $default);

		$response = $this->api_connect($Model, $params);
		$response = json_decode($response, true);

		return $response;
	}

/**
 * Fetches the EmailManager Api Key using the usermail and password
 *
 * @param object $Model Model using the behavior
 * @throws Exception
 * @return string $result[0]['apikey'] Returned api key
 */
	private function getApiKey(Model $Model) {
		$params = array(
			'username' => $this->settings[$Model->alias]['username'],
			'password' => $this->settings[$Model->alias]['password'],
			'method' => 'authentLogin'
		);
		$result = $this->api_connect($Model, $params);
		$result = json_decode($result, true);

		if(array_key_exists('apikey', $result[0])) {
			return $result[0]['apikey'];
		}else {
			throw new Exception($result[0]['message']);
		}
	}

/**
 * Main function to connect to the EmailManager api
 *
 * @param object $Model Model using the behavior
 * @param array $params Parameters to be used in the query
 * @return jsonString $curlResult 
 */
    private function api_connect(Model $Model, $params = array()) {
        $url = $this->getUrl($this->settings[$Model->alias], $params);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $curlResult = curl_exec($ch);

        return $curlResult;
    }

/**
 * Creates the URL based on the passed parameters and the basic configuration
 *
 * @param array $settings Basic configuration settings
 * @param array $params Parameters to be used in the query
 * @return string $url Url correctly formated based on params and settings
 */
    private function getUrl($settings, $params = array()) {
    	$url = $this->baseUrl;
    	$url .= 'domain=' . $settings['domain'];
    	$url .= '&output=' . $settings['output'];

    	if(!empty($params)) {
			foreach($params as $key => $param) {
	    		$url .= '&' . $key . '=' . rawurlencode($param);
	    	}
		}    	

    	return $url;
    }
}
