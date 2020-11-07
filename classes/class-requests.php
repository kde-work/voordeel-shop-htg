<?php
/**
 * THESPA_Requests Class.
 *
 * @package THESPA_waterTesting\Classes
 * @version 1.0.13
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class THESPA_Requests
 */
class THESPA_Requests {

	/**
	 * To email form.
	 */
	public static function form_handler() {
		if ( !empty( $_POST ) AND isset( $_POST['email'] ) AND isset( $_POST['data'] ) AND wp_verify_nonce( $_POST['nonce'], 'thespashoppe' ) ) {

			$message = ( isset( $_POST['message'] ) ) ? $_POST['message'] : false;
			$email = ( isset( $_POST['email'] ) ) ? $_POST['email'] : false;
			$is_message = ( $_POST['action'] == 'thespa_get-help' ) ? true : false;

			$data = $_POST['data'];
			$js_id = $data['id'];
			$generate_data = $data;
			$generate_data['results'] = stripslashes( $_POST['results'] );
			$generate_data['regular_results'] = stripslashes( $_POST['regular_results'] );

			// Save session
			$save = self::save_method( $data, $js_id, get_current_user_id()*1 );

			// Generate PDF
			$report = new THESPA_Report();
			$data['id'] = $js_id;
			$generate = $report->generate( $generate_data );

			// Send Email
			$mail = new THESPA_Email();
			$r = $mail->to_email( $email, $js_id, $generate, $is_message, $message );

			if ( $save['html'] ) {
				$message .= 'Form has been saved<br>';
			}
			$message .= 'The request was sent successfully!';

			echo json_encode( ['data' => $email . "; js_id : {$js_id}", 'r' => $r, 'pdf' => $generate['abs'], 'save' => $save, 'message' => $message] );
			die;
		}

		echo json_encode( ['error' => 'nonce error'] );
		die;
	}

	/**
	 * Save form.
	 *
	 * @param  $user_id
	 */
	public static function save( $user_id = false ) {
		if ( !empty( $_POST ) AND isset( $_POST['data'] ) AND wp_verify_nonce( $_POST['nonce'], 'thespashoppe' ) ) {
			$data = $_POST['data'];
			$js_id = $data['id'];
			$user_id = ( !$user_id ) ? get_current_user_id() : $user_id;

			echo json_encode( self::save_method( $data, $js_id, $user_id ) );
			die;
		}

		echo json_encode( ['error' => 'nonce error'] );
		die;
	}

	/**
	 * Save method.
	 *
	 * @param  array $data
	 * @param  int $js_id
	 * @param  int|bool $user_id
	 * @return array
	 */
	public static function save_method( $data, $js_id, $user_id ) {
		global $wpdb;

		$user_id = ( $user_id === false ) ? get_current_user_id() : $user_id;

		if( !$js_id ) {
			return ['data' => $data, 'error' => 'empty JS ID'];
		}

		$exist_id = $wpdb->get_var(
			$wpdb->prepare( "
					SELECT `id` FROM `{$wpdb->prefix}spa_sessions`
					WHERE `user_id` = '%d' AND `js_id` = '%s'
				", $user_id, $js_id ) );

		if( !$exist_id ) {
			$q = $wpdb->query(
				$wpdb->prepare( "
					INSERT INTO `{$wpdb->prefix}spa_sessions` SET
					`user_id` = '%d',
					`js_id` = '%s',
					`data` = '%s'
				", $user_id, $js_id, json_encode( $data ) ) );

			if ( $q ) {
				return ['html' => THESPA_shortcodes::getSaves(), 'data' => $data, 'success' => '1'];
			}
		} else {
			$q = $wpdb->query(
				$wpdb->prepare( "
				UPDATE `{$wpdb->prefix}spa_sessions` SET
					`data` = '%s'
					WHERE `id` = '%d' 
				", json_encode( $data ), $exist_id ) );

			if ( $q ) {
				return ['html' => THESPA_shortcodes::getSaves(), 'data' => $data, 'success' => '2'];
			} else {
				return ['data' => $data, 'html' => THESPA_shortcodes::getSaves(), 'error' => 'already exist'];
			}
		}

		return ['data' => $data, 'error' => 'data base error'];
	}

	/**
	 * Remove test.
	 */
	public static function remove_test() {
		if ( !empty( $_POST ) AND isset( $_POST['js_id'] ) AND wp_verify_nonce( $_POST['nonce'], 'thespashoppe' ) ) {
			global $wpdb;

			$js_id = $_POST['js_id'];
			$user_id = get_current_user_id();

			if( !$js_id ) {
				echo json_encode( ['error' => 'empty JS ID'] );
				die;
			}

			$q = $wpdb->query(
				$wpdb->prepare( "
					UPDATE `{$wpdb->prefix}spa_sessions` SET
						`is_remove` = '1'
						WHERE `user_id` = '%d' AND `js_id` = '%s'
					", $user_id, $js_id ) );

			if ( $q ) {
				echo json_encode( ['success' => 'remove 1'] );
				die;
			}

			echo json_encode( ['error' => 'not remove'] );
			die;
		}

		echo json_encode( ['error' => 'nonce error'] );
		die;
	}
}