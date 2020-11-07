<?php
/**
 * THESPA_Email Class.
 *
 * @package THESPA_waterTesting\Classes
 * @version 1.0.13
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class THESPA_Email
 */
class THESPA_Email {

	/**
	 * Send email with results.
	 *
	 * @param  string $email
	 * @param  string $js_id
	 * @param  array $generate
	 * @param  bool $is_message
	 * @param  string $message
	 * @return array
	 */
	public function to_email( $email, $js_id, $generate, $is_message = false, $message = '' ) {
		global $wpdb;

		$u = $wpdb->update( "{$wpdb->prefix}spa_sessions",
			array( 'email' => $email ),
			array( 'js_id' => $js_id )
		);
		$to = $email;
		$content = $generate['html'];
		$title = 'The Spa Shoppe — Online water testing Report';

		if ( $is_message !== false ) {
			$title = date("H:i:s d-m-Y") . " - $email";
			$message = htmlspecialchars( $message );
			$content = "<div style='border:1px solid #999; padding: 40px;'><i>User message:</i><br><b>$message</b><br><br><i>User email:</i><br><b>$email</b></div> $content";
			$id = $this->create_mail_post( $title, $content, $email, $message, $js_id );
//			$to = get_option( 'admin_email' );
			$to = 'info@luremarketing.ca';
			$title = 'The Spa Shoppe — Online water testing. Report from user.';
		}

		if ( isset( $generate['html'] ) AND $generate['html'] AND $email ) {
			wp_mail( $to, $title, $content, "Content-type: text/html; charset=UTF-8\r\n", [$generate['abs']] );
		}

		return [
			'update' => $u,
			'id' => $id,
		];
	}

	/**
	 * Create mail post.
	 *
	 * @param  string $title
	 * @param  string $content
	 * @param  string $email
	 * @param  string $message
	 * @param  string $js_id
	 * @return int
	 */
	public function create_mail_post( $title, $content, $email, $message, $js_id ) {
		$post_data = array(
			'post_title'    => wp_strip_all_tags( $title ),
			'post_content'  => $content,
			'post_status'   => 'publish',
			'post_type'     => 'thespa-message',
		);
		$id = wp_insert_post( $post_data );

		update_post_meta( $id, 'user_email', $email );
		update_post_meta( $id, 'user_message', $message );
		update_post_meta( $id, 'js_id', $js_id );

		return $id;
	}
}