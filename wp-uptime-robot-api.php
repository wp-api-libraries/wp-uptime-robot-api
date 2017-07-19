<?php
/**
 * WP-UptimeRobot-API
 *
 * @author Santiago Garza <github.com/sfgarza>
 * @link https://uptimerobot.com/api APIv2 Documentation.
 * @package WP-API-Libraries\WP-UptimeRobot-API
 */

/*
* Plugin Name: WP Uptime Robot API
* Plugin URI: https://github.com/wp-api-libraries/wp-uptime-robot-api
* Description: Perform API requests to Uptime Robot in WordPress.
* Author: WP API Libraries
* Version: 1.0.0
* Text Domain: wp-uptime-robot-api
* Author URI: https://wp-api-libraries.com
* GitHub Plugin URI: https://github.com/wp-api-libraries/wp-uptime-robot-api
* GitHub Branch: master
*/

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'UptimeRobotApi' ) ) {
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
		 * @var string
		 */
		static private $format;

		/**
		 * Indicate if json respone should be wrapped in a callback.
		 *
		 * @var int
		 */
		static private $callback;

		/**
		 * URL to the API.
		 *
		 * @var string
		 */
		private $base_uri = 'https://api.uptimerobot.com/v2';

		/**
		 * Request POST args.
		 *
		 * @var array
		 */
		private $args = array();

		/**
		 * Constructor.
		 *
		 * @param string $api_key  API key to the account.
		 * @param string $format   XML or JSON.
		 * @param int    $callback If specified, returns json wrapped in a callback with the name passed in.
		 */
		public function __construct( $api_key, $format = 'json', $callback = null ) {
			static::$api_key = $api_key;
			static::$format  = $format;
			static::$callback = $callback;
		}

		/**
		 * Fetch the request from the API.
		 *
		 * @param  string $request Request URL.
		 * @return mixed           Request results.
		 */
		private function fetch( $request ) {

			$this->args['body']['api_key'] = static::$api_key;
			$this->args['body']['format'] = static::$format;

			if ( null !== static::$callback ) {
				$this->args['body']['callback'] = static::$callback;
			}

			$response = wp_remote_post( $request, $this->args );
			$code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $code ) {
				return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'wp-uptime-robot-api' ), $code ) );
			}

			$body = wp_remote_retrieve_body( $response );

			if ( 'json' === static::$format && null === static::$callback  ) {
				return json_decode( $body );
			}

			return $body;
		}

		/**
		 * Account details (max number of monitors that can be added and number of up/down/paused monitors) can be grabbed
		 * using this method.
		 *
		 * @api
		 *
		 * @return array Request results.
		 */
		public function get_account_details() {
			$request = $this->base_uri . '/getAccountDetails';
			return $this->fetch( $request );
		}


		/**
		 * Get monitor info.
		 *
		 * @api
		 *
		 * @param  array $args Array of arguments to send into get_monitors.
		 * @return array       Array of monitor info.
		 */
		public function get_monitors( $args = array() ) {
			// Set route.
			$request = $this->base_uri . '/getMonitors';

			// Parse and set query args.
			if ( isset( $args['monitors'] ) ) {
				$this->args['body']['monitors'] = $this->get_implode( $args['monitors'] );
			}
			if ( isset( $args['types'] ) ) {
				$this->args['body']['types'] = $this->get_implode( $args['types'] );
			}
			if ( isset( $args['statuses'] ) ) {
				$this->args['body']['statuses'] = $this->get_implode( $args['statuses'] );
			}
			if ( isset( $args['custom_uptime_ratios'] ) ) {
				$this->args['body']['custom_uptime_ratios'] = $this->get_implode( $args['custom_uptime_ratios'] );
			}
			if ( isset( $args['custom_uptime_ranges'] ) ) {
				$this->args['body']['custom_uptime_ranges'] = $this->get_implode( $args['custom_uptime_ranges'] );
			}
			if ( isset( $args['all_time_uptime_ratio'] ) ) {
				$this->args['body']['all_time_uptime_ratio'] = $args['all_time_uptime_ratio'];
			}
			if ( isset( $args['all_time_uptime_durations'] ) ) {
				$this->args['body']['all_time_uptime_durations'] = $args['all_time_uptime_durations'];
			}
			if ( isset( $args['logs'] ) ) {
				$this->args['body']['logs'] = $args['logs'];
			}
			if ( isset( $args['logs_start_date'] ) ) {
				$this->args['body']['logs_start_date'] = $args['logs_start_date'];
			}
			if ( isset( $args['logs_end_date'] ) ) {
				$this->args['body']['logs_end_date'] = $args['logs_end_date'];
			}
			if ( isset( $args['logs_limit'] ) ) {
				$this->args['body']['logs_limit'] = $args['logs_limit'];
			}
			if ( isset( $args['response_times'] ) ) {
				$this->args['body']['response_times'] = $args['response_times'];
			}
			if ( isset( $args['response_times_limit'] ) ) {
				$this->args['body']['response_times_limit'] = $args['response_times_limit'];
			}
			if ( isset( $args['response_times_average'] ) ) {
				$this->args['body']['response_times_average'] = $args['response_times_average'];
			}
			if ( isset( $args['response_times_start_date'] ) ) {
				$this->args['body']['response_times_start_date'] = $args['response_times_start_date'];
			}
			if ( isset( $args['response_times_end_date'] ) ) {
				$this->args['body']['response_times_end_date'] = $args['response_times_end_date'];
			}
			if ( isset( $args['alert_contacts'] ) ) {
				$this->args['body']['alert_contacts'] = $args['alert_contacts'];
			}
			if ( isset( $args['mwindows'] ) ) {
				$this->args['body']['mwindows'] = $args['mwindows'];
			}
			if ( isset( $args['timezone'] ) ) {
				$this->args['body']['timezone'] = $args['timezone'];
			}
			if ( isset( $args['offset'] ) ) {
				$this->args['body']['offset'] = $args['offset'];
			}
			if ( isset( $args['limit'] ) ) {
				$this->args['body']['limit'] = $args['limit'];
			}
			if ( isset( $args['search'] ) ) {
				$this->args['body']['search'] = htmlspecialchars( $args['search'] );
			}

			// Make API Call.
			$result = $this->fetch( $request );

			// Loop until all monitors are retrieved.
			if ( isset( $result->pagination->limit ) && isset( $result->pagination->offset ) && isset( $result->pagination->total ) ) {
				$limit = $result->pagination->limit;
				$offset = $result->pagination->offset;
				$total = $result->pagination->total;

				while ( ($limit * $offset) + $limit < $total ) {
					$result->pagination->limit = ($limit * $offset) + $limit;
					$offset++;
					$this->args['body']['offset'] = ($offset * $limit);
					$append = $this->fetch( $request );

					if ( 'fail' === $append->stat ) { break; }

					$result->monitors = array_merge( $result->monitors, $append->monitors );
				}
				$result->pagination->limit = ( $limit * $offset ) + $limit;
			}

			// Return to sender.
			return $result;
		}

		/**
		 * New monitors of any type can be created using this method.
		 *
		 * @api
		 *
		 * @param  array $args Args to be sent into newMonitor request.
		 * @return array       Request results.
		 */
		public function new_monitor( $args ) {

			if ( ! isset( $args['friendly_name'] ) || ! isset( $args['url'] ) || ! isset( $args['type'] ) ) {
				return new WP_Error( 'required-fields', __( 'Required fields are empty', 'wp-uptime-robot-api' ) );
			}

			$request = $this->base_uri . '/newMonitor';
			$this->args['body']['friendly_name'] = $args['friendly_name'];
			$this->args['body']['url'] = $args['url'];
			$this->args['body']['type'] = $args['type'];

			if ( isset( $args['type'] ) ) {
				$this->args['body']['type'] = $args['type'];
			}
			if ( isset( $args['sub_type'] ) ) {
				$this->args['body']['sub_type'] = $args['sub_type'];
			}
			if ( isset( $args['port'] ) ) {
				$this->args['body']['port'] = $args['port'];
			}
			if ( isset( $args['keyword_type'] ) ) {
				$this->args['body']['keyword_type'] = $args['keyword_type'];
			}
			if ( isset( $args['keyword_value'] ) ) {
				$this->args['body']['keyword_value'] = $args['keyword_value'];
			}
			if ( isset( $args['interval'] ) ) {
				$this->args['body']['interval'] = $args['interval'];
			}
			if ( isset( $args['http_username'] ) ) {
				$this->args['body']['http_username'] = $args['http_username'];
			}
			if ( isset( $args['http_password'] ) ) {
				$this->args['body']['http_password'] = $args['http_password'];
			}
			if ( isset( $args['alert_contacts'] ) ) {
				$this->args['body']['alert_contacts'] = $this->get_implode( $args['alert_contacts'] );
			}
			if ( isset( $args['mwindows'] ) ) {
				$this->args['body']['mwindows'] = $this->get_implode( $args['mwindows'] );
			}

			return $this->fetch( $request );
		}

		/**
		 * Monitors can be edited using this method.
		 *
		 * Important:
		 * The type of a monitor can not be edited (like changing a HTTP monitor into a Port monitor). For such
		 * cases, deleting the monitor and re-creating a new one is adviced.
		 *
		 * @api
		 *
		 * @param  array $args Array of arguments to send into get_monitors.
		 * @return array       Array of monitor info.
		 */
		public function edit_monitor( $args ) {

			if ( ! isset( $args['id'] ) ) {
				return new WP_Error( 'required-fields', __( 'Monitor id required', 'wp-uptime-robot-api' ) );
			}

			$request = $this->base_uri . '/editMonitor';
			$this->args['body']['id'] = $args['id'];

			if ( isset( $args['friendly_name'] ) ) {
				$this->args['body']['friendly_name'] = $args['friendly_name'];
			}
			if ( isset( $args['url'] ) ) {
				$this->args['body']['url'] = $args['url'];
			}
			if ( isset( $args['sub_type'] ) ) {
				$this->args['body']['sub_type'] = $args['sub_type'];
			}
			if ( isset( $args['port'] ) ) {
				$this->args['body']['port'] = $args['port'];
			}
			if ( isset( $args['keyword_type'] ) ) {
				$this->args['body']['keyword_type'] = $args['keyword_type'];
			}
			if ( isset( $args['keyword_value'] ) ) {
				$this->args['body']['keyword_value'] = $args['keyword_value'];
			}
			if ( isset( $args['interval'] ) ) {
				$this->args['body']['interval'] = $args['interval'];
			}
			if ( isset( $args['status'] ) ) {
				$this->args['body']['status'] = $args['status'];
			}
			if ( isset( $args['http_username'] ) ) {
				$this->args['body']['http_username'] = $args['http_username'];
			}
			if ( isset( $args['http_password'] ) ) {
				$this->args['body']['http_password'] = $args['http_password'];
			}
			if ( isset( $args['alert_contacts'] ) ) {
				$this->args['body']['alert_contacts'] = $this->get_implode( $args['alert_contacts'] );
			}
			if ( isset( $args['mwindows'] ) ) {
				$this->args['body']['mwindows'] = $this->get_implode( $args['mwindows'] );
			}

			return $this->fetch( $request );
		}

		/**
		 * Monitors can be deleted using this method.
		 *
		 * @api
		 *
		 * @param  int $monitor_id  ID of monitor.
		 * @return array              Request results.
		 */
		public function delete_monitor( $monitor_id ) {
			if ( empty( $monitor_id ) ) {
				return new WP_Error( 'required-fields', __( 'Required fields are empty', 'wp-uptime-robot-api' ) );
			}

			$this->args['body']['id'] = $monitor_id;
			$request = $this->base_uri . '/deleteMonitor';

			return $this->fetch( $request );
		}

		/**
		 * The list of alert contacts can be called with this method.
		 *
		 * @api
		 *
		 * @param  array $args Arguments for getAlertContacts request.
		 * @return array       Request results.
		 */
		public function get_alert_contacts( $args = array() ) {
			$request = $this->base_uri . '/getAlertContacts';

			if ( isset( $args['alert_contacts'] ) ) {
				$this->args['body']['alert_contacts'] = $this->get_implode( $args['alert_contacts'] );
			}
			if ( isset( $args['offset'] ) ) {
				$this->args['body']['offset'] = $args['offset'];
			}
			if ( isset( $args['limit'] ) ) {
				$this->args['body']['limit'] = $args['limit'];
			}

			return $this->fetch( $request );
		}


		/**
		 * Converts the status ID to the corresponding status string.
		 *
		 * @param  int    $status Status ID to convert.
		 * @return string         Status in string format ( Up|Down|Seems Down|Paused|Not Checked Yet|Not a Valid Status )
		 */
		public function status_tostring( Int $status ){
			switch ( $status ) {
				case 0:
					$str_status = __( 'Paused','wp-uptime-robot-api' );
					break;
				case 1:
					$str_status = __( 'Not Checked Yet','wp-uptime-robot-api' );
					break;
				case 2:
					$str_status = __( 'Up','wp-uptime-robot-api' );
					break;
				case 8:
					$str_status = __( 'Seems Down','wp-uptime-robot-api' );
					break;
				case 9:
					$str_status = __( 'Down','wp-uptime-robot-api' );
					break;
				default:
					$str_status = __( 'Not a Valid Status', 'wp-uptime-robot-api' );
					break;
			}

			return $str_status;
		}

		/**
		 * Converts the alert type ID to its corresponding messages.
		 *
		 * @param  Int    $type Alert type ID.
		 * @return String       Alert type String (SMS|E-mail|Twitter DM|Boxcar|Web-Hook|Pushbullet|Zapier|Pushover|HipChat|Slack).
		 */
		public function alert_type_tostring( Int $type ){
			switch ( $status ) {
				case 1:
					$str_type = __( 'SMS','wp-uptime-robot-api' );
					break;
				case 2:
					$str_type = __( 'E-mail','wp-uptime-robot-api' );
					break;
				case 3:
					$str_type = __( 'Twitter DM','wp-uptime-robot-api' );
					break;
				case 4:
					$str_type = __( 'Boxcar','wp-uptime-robot-api' );
					break;
				case 5:
					$str_type = __( 'Web-Hook','wp-uptime-robot-api' );
					break;
				case 6:
					$str_type = __( 'Pushbullet','wp-uptime-robot-api' );
					break;
				case 7:
					$str_type = __( 'Zapier','wp-uptime-robot-api' );
					break;
				case 9:
					$str_type = __( 'Pushover','wp-uptime-robot-api' );
					break;
				case 10:
					$str_type = __( 'HipChat','wp-uptime-robot-api' );
					break;
				case 11:
					$str_type = __( 'Slack','wp-uptime-robot-api' );
					break;
				default:
					$str_type = __( 'Not a Valid Alert Type', 'wp-uptime-robot-api' );
					break;
			}

			return $str_type;
		}

		/**
		 * HTTP response code messages.
		 *
		 * @api
		 *
		 * @param  string $code Response code to get message from.
		 * @return string       Message corresponding to response code sent in.
		 */
		public function response_code_msg( $code = '' ) {
			switch ( $code ) {
				case 100:
					$msg = __( 'ApiKey not mentioned or in a wrong format.','wp-uptime-robot-api' );
					break;
				case 101:
					$msg = __( 'ApiKey is wrong.','wp-uptime-robot-api' );
					break;
				case 102:
					$msg = __( 'Format is wrong (should be xml or json).','wp-uptime-robot-api' );
					break;
				case 103:
					$msg = __( 'No such method exists.','wp-uptime-robot-api' );
					break;
				case 200:
					$msg = __( 'MonitorID(s) should be integers.','wp-uptime-robot-api' );
					break;
				case 201:
					$msg = __( 'MonitorUrl is invalid.','wp-uptime-robot-api' );
					break;
				case 202:
					$msg = __( 'MonitorType is invalid.','wp-uptime-robot-api' );
					break;
				case 203:
					$msg = __( 'MonitorSubType is invalid.','wp-uptime-robot-api' );
					break;
				case 204:
					$msg = __( 'MonitorKeywordType is invalid.','wp-uptime-robot-api' );
					break;
				case 205:
					$msg = __( 'MonitorPort is invalid.','wp-uptime-robot-api' );
					break;
				case 206:
					$msg = __( 'MonitorFriendlyName is required.','wp-uptime-robot-api' );
					break;
				case 207:
					$msg = __( 'The monitor already exists.','wp-uptime-robot-api' );
					break;
				case 208:
					$msg = __( 'MonitorSubType is required for this type of monitors.','wp-uptime-robot-api' );
					break;
				case 209:
					$msg = __( 'The monitorKeyWordType and monitorKeyWordValue are required for this type of monitor.','wp-uptime-robot-api' );
					break;
				case 210:
					$msg = __( 'The monitorID does not exist.','wp-uptime-robot-api' );
					break;
				case 211:
					$msg = __( 'The monitorID is required.','wp-uptime-robot-api' );
					break;
				case 212:
					$msg = __( 'The account has no monitors.','wp-uptime-robot-api' );
					break;
				case 213:
					$msg = __( 'At least one of the parameters to be edited are required.','wp-uptime-robot-api' );
					break;
				case 214:
					$msg = __( 'The monitorHTTPUsername and monitorHTTPPassword should both be empty or have values.','wp-uptime-robot-api' );
					break;
				case 215:
					$msg = __( 'The monitor specific apiKeys can only use getMonitors method.','wp-uptime-robot-api' );
					break;
				case 216:
					$msg = __( 'A user with this e-mail already exists.','wp-uptime-robot-api' );
					break;
				case 217:
					$msg = __( 'The userFirstLastName and userEmail are both required.','wp-uptime-robot-api' );
					break;
				case 218:
					$msg = __( 'The userEmail is not in the right e-mail format.','wp-uptime-robot-api' );
					break;
				case 219:
					$msg = __( 'This account is not authorized to create users.','wp-uptime-robot-api' );
					break;
				case 220:
					$msg = __( 'The monitorAlertContacts value is wrong.','wp-uptime-robot-api' );
					break;
				case 221:
					$msg = __( 'The account has no alert contacts.','wp-uptime-robot-api' );
					break;
				case 222:
					$msg = __( 'The alertcontactID(s) should be integers.','wp-uptime-robot-api' );
					break;
				case 223:
					$msg = __( 'The alertContactType and alertContactValue are both required.','wp-uptime-robot-api' );
					break;
				case 224:
					$msg = __( 'This alertContactType is not supported".','wp-uptime-robot-api' );
					break;
				case 225:
					$msg = __( 'The alert contact already exists.','wp-uptime-robot-api' );
					break;
				case 226:
					$msg = __( 'The alert contact is not following @uptimerobot Twitter user. It is required so that the Twitter direct messages (DM) can be sent.','wp-uptime-robot-api' );
					break;
				case 227:
					$msg = __( 'The Boxcar user mentioned does not exist.','wp-uptime-robot-api' );
					break;
				case 228:
					$msg = __( 'The Boxcar alert contact could not be added, please try again later.','wp-uptime-robot-api' );
					break;
				case 229:
					$msg = __( 'The alertContactID does not exist.','wp-uptime-robot-api' );
					break;
				case 230:
					$msg = __( 'The alertContactValue should be a valid e-mail for this alertContactType.','wp-uptime-robot-api' );
					break;
				default:
					$msg = __( 'Response code unknown.', 'wp-uptime-robot-api' );
					break;
			}

			return $msg;
		}

		/**
		 * Implode the array and deliminate the values with '-' or return the variable
		 * as is.
		 *
		 * @param  array|string $var Array to be imploded or a string to be sent back.
		 * @return string            Imploded string.
		 */
		private function get_implode( $var ) {
			if ( is_array( $var ) ) {
				return implode( '-', $var );
			}

			return $var;
		}
	}
}
