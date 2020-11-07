<?php
/**
 * THESPA_data Class.
 *
 * @package THESPA_data\Classes
 * @version 1.0.13
 */
defined( 'ABSPATH' ) || exit;

/**
 * Class THESPA_data
 */
class THESPA_data {

	/**
	 * Form data from database.
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * List of devices.
	 *
	 * @var array
	 */
	private $devices = [];

	/**
	 * List of products.
	 *
	 * @var array
	 */
	private $products = [];

	/**
	 * THESPA_data Constructor.
	 */
	public function __construct() {
		$this->setup_data_from_db();
	}

	/**
	 * Data from data base into var $data.
	 *
	 * @return void
	 */
	private function setup_data_from_db() {
		global $wpdb;

		// get products
		$this->setup_products();

		// get 'devices'
		$this->devices = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}spa_devices` as devices", ARRAY_A );

		// get 'volume'/'type'
		$this->data = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}spa_volume` as volume", ARRAY_A );

		for ( $i = 0; $i < count( $this->data ); $i++ ) {
			// get 'chemical'
			$this->data[$i]['data'] = $wpdb->get_results(
				$wpdb->prepare( "
					SELECT chemical.* FROM `{$wpdb->prefix}spa_chemical` as chemical
					LEFT JOIN `{$wpdb->prefix}spa_volume_chemical` as relationtips
					ON chemical.`id` = relationtips.`id_chemical`
					WHERE relationtips.`id_volume` = '%d'
				", $this->data[$i]['id'] ), ARRAY_A );

			// get 'global_result'
			$this->data[$i]['global_result'] = $wpdb->get_results(
				$wpdb->prepare( "
					SELECT global_result.* FROM `{$wpdb->prefix}spa_global_result` as global_result
					LEFT JOIN `{$wpdb->prefix}spa_volume_global_result` as relationtips
					ON global_result.`id` = relationtips.`id_global_result`
					WHERE relationtips.`id_volume` = '%d'
				", $this->data[$i]['id'] ), ARRAY_A );

			for ( $j = 0; $j < count( $this->data[$i]['data'] ); $j++ ) {
				// get 'test'
				$this->data[$i]['data'][$j]['data'] = $wpdb->get_results(
					$wpdb->prepare( "
						SELECT test.* FROM `{$wpdb->prefix}spa_test` as test
						LEFT JOIN `{$wpdb->prefix}spa_chemical_test` as relationtips
						ON test.`id` = relationtips.`id_test`
						WHERE relationtips.`id_chemical` = '%d'
					", $this->data[$i]['data'][$j]['id'] ), ARRAY_A );

				for ( $k = 0; $k < count( $this->data[$i]['data'][$j]['data'] ); $k++ ) {
					// get 'value'
					$this->data[$i]['data'][$j]['data'][$k]['data'] = $wpdb->get_results(
						$wpdb->prepare( "
							SELECT spa_value.* FROM `{$wpdb->prefix}spa_value` as spa_value
							LEFT JOIN `{$wpdb->prefix}spa_test_value` as relationtips
							ON spa_value.`id` = relationtips.`id_value`
							WHERE relationtips.`id_test` = '%d'
						", $this->data[$i]['data'][$j]['data'][$k]['id'] ), ARRAY_A );

					for ( $l = 0; $l < count( $this->data[$i]['data'][$j]['data'][$k]['data'] ); $l++ ) {
						// get 'result'
						$this->data[$i]['data'][$j]['data'][$k]['data'][$l]['data'] = $wpdb->get_results(
							$wpdb->prepare( "
								SELECT result.* FROM `{$wpdb->prefix}spa_result` as result
								LEFT JOIN `{$wpdb->prefix}spa_value_result` as relationtips
								ON result.`id` = relationtips.`id_result`
								WHERE relationtips.`id_value` = '%d'
							", $this->data[$i]['data'][$j]['data'][$k]['data'][$l]['id'] ), ARRAY_A );
					}
				}
			}
		}
	}

	/**
	 * Data from data base into var $data.
	 *
	 * @param  string $type
	 * @param  int $id
	 * @return array
	 */
	public static function get_data_by_type_and_id( $type, $id ) {
		global $wpdb;

		$type = esc_sql( $type );
		return $wpdb->get_results(
			$wpdb->prepare( "
					SELECT * FROM `{$wpdb->prefix}spa_{$type}`
					WHERE `id` = '%d'
				", $id ), ARRAY_A );
	}

	/**
	 * Setup products list from `thespa_data`.`products`.
	 *
	 * @return void
	 */
	private function setup_products() {
		global $wpdb;

		$ids = [];
		$d = $wpdb->get_results( "SELECT distinct `products` FROM `{$wpdb->prefix}thespa_data`", ARRAY_A );
		foreach ( $d as $datum ) {
			$prs = explode( ",", $datum['products'] );
			foreach ( $prs as $pr ) {
				if ( !isset( $ids[$pr] ) ) {
					array_push($ids, $pr );
				}
			}
		}

		$i = 0;
		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			if ( is_object( $product ) ) {
				$this->products[$i] = [
					'id' => $id,
					'name' => $product->get_title(),
					'url' => $product->get_permalink(),
					'img' => wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'medium', true )[0],
					'cost' => $product->get_price_html(),
				];
				$i++;
			}
		}
	}

	/**
	 * Return data form by ID.
	 *
	 * @param  int $js_id
	 * @return array
	 */
	public function get_save_by_id( $js_id ) {
		global $wpdb;

		$user_id = get_current_user_id();
//		if ( !$user_id ) return [];

		$r = $wpdb->get_results( $wpdb->prepare( "
				SELECT `data` FROM `{$wpdb->prefix}spa_sessions`
				WHERE `user_id` = '%d' AND `js_id` = '%s'
			", $user_id, $js_id ), ARRAY_A );

		if ( !empty( $r ) AND $r[0]['data'] ) {
			return json_decode( $r[0]['data'] );
		}
		return [];
	}

	/**
	 * Return data form by ID.
	 *
	 * @return array
	 */
	public function get_init_blank_param() {
		$r = array_pop( $this->get_saves( $_GET['js_id'] ) );

		if ( !empty( $r ) AND $r['data'] ) {
			$data = json_decode( $r['data'] );
			$data->id = null;
			$data->chemical = null;
			$data->test = [];
			return $data;
		}
		return [];
	}

	/**
	 * Return data form by ID.
	 *
	 * @param  string $exception
	 * @return array
	 */
	public function get_saves( $exception = '' ) {
		global $wpdb;

		$user_id = get_current_user_id();
		if ( !$user_id ) return [];

		$add = '';
		if ( $exception ) {
			$exception = esc_sql( $exception );
			$add = "AND `js_id` != '$exception'";
		}

		return $wpdb->get_results( $wpdb->prepare( "
				SELECT * FROM `{$wpdb->prefix}spa_sessions`
				WHERE `user_id` = '%d' AND `is_remove` != '1' $add
			", $user_id ), ARRAY_A );
	}

	/**
	 * Return data.
	 *
	 * @return array
	 */
	public function get_data() {
		return apply_filters( 'THESPA_data', $this->data );
	}

	/**
	 * Return devices.
	 *
	 * @return array
	 */
	public function get_devices() {
		return apply_filters( 'THESPA_devices', $this->devices );
	}

	/**
	 * Return device by id.
	 *
	 * @param  int $id
	 * @return array|bool
	 */
	public function get_device( $id ) {
		foreach ( $this->devices as $device ) {
			if ( $device['id'] == $id ) {
				return $device;
			}
		}
		return false;
	}

	/**
	 * Return type by id.
	 *
	 * @param  int $id
	 * @return array|bool
	 */
	public function get_volume( $id ) {
		foreach ( $this->data as $volume ) {
			if ( $volume['id'] == $id ) {
				return $volume;
			}
		}
		return false;
	}

	/**
	 * Return products.
	 *
	 * @return array
	 */
	public function get_products() {
		return apply_filters( 'THESPA_products', $this->products );
	}

	/**
	 * Return lit of 'type'.
	 *
	 * @param  string
	 * @return array
	 */
	public function get_list_of( $name ) {
		$data = [];
		switch ( $name ) {
			case 'volume':
				foreach ( $this->data as $datum ) {
					$data[] = [
						'id' => $datum['id'],
						'name' => $datum['name'],
					];
				}
				break;
			case 'devices':
			case 'device':
				$data = $this->get_devices();
				break;
			case 'products':
				$data = $this->get_products();
				break;
		}

		return apply_filters( 'THESPA_data_list_of', $data, $name );
	}
}