<?php
/**
 * Simple PayPal Payment
 *
 * @package    Simple PayPal Payment
 * @subpackage SimplePayPalPayment Main Functions
/*  Copyright (c) 2019- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$simplepaypalpayment = new SimplePayPalPayment();

/** ==================================================
 * Class Main function
 *
 * @since 1.00
 */
class SimplePayPalPayment {

	/** ==================================================
	 * Attributes
	 *
	 * @var $simplepaypalpayment_atts  attributes.
	 */
	private $simplepaypalpayment_atts;

	/** ==================================================
	 * Construct
	 *
	 * @since   1.00
	 */
	public function __construct() {

		add_action( 'wp_footer', array( $this, 'load_localize_scripts_styles' ) );
		add_action( 'admin_footer', array( $this, 'load_localize_scripts_styles' ) );

		add_shortcode( 'simplepaypalpayment', array( $this, 'simplepaypalpayment_func' ) );
		add_action( 'init', array( $this, 'simplepaypalpayment_block_init' ) );

	}

	/** ==================================================
	 * Attribute block
	 *
	 * @since 1.00
	 */
	public function simplepaypalpayment_block_init() {

		$asset_file = include( plugin_dir_path( __DIR__ ) . 'block/dist/simplepaypalpayment-block.asset.php' );

		wp_register_script(
			'simplepaypalpayment-block',
			plugins_url( 'block/dist/simplepaypalpayment-block.js', dirname( __FILE__ ) ),
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		wp_localize_script(
			'simplepaypalpayment-block',
			'simplepaypalpayment_text',
			array(
				'codes' => __( 'Codes', 'simple-paypal-payment' ),
				'locale' => __( 'Locale Codes', 'simple-paypal-payment' ),
				'button' => __( 'Button Styles', 'simple-paypal-payment' ),
				'amount' => __( 'Price', 'simple-paypal-payment' ),
				'currency' => __( 'Currency Codes', 'simple-paypal-payment' ),
				'before' => __( 'Display before payment', 'simple-paypal-payment' ),
				'after' => __( 'Display after payment', 'simple-paypal-payment' ),
				'remove' => __( 'HTML elements to remove after payment', 'simple-paypal-payment' ),
				'view' => __( 'View' ),
				'check' => __( 'Can check the behavior in "Preview".', 'simple-paypal-payment' ),
				'button_attention' => __( 'Check the button style changes in the "Preview".', 'simple-paypal-payment' ),
				'payname' => __( 'Unique name for this payment', 'simple-paypal-payment' ),
			)
		);

		$locale_codes = $this->import_locale_csv( plugin_dir_path( __DIR__ ) . 'code/paypal_locale_code.csv' );
		wp_localize_script(
			'simplepaypalpayment-block',
			'simplepaypalpayment_locale_codes',
			$locale_codes
		);

		$currency_codes = $this->import_currency_csv( plugin_dir_path( __DIR__ ) . 'code/paypal_currency_code.csv' );
		wp_localize_script(
			'simplepaypalpayment-block',
			'simplepaypalpayment_currency_codes',
			$currency_codes
		);

		$simplepaypalpayment_settings = get_option( 'simplepaypalpayment_settings' );
		register_block_type(
			'simple-paypal-payment/simplepaypalpayment-block',
			array(
				'editor_script'   => 'simplepaypalpayment-block',
				'render_callback' => array( $this, 'simplepaypalpayment_block_func' ),
				'attributes'      => array(
					'locale'   => array(
						'type'    => 'string',
						'default' => $simplepaypalpayment_settings['locale'],
					),
					'size'     => array(
						'type'    => 'string',
						'default' => $simplepaypalpayment_settings['size'],
					),
					'color'    => array(
						'type'    => 'string',
						'default' => $simplepaypalpayment_settings['color'],
					),
					'shape'    => array(
						'type'    => 'string',
						'default' => $simplepaypalpayment_settings['shape'],
					),
					'label'    => array(
						'type'    => 'string',
						'default' => $simplepaypalpayment_settings['label'],
					),
					'amount'   => array(
						'type'    => 'string',
						'default' => $simplepaypalpayment_settings['amount'],
					),
					'currency' => array(
						'type'    => 'string',
						'default' => $simplepaypalpayment_settings['currency'],
					),
					'before'   => array(
						'type'    => 'string',
						'default' => html_entity_decode( $simplepaypalpayment_settings['before'] ),
					),
					'after'    => array(
						'type'    => 'string',
						'default' => html_entity_decode( $simplepaypalpayment_settings['after'] ),
					),
					'remove'   => array(
						'type'    => 'string',
						'default' => html_entity_decode( $simplepaypalpayment_settings['remove'] ),
					),
					'payname'  => array(
						'type'    => 'string',
						'default' => null,
					),
				),
			)
		);

	}

	/** ==================================================
	 * Blocks
	 *
	 * @param array  $atts  atts.
	 * @param string $content  content.
	 * @return string $content
	 * @since 2.00
	 */
	public function simplepaypalpayment_block_func( $atts, $content ) {

		$settings_tbl = get_option( 'simplepaypalpayment_settings' );

		foreach ( $settings_tbl as $key => $value ) {
			$blockkey = strtolower( $key );
			if ( empty( $atts[ $blockkey ] ) ) {
				$atts[ $blockkey ] = $value;
			} else {
				if ( strtolower( $atts[ $blockkey ] ) === 'false' ) {
					$atts[ $blockkey ] = null;
				}
			}
		}

		$this->simplepaypalpayment_atts = $atts;

		$content = '<div class="simple_paypal_payment_before">' . $atts['before'] . '</div><div class="simple_paypal_payment_after"></div><div id="simple_paypal_payment_paypal-button"></div>';

		if ( is_archive() || is_home() ) {
			$content = null;
		}

		return $content;

	}

	/** ==================================================
	 * Load Localize Script and Style
	 *
	 * @since 1.00
	 */
	public function load_localize_scripts_styles() {

		$localize_spp_settings = array();
		if ( is_singular() || is_admin() ) {
			$localize_spp_settings = $this->simplepaypalpayment_atts;
		} else { /* for widget */
			if ( ( is_archive() || is_home() ) && is_active_widget( false, false, 'simplepaypalpaymentwidgetitem', true ) ) {
				$localize_spp_settings = get_option( 'simplepaypalpayment_settings' );
			} else {
				return;
			}
		}
		if ( empty( $localize_spp_settings ) ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'paypal', 'https://www.paypalobjects.com/api/checkout.js', array(), '1.0.0', false );

		$paypal_settings = get_option( 'simplepaypalpayment_ids' );
		if ( $paypal_settings['sandbox'] ) {
			$env = array( 'env' => 'sandbox' );
		} else {
			$env = array( 'env' => 'production' );
		}

		$sandbox = array( 'sandbox_id' => apply_filters( 'spp_decrypt', $paypal_settings['sandbox_id'] ) );
		$production = array( 'production_id' => apply_filters( 'spp_decrypt', $paypal_settings['production_id'] ) );

		$localize_spp_settings = array_merge( $localize_spp_settings, $env );
		$localize_spp_settings = array_merge( $localize_spp_settings, $sandbox );
		$localize_spp_settings = array_merge( $localize_spp_settings, $production );

		$handle  = 'simple-paypal-payment-ajax-script';
		$action1 = 'simple-paypal-payment-charge-ajax-action';
		wp_enqueue_script( $handle, plugin_dir_url( __DIR__ ) . 'js/jquery.simplepaypalpayment.js', array( 'jquery' ), '1.0.0', false );
		$ajax_arr = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'action'   => $action1,
			'nonce'    => wp_create_nonce( $action1 ),
		);

		$ajax_arr = array_merge( $ajax_arr, $localize_spp_settings );
		wp_localize_script( $handle, 'SIMPLEPAYPALPAYMENTCHARGE', $ajax_arr );

	}

