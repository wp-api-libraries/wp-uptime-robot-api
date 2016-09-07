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
}
