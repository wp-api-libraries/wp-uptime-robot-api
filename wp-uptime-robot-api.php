<?php
/**
 * WP-UptimeRobot-API
 *
 * @package WP-UptimeRobot-API
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * UptimeRobotApi class.
 */
class UptimeRobotApi {

	/**
	 * API key
	 *
	 * @var string
	 */
	static private $api_key;

	/**
	 * Return format. XML or JSON.
	 *
	 * @var [string
	 */
	static private $format;

	/**
	 * Indicate if json respone should be wrapped in a callback.
	 *
	 * @var int
	 */
	static private $no_json_callback;

	/**
	 * URL to the API.
	 *
	 * @var string
	 */
	private $base_uri = 'https://api.uptimerobot.com';

	/**
	 * Constructor.
	 *
	 * @param [String] $api_key  API key to the account.
	 * @param [String] $format   XML or JSON.
	 * @param [Int]    $no_json_callback Return json wrapped in a callback.
	 */
	public function __construct( $api_key, $format = 'json', $no_json_callback = 1 ) {
		static::$api_key = $api_key;
		static::$format  = $format;
		static::$no_json_callback = $no_json_callback;
	}

	/**
	 * Fetch the request from the API.
	 *
	 * @param  [String] $request Request URL.
	 * @return [type]          [description]
	 */
	private function fetch( $request ) {

		if ( preg_match( '#\?#', $request ) ) {
			$request .= '&apiKey=' . static::$api_key  . "&rand=" . mt_rand(1000000, 10000000);
		} else {
			$request .= '?apiKey=' .static::$api_key  . "&rand=" . mt_rand(1000000, 10000000);
		}

		$request .= '&format=' . static::$format;
		$request .= '&noJsonCallback=' . static::$no_json_callback;

		$response = wp_remote_get( $request );
		$code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $code ) {
			return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'text-domain' ), $code ) );
		}

		$body = wp_remote_retrieve_body( $response );

		if ( 'json' === static::$format && static::$no_json_callback  ) {
			return json_decode( $body );
		}

		return $body;
	}

	/**
	 * [get_account_details description]
	 */
	public function get_account_details() {
		$request = $this->base_uri . '/getAccountDetails';
		return $this->fetch( $request );
	}


	/**
	 * Get monitor info.
	 *
	 * @param  [String] $search                [description].
	 * @param  [type]   $monitors              [description].
	 * @param  [type]   $custom_up_ratio       [description].
	 * @param  [Int]    $logs                  [description].
	 * @param  [Int]    $response_times        [description].
	 * @param  [Int]    $response_average [description].
	 * @param  [Int]    $alert_contacts        [description].
	 * @param  [Int]    $show_alert_contacts   [description].
	 * @param  [Int]    $show_timezone         [description].
	 * @return [type]                          [description]
	 */
	public function get_monitors( $search = '', $monitors = null, $custom_up_ratio = null, $logs = 0, $response_times = 0, $response_average = 0, $alert_contacts = 0, $show_alert_contacts = 0, $show_timezone = 0 ) {
		$request = $this->base_uri . '/getMonitors';

		$request .= '?logs=' . $logs . '&responseTimes=' . $response_times . '&responseTimesAverage=' . $response_average . '&alertContacts=' . $alert_contacts . '&showMonitorAlertContacts=' . $show_alert_contacts . '&showTimezone=' . $show_timezone;

		if ( ! empty( $search ) ) {
			$request .= '&search=' . htmlspecialchars( $search );
		}
		if ( ! empty( $monitors ) ) {
				$request .= '&monitors=' . $this->get_implode( $monitors );
		}
		if ( ! empty( $custom_up_ratio ) ) {
				$request .= '&customUptimeRatio=' . $this->get_implode( $custom_up_ratio );
		}
		$result = $this->fetch( $request );

		if( isset( $result->limit ) && isset( $result->offset ) && isset( $result->total )){
			$limit = $result->limit;
			$offset = $result->offset;
			$total = $result->total;

			while ( ($limit * $offset) + $limit < $total ) {
				$result->limit = ($limit * $offset) + $limit;
				$offset++;
				$append = $this->fetch( $request.'&offset='.($offset * $limit) );
				$result->monitors->monitor = array_merge( $result->monitors->monitor, $append->monitors->monitor );
			}
			$result->limit = ( $limit * $offset ) + $limit;
		}

		return $result;
	}

	/**
	 * Create a new Monitor.
	 *
	 * @param  [String] $friendly_name  Required | Display name.
	 * @param  [String] $url            Required | Domain to be monitored.
	 * @param  [String] $type           Required | Monitor type.
	 * @param  [type]   $alert_contacts Optional | .
	 * @param  [type]   $sub_type       Optional | .
	 * @param  [type]   $port           Optional | .
	 * @param  [type]   $keyword_type   Optional | .
	 * @param  [type]   $keyword_value  Optional | .
	 * @param  [type]   $username       Optional | .
	 * @param  [type]   $password       Optional | .
	 * @param  [int]    $interval       Optional | .
	 * @return [type]                  [description] .
	 */
	public function new_monitor( $friendly_name, $url, $type, $alert_contacts = null, $sub_type = null, $port = null, $keyword_type = null, $keyword_value = null, $username = null, $password = null, $interval = 3 ) {

		if ( empty( $friendly_name ) || empty( $url ) || empty( $type ) ) {
			return new WP_Error( 'required-fields', __( 'Required fields are empty', 'text-domain' ) );
		}

		$friendly_name = urlencode( $friendly_name );
		$request = $this->base_uri . '/newMonitor?monitorFriendlyName=' . $friendly_name . '&monitorURL=' . $url . '&monitorType=' . $type;

		if ( ! empty( $alert_contacts ) ) {
			$request .= '&monitorAlertContacts=' . $this->get_implode( $alert_contacts );
		}
		if ( ! empty( $sub_type ) ) {
			$request .= '&monitorSubType=' . $sub_type;
		}
		if ( ! empty( $port ) ) {
			$request .= '&monitorPort=' . $port;
		}
		if ( isset( $keyword_type ) ) {
			$request .= '&monitorKeywordType=' . $keyword_type;
		}
		if ( isset( $keyword_value ) ) {
			$request .= '&monitorKeywordValue=' . urlencode( $keyword_value );
		}
		if ( isset( $username ) ) {
			$request .= '&monitorHTTPUsername=' . urlencode( $username );
		}
		if ( isset( $password ) ) {
			$request .= '&monitorHTTPPassword=' . urlencode( $password );
		}
		if ( ! empty( $interval ) ) {
			$request .= '&monitorInterval=' . $interval;
		}

		return $this->fetch( $request );
	}

	/**
	 * [edit_monitor description]
	 *
	 * @param  [type] $monitor_id     [description].
	 * @param  [type] $monitor_status [description].
	 * @param  [type] $friendly_name  [description].
	 * @param  [type] $url            [description].
	 * @param  [type] $type           [description].
	 * @param  [type] $alert_contacts [description].
	 * @param  [type] $sub_type       [description].
	 * @param  [type] $port           [description].
	 * @param  [type] $keyword_type   [description].
	 * @param  [type] $keyword_value  [description].
	 * @param  [type] $username       [description].
	 * @param  [type] $password       [description].
	 * @return [type]                 [description]
	 */
	public function edit_monitor( $monitor_id, $monitor_status = null, $friendly_name = null, $url = null, $type = null, $alert_contacts = null, $sub_type = null, $port = null, $keyword_type = null, $keyword_value = null, $username = null, $password = null ) {

		$request = $this->base_uri . '/editMonitor?monitorID=' . $monitor_id;

		if (isset($monitor_status)){
	    $request .= '&monitorStatus=' . $monitor_status;
		}
		if (isset($friendly_name)){
		  $request .= '&monitorFriendlyName=' . urlencode($friendly_name);
		}
		if (isset($url)){
		  $request .= '&monitorURL=' . $url;
		}
		if (isset($type)){
		  $request .= '&monitorType=' . $type;
		}
		if (isset($sub_type)){
		  $request .= '&monitorSubType=' . $sub_type;
		}
		if (isset($port)){
		  $request .= '&monitorPort=' . $port;
		}
		if (isset($keyword_type)){
		  $request .= '&monitorKeywordType=' . $keyword_type;
		}
		if (isset($keyword_value)){
		  $request .= '&monitorKeywordValue=' . urlencode($keyword_value);
		}
		if (isset($username)){
		  $request .= '&monitorHTTPUsername=' . urlencode($username);
		}
		if (isset($password)){
		  $request .= '&monitorHTTPPassword=' . urlencode($password);
		}
		if (!empty($alert_contacts)){
		  $request .= '&monitorAlertContacts=' . $this->get_implode($alert_contacts);
		}

		return $this->fetch( $request );
	}

	/**
	 * Delete a monitor.
	 *
	 * @param  [Int] $monitor_id  ID of monitor.
	 * @return [type]             [description]
	 */
	public function delete_monitor( $monitor_id ) {
		if ( empty( $monitor_id ) ) {
			return new WP_Error( 'required-fields', __( 'Required fields are empty', 'text-domain' ) );
		}

		$request = $this->base_uri . '/deleteMonitor?monitorID=' . $monitor_id;

		return $this->fetch( $request );
	}

	/**
	 * [get_alert_contacts description]
	 *
	 * @param  [type] $alert_contacts [description].
	 * @return [type]                 [description]
	 */
	public function get_alert_contacts( $alert_contacts = null ) {
		$request = $this->base_uri . '/getAlertContacts';

		if ( ! empty( $alert_contacts ) ) {
			$request .= '?alertcontacts=' . $this->get_implode( $alert_contacts );
		}

		return $this->fetch( $request );
	}

	/**
	 * [new_alert_contact description]
	 */
	protected function new_alert_contact() {

	}

	/**
	 * [delete_alert_contact description]
	 */
	protected function delete_alert_contact() {

	}

	/**
	 * HTTP response code messages.
	 *
	 * @param  [String] $code : Response code to get message from.
	 * @return [String]       : Message corresponding to response code sent in.
	 */
	public function response_code_msg( $code = '' ) {
		switch ( $code ) {
			case 100:
				$msg = __( 'ApiKey not mentioned or in a wrong format.','text-domain' );
				break;
			case 101:
				$msg = __( 'ApiKey is wrong.','text-domain' );
				break;
			case 102:
				$msg = __( 'Format is wrong (should be xml or json).','text-domain' );
				break;
			case 103:
				$msg = __( 'No such method exists.','text-domain' );
				break;
			case 200:
				$msg = __( 'MonitorID(s) should be integers.','text-domain' );
				break;
			case 201:
				$msg = __( 'MonitorUrl is invalid.','text-domain' );
				break;
			case 202:
				$msg = __( 'MonitorType is invalid.','text-domain' );
				break;
			case 203:
				$msg = __( 'MonitorSubType is invalid.','text-domain' );
				break;
			case 204:
				$msg = __( 'MonitorKeywordType is invalid.','text-domain' );
				break;
			case 205:
				$msg = __( 'MonitorPort is invalid.','text-domain' );
				break;
			case 206:
				$msg = __( 'MonitorFriendlyName is required.','text-domain' );
				break;
			case 207:
				$msg = __( 'The monitor already exists.','text-domain' );
				break;
			case 208:
				$msg = __( 'MonitorSubType is required for this type of monitors.','text-domain' );
				break;
			case 209:
				$msg = __( 'The monitorKeyWordType and monitorKeyWordValue are required for this type of monitor.','text-domain' );
				break;
			case 210:
				$msg = __( 'The monitorID does not exist.','text-domain' );
				break;
			case 211:
				$msg = __( 'The monitorID is required.','text-domain' );
				break;
			case 212:
				$msg = __( 'The account has no monitors.','text-domain' );
				break;
			case 213:
				$msg = __( 'At least one of the parameters to be edited are required.','text-domain' );
				break;
			case 214:
				$msg = __( 'The monitorHTTPUsername and monitorHTTPPassword should both be empty or have values.','text-domain' );
				break;
			case 215:
				$msg = __( 'The monitor specific apiKeys can only use getMonitors method.','text-domain' );
				break;
			case 216:
				$msg = __( 'A user with this e-mail already exists.','text-domain' );
				break;
			case 217:
				$msg = __( 'The userFirstLastName and userEmail are both required.','text-domain' );
				break;
			case 218:
				$msg = __( 'The userEmail is not in the right e-mail format.','text-domain' );
				break;
			case 219:
				$msg = __( 'This account is not authorized to create users.','text-domain' );
				break;
			case 220:
				$msg = __( 'The monitorAlertContacts value is wrong.','text-domain' );
				break;
			case 221:
				$msg = __( 'The account has no alert contacts.','text-domain' );
				break;
			case 222:
				$msg = __( 'The alertcontactID(s) should be integers.','text-domain' );
				break;
			case 223:
				$msg = __( 'The alertContactType and alertContactValue are both required.','text-domain' );
				break;
			case 224:
				$msg = __( 'This alertContactType is not supported".','text-domain' );
				break;
			case 225:
				$msg = __( 'The alert contact already exists.','text-domain' );
				break;
			case 226:
				$msg = __( 'The alert contact is not following @uptimerobot Twitter user. It is required so that the Twitter direct messages (DM) can be sent.','text-domain' );
				break;
			case 227:
				$msg = __( 'The Boxcar user mentioned does not exist.','text-domain' );
				break;
			case 228:
				$msg = __( 'The Boxcar alert contact could not be added, please try again later.','text-domain' );
				break;
			case 229:
				$msg = __( 'The alertContactID does not exist.','text-domain' );
				break;
			case 230:
				$msg = __( 'The alertContactValue should be a valid e-mail for this alertContactType.','text-domain' );
				break;
			default:
				$msg = __( 'Response code unknown.', 'text-domain' );
				break;
		}

		return $msg;
	}

	/**
	 * Implode the array and deliminate the values with '-' or return the variable
	 * as is.
	 *
	 * @param  [Array|String] $var Array to be imploded or a string to be sent back.
	 * @return [String]            Imploded string.
	 */
	private function get_implode( $var ) {
		if ( is_array( $var ) ) {
			return implode( '-', $var );
		}

		return $var;
	}
}