	/** ==================================================
	 * Short code
	 *
	 * @param Array  $atts  attributes.
	 * @param String $content  contents.
	 * @return String $content  contents.
	 * @since 1.00
	 */
	public function simplepaypalpayment_func( $atts, $content = null ) {

		$a = shortcode_atts(
			array(
				'locale'   => '',
				'size'     => '',
				'color'    => '',
				'shape'    => '',
				'label'    => '',
				'amount'   => '',
				'currency' => '',
				'before'   => '',
				'after'    => '',
				'remove'   => '',
				'remove2'  => '',
				'email'    => '',
				'payname'  => '',
			),
			$atts
		);

		$settings_tbl = get_option( 'simplepaypalpayment_settings' );

		foreach ( $settings_tbl as $key => $value ) {
			$shortcodekey = strtolower( $key );
			if ( empty( $a[ $shortcodekey ] ) ) {
				$a[ $shortcodekey ] = $value;
			} else {
				if ( strtolower( $a[ $shortcodekey ] ) === 'false' ) {
					$a[ $shortcodekey ] = null;
				}
			}
		}

		$this->simplepaypalpayment_atts = $a;

		if ( is_singular() || is_admin() ) {
			if ( ! empty( $a['before'] ) ) {
				$before_text = $a['before'];
			} else {
				$before_text = null;
			}
			$content = '<span class="simple_paypal_payment_before">' . $before_text . '</span><span class="simple_paypal_payment_after"></span><div id="simple_paypal_payment_paypal-button"></div>';
		} else {
			if ( is_archive() || is_home() ) {
				$content = null;
			} else {
				$content = __( 'It is not displayed on the edit screen. Please preview.', 'simple-paypal-payment' );
			}
		}

		return do_shortcode( $content );

	}

	/** ==================================================
	 * Import locale code
	 *
	 * @param string $csv_file  csv_file.
	 * @return array $locale_codes  locale_codes.
	 * @since 2.00
	 */
	private function import_locale_csv( $csv_file ) {

		$f = fopen( $csv_file, 'r' );
		$count = 0;
		$locale_codes = array();
		while ( $line = fgetcsv( $f ) ) {
			if ( 0 == $count ) { /* Delete BOM */
				$line = preg_replace( '/^\xEF\xBB\xBF/', '', $line );
			}

			$code = trim( $line[0] );
			$locale_codes[] = array(
				'value' => $code,
				'label' => $code,
			);

			++$count;
		}
		fclose( $f );

		return $locale_codes;

	}

	/** ==================================================
	 * Import currency code
	 *
	 * @param string $csv_file  csv_file.
	 * @return array $currency_codes  currency_codes.
	 * @since 2.00
	 */
	private function import_currency_csv( $csv_file ) {

		$f = fopen( $csv_file, 'r' );
		$count = 0;
		$currency_codes = array();
		while ( $line = fgetcsv( $f ) ) {
			if ( 0 == $count ) { /* Delete BOM */
				$line = preg_replace( '/^\xEF\xBB\xBF/', '', $line );
			}

			$code = trim( $line[1] );
			$country = trim( $line[0] );
			$currency_codes[] = array(
				'value' => $code,
				'label' => $code . ' - ' . $country,
			);

			++$count;
		}
		fclose( $f );

		return $currency_codes;

	}

}


