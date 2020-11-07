<?php
/**
 * THESPA_Report Class.
 *
 * @package THESPA_waterTesting\Classes
 * @version 1.0.13
 */
defined( 'ABSPATH' ) || exit;

use Dompdf\Dompdf;

/**
 * Class THESPA_Report
 */
class THESPA_Report {

	/**
	 * Generate PDF report of Water Testing Form.
	 *
	 * @param  $data
	 * @return array
	 */
	public function generate( $data ) {
		$data = $this->data_setup( $data );
		$folder = wp_upload_dir()['basedir'] . "/thespashoppe-pdf";
		if ( !is_dir( $folder ) ) mkdir( $folder, 0777, true );
		$data['folder'] = $folder;
		$generate = $this->generate_pdf( $data );

		$this->save_file_in_database( $data['id'], $generate['file_name'] );

		return $generate;
	}

	/**
	 * Save file in database.
	 *
	 * @param  $js_id
	 * @param  $file
	 * @return integer
	 */
	public function save_file_in_database( $js_id, $file ) {
		global $wpdb;

		return $wpdb->update( "{$wpdb->prefix}spa_sessions",
			array( 'file' => $file ),
			array( 'js_id' => $js_id )
		);
	}

	/**
	 * Setup data from JS ids.
	 *
	 * @param  $data
	 * @return array
	 */
	public function data_setup( $data ) {
		$obj_data = new THESPA_data();
		$data['title'] = THESPA_shortcodes::getName( $obj_data, $data, '' );
		$data['chemical-name'] = THESPA_data::get_data_by_type_and_id( 'chemical', $data['chemical'] )[0]['name'];

		foreach ( $data['test'] as $test => $value ) {
			$t = THESPA_data::get_data_by_type_and_id( 'test', $test )[0]['name'];
			$v = THESPA_data::get_data_by_type_and_id( 'value', $value )[0]['name'];
			$data['tests'] .= "$t: <b>$v</b><br>";
		}

		return $data;
	}

	/**
	 * Generate pdf.
	 *
	 * @param  $data
	 * @return array
	 */
	protected function generate_pdf( $data ) {
		$replace = [
			'%ID%' => $data['id'],
			'%TITLE%' => $data['title'],
			'%CHEMICAL%' => $data['chemical-name'],
			'%TESTS%' => $data['tests'],
			'%RESULTS%' => $data['results'],
			'%REGULAR_RESULTS%' => $data['regular_results'],
			'%LOGO%' => THESPA_PLUGIN_DIR . 'assets/img/logo.png'
		];
		$file_name = "The-Spa-Shoppe_{$data['id']}.pdf";

		$html = file_get_contents( THESPA_PLUGIN_DIR . 'pdf-report/report.html' );
		$html = strtr( $html, $replace );

		$dompdf = new Dompdf();
		$dompdf->loadHtml( $html, 'UTF-8' );
		$dompdf->setPaper( 'A4', 'portrait' );
		$dompdf->render();

//		// Brother show
//		$dompdf->stream( $file_name );

		// Server save
		$pdf = $dompdf->output();
		file_put_contents( $data['folder'] . "/{$file_name}", $pdf );
		file_put_contents( $data['folder'] . "/{$file_name}.html", $html );
		return [
			'html' => $html,
			'file_name' => $file_name,
			'abs' => $data['folder'] . "/{$file_name}"
		];
	}
}