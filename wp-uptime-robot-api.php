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

	static private $api_key;
	static private $format;
	static private $no_json_callback;
	private $base_uri = 'https://api.uptimerobot.com';

	/**
	 * [__construct description]
	 *
	 * @param [String] $api_key : API key to the account.
	 */
	public function __construct( $api_key = null, $format = 'json', $no_json_callback = 1 ) {
		static::$api_key = $api_key;
		static::$format = $format;
		static::$no_json_callback = $no_json_callback;
	}

	/**
	 * [get_account_details description]
	 */
	protected function get_account_details() {

	}

	/**
	 * [get_monitors description]
	 */
	protected function get_monitors() {

	}

	/**
	 * [new_monitor description]
	 */
	protected function new_monitor() {

	}

	/**
	 * [edit_monitor description]
	 */
	protected function edit_monitor() {

	}

	/**
	 * [delete_monitor description]
	 */
	protected function delete_monitor() {

	}

	/**
	 * [reset_monitor description]
	 */
	protected function reset_monitor() {

	}

	/**
	 * [get_alert_contacts description]
	 */
	protected function get_alert_contacts() {

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
}
