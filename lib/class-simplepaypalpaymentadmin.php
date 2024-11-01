<?php
/**
 * Simple PayPal Payment
 *
 * @package    Simple PayPal Payment
 * @subpackage SimplePayPalPaymentAdmin Management screen
	Copyright (c) 2019- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

$simplepaypalpaymentadmin = new SimplePayPalPaymentAdmin();

/** ==================================================
 * Management screen
 */
class SimplePayPalPaymentAdmin {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_settings' ) );

		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );

		/* original hook */
		add_filter( 'spp_decrypt', array( $this, 'decrypt' ), 10, 1 );

	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param array  $links  links array.
	 * @param string $file  file.
	 * @return array $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'simple-paypal-payment/simplepaypalpayment.php';
		}
		if ( $file === $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'options-general.php?page=simplepaypalpayment' ) . '">' . __( 'Settings' ) . '</a>';
		}
			return $links;
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_menu() {
		add_options_page( 'Simple PayPal Payment Options', 'Simple PayPal Payment', 'manage_options', 'simplepaypalpayment', array( $this, 'plugin_options' ) );
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function plugin_options() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$this->options_updated();

		$scriptname                   = admin_url( 'options-general.php?page=simplepaypalpayment' );
		$paypal_settings              = get_option( 'simplepaypalpayment_ids' );
		$simplepaypalpayment_settings = get_option( 'simplepaypalpayment_settings' );

		?>

		<div class="wrap">
		<h2>Simple PayPal Payment</h2>

			<details>
			<summary><strong><?php esc_html_e( 'Various links of this plugin', 'simple-paypal-payment' ); ?></strong></summary>
			<?php $this->credit(); ?>
			</details>

			<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
			<?php wp_nonce_field( 'spp_set', 'simplepaypalpayment_set' ); ?>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'PayPal ID', 'simple-paypal-payment' ); ?></strong></summary>
<h4><a style="text-decoration: none;" href="https://developer.paypal.com/developer/applications/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get from REST API apps', 'simple-paypal-payment' ); ?></a></h4>
				<div style="display: block;padding:5px 5px"><input type="checkbox" name="sandboxmode" value="1" <?php checked( '1', $paypal_settings['sandbox'] ); ?>><?php esc_html_e( 'Sandbox mode', 'simple-paypal-payment' ); ?></div>
				<div style="display: block;padding:5px 5px"><?php esc_html_e( 'Sandbox ID', 'simple-paypal-payment' ); ?><input type="text" name="sandboxid" value="<?php echo esc_attr( $this->decrypt( $paypal_settings['sandbox_id'] ) ); ?>" style="width: 700px"></div>
				<div style="display: block;padding:5px 5px"><?php esc_html_e( 'Production ID', 'simple-paypal-payment' ); ?><input type="text" name="productionid" value="<?php echo esc_attr( $this->decrypt( $paypal_settings['production_id'] ) ); ?>" style="width: 700px"></div>
			</details>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( "Default value of shortcode and block and widget attribute (Widget's only use this value.)", 'simple-paypal-payment' ); ?></strong></summary>
				<table border=1 cellspacing="0" cellpadding="5" bordercolor="#000000" style="border-collapse: collapse">
				<tr>
				<td><strong><?php esc_html_e( 'Attribute', 'simple-paypal-payment' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'Default value', 'simple-paypal-payment' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'Description' ); ?></strong></td>
				</tr>
				<tr>
				<td>locale</td>
				<td>
				<input type="text" name="spp_locale" value="<?php echo esc_attr( $simplepaypalpayment_settings['locale'] ); ?>">
				</td>
				<td><a style="text-decoration: none;" href="https://developer.paypal.com/docs/integration/direct/rest/locale-codes/#supported-locale-codes" target="_blank" rel="noopener noreferrer">Supported locale codes</a></td>
				</tr>
				<tr>
				<td>size</td>
				<td>
				<select name="spp_size">
				<?php
				$spp_size_arr = array( 'small', 'medium', 'large', 'responsive' );
				foreach ( $spp_size_arr as $key ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $simplepaypalpayment_settings['size'], $key ); ?>><?php echo esc_html( $key ); ?></option>
					<?php
				}
				unset( $spp_size_arr );
				?>
				</select>
				</td>
				<td rowspan=4>
				<a style="text-decoration: none;" href="https://developer.paypal.com/docs/checkout/how-to/customize-button/#button-styles" target="_blank" rel="noopener noreferrer">Button styles</a></td>
				</tr>
				<tr>
				<td>color</td>
				<td>
				<select name="spp_color">
				<?php
				$spp_color_arr = array( 'gold', 'blue', 'silver', 'white', 'black' );
				foreach ( $spp_color_arr as $key ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $simplepaypalpayment_settings['color'], $key ); ?>><?php echo esc_html( $key ); ?></option>
					<?php
				}
				unset( $spp_color_arr );
				?>
				</select>
				</td>
				</tr>
				<tr>
				<td>shape</td>
				<td>
				<select name="spp_shape">
				<?php
				$spp_shape_arr = array( 'pill', 'rect' );
				foreach ( $spp_shape_arr as $key ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $simplepaypalpayment_settings['shape'], $key ); ?>><?php echo esc_html( $key ); ?></option>
					<?php
				}
				unset( $spp_shape_arr );
				?>
				</select>
				</td>
				</tr>
				<tr>
				<td>label</td>
				<td>
				<select name="spp_label">
				<?php
				$spp_label_arr = array( 'checkout', 'credit', 'pay', 'buynow', 'paypal' );
				foreach ( $spp_label_arr as $key ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $simplepaypalpayment_settings['label'], $key ); ?>><?php echo esc_html( $key ); ?></option>
					<?php
				}
				unset( $spp_label_arr );
				?>
				</select>
				</td>
				</tr>
				<tr>
				<td>amount</td>
				<td>
				<input type="text" name="spp_amount" value="<?php echo esc_attr( $simplepaypalpayment_settings['amount'] ); ?>">
				</td>
				<td><?php esc_html_e( 'Price', 'simple-paypal-payment' ); ?></td>
				</tr>
				<tr>
				<td>currency</td>
				<td>
				<select name="spp_currency">
				<?php
				$spp_currency_arr = array(
					'USD' => 'United States dollar',
					'AUD' => 'Australian dollar',
					'BRL' => 'Brazilian real',
					'CAD' => 'Canadian dollar',
					'CZK' => 'Czech koruna',
					'DKK' => 'Danish krone',
					'EUR' => 'Euro',
					'HKD' => 'Hong Kong dollar',
					'HUF' => 'Hungarian forint',
					'INR' => 'Indian rupee',
					'ILS' => 'Israeli new shekel',
					'JPY' => 'Japanese yen',
					'MYR' => 'Malaysian ringgit',
					'MXN' => 'Mexican peso',
					'TWD' => 'New Taiwan dollar',
					'NZD' => 'New Zealand dollar',
					'NOK' => 'Norwegian krone',
					'PHP' => 'Philippine peso',
					'PLN' => 'Polish zloty',
					'GBP' => 'Pound sterling',
					'RUB' => 'Russian ruble',
					'SGD' => 'Singapore dollar',
					'SEK' => 'Swedish krona',
					'CHF' => 'Swiss franc',
					'THB' => 'Thai baht',
				);
				foreach ( $spp_currency_arr as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $simplepaypalpayment_settings['currency'], $key ); ?>><?php echo esc_html( $value ); ?></option>
					<?php
				}
				unset( $spp_currency_arr );
				?>
				</select>
				</td>
				<td><a style="text-decoration: none;" href="https://developer.paypal.com/docs/integration/direct/rest/currency-codes/" target="_blank" rel="noopener noreferrer">Currency Codes</a></td>
				</tr>
				<tr>
				<td>before</td>
				<td>
				<textarea name="spp_before" style="resize: auto; max-width: 500px; max-height: 500px; min-width: 100px; min-height: 100px; width:500px; height:100px"><?php echo esc_html( html_entity_decode( $simplepaypalpayment_settings['before'] ) ); ?></textarea>
				</td>
				<td><?php esc_html_e( 'Display before payment', 'simple-paypal-payment' ); ?></td>
				</tr>
				<tr>
				<td>after</td>
				<td>
				<textarea name="spp_after" style="resize: auto; max-width: 500px; max-height: 500px; min-width: 100px; min-height: 100px; width:500px; height:100px"><?php echo esc_html( html_entity_decode( $simplepaypalpayment_settings['after'] ) ); ?></textarea>
				</td>
				<td><?php esc_html_e( 'Display after payment', 'simple-paypal-payment' ); ?></td>
				</tr>
				<tr>
				<td>remove</td>
				<td>
				<input type="text" name="spp_remove" value="<?php echo esc_attr( $simplepaypalpayment_settings['remove'] ); ?>">
				</td>
				<td rowspan="2"><?php esc_html_e( 'HTML elements to remove after payment', 'simple-paypal-payment' ); ?></td>
				</tr>
				<tr>
				<td>remove2</td>
				<td rowspan="3"><?php esc_html_e( 'This is a special attribute. Only shortcode are valid.', 'simple-paypal-payment' ); ?></td>
				</tr>
				<tr>
				<td>email</td>
				<td><?php esc_html_e( 'Email' ); ?></td>
				</tr>
				<tr>
				<td>payname</td>
				<td><?php esc_html_e( 'Unique name for this payment', 'simple-paypal-payment' ); ?></td>
				</tr>
				</table>
			</details>

			<details style="margin-bottom: 5px;">
			<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Apply Filters' ); ?></strong></summary>
				<h3><?php esc_html_e( 'The following filters are provided.', 'simple-paypal-payment' ); ?></h3>
				<h4>simple_paypal_payment_charge</h4>
				<div><?php esc_html_e( 'Processing when charging is successful.', 'simple-paypal-payment' ); ?></div>
				<div style="margin: 5px; padding: 5px;">
				<table border=1 cellspacing="0" cellpadding="5" bordercolor="#000000" style="border-collapse: collapse">
				<tr>
				<td><strong><?php esc_html_e( 'Variable', 'simple-paypal-payment' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'Description' ); ?></strong></td>
				<td><strong><?php esc_html_e( 'From', 'simple-paypal-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$data</strong></td>
				<td rowspan="2"><strong><?php esc_html_e( 'Payment information by JSON', 'simple-paypal-payment' ); ?></strong></td>
				<td rowspan="2"><strong><?php esc_html_e( 'Value of PayPal', 'simple-paypal-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$payment_data</strong></td>
				</tr>
				<tr>
				<td><strong>$email</strong></td>
				<td><strong><?php esc_html_e( 'Email' ); ?></strong></td>
				<td rowspan="4"><strong><?php esc_html_e( 'Value of Simple PayPal Payment', 'simple-paypal-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$amount</strong></td>
				<td><strong><?php esc_html_e( 'Price', 'simple-paypal-payment' ); ?></strong></td>
				</tr>
				<tr>
				<td><strong>$currency</strong></td>
				<td><strong><a style="text-decoration: none;" href="https://developer.paypal.com/docs/integration/direct/rest/currency-codes/" target="_blank" rel="noopener noreferrer">Currency Codes</a></strong></td>
				</tr>
				<tr>
				<td><strong>$payname</strong></td>
				<td><strong><?php esc_html_e( 'Unique name for this payment', 'simple-paypal-payment' ); ?></strong></td>
				</tr>
				</table>
				</div>
				<div><strong><?php esc_html_e( 'Sample code', 'simple-paypal-payment' ); ?></strong></div>
<textarea rows="25" cols="120" readonly>
/** ==================================================
* Show button for shortcode
*/
&lsaquo;?php echo do_shortcode('[simplepaypalpayment size="medium" amount=20 currency="USD" email="test@test.com" payname="testpay"]'); ?&rsaquo;

/** ==================================================
* Filter of Simple PayPal Payment
*
* @param string $data  data.
* @param string $payment_data  payment_data.
* @param string $email  email.
* @param int    $amount  amount.
* @param string $currency  currency.
* @param string $payname  payname.
*/
function paypal_charge( $data, $payment_data, $email, $amount, $currency, $payname ) {

/* Please write the process to be done when billing succeeds. */
if ( 'testpay' === $payname ) {
update_option( 'testpay_paypal', 'paypal' . $payname . $amount . $currency );
}

}
add_filter( 'simple_paypal_payment_charge', 'paypal_charge', 10, 6 );
</textarea>
			</details>

			<?php submit_button( __( 'Save Changes' ), 'large', 'Manageset', false ); ?>
			</form>

		</div>
		<?php
	}

	/** ==================================================
	 * Credit
	 *
	 * @since 1.00
	 */
	private function credit() {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_ver_num;
		/* translators: FAQ Link & Slug */
		$faq       = sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'simple-paypal-payment' ), $slug );
		$support   = 'https://wordpress.org/support/plugin/' . $slug;
		$review    = 'https://wordpress.org/support/view/plugin-reviews/' . $slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/' . $slug;
		$facebook  = 'https://www.facebook.com/katsushikawamori/';
		$twitter   = 'https://twitter.com/dodesyo312';
		$youtube   = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate    = __( 'https://shop.riverforest-wp.info/donate/', 'simple-paypal-payment' );

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo esc_html( $plugin_version ); ?> | 
		<a style="text-decoration: none;" href="<?php echo esc_url( $faq ); ?>" target="_blank" rel="noopener noreferrer">FAQ</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer">Support Forums</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $review ); ?>" target="_blank" rel="noopener noreferrer">Reviews</a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo esc_url( $translate ); ?>" target="_blank" rel="noopener noreferrer">
		<?php
		/* translators: Plugin translation link */
		echo esc_html( sprintf( __( 'Translations for %s' ), $plugin_name ) );
		?>
		</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php esc_html_e( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'simple-paypal-payment' ); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo esc_url( $donate ); ?>')"><?php esc_html_e( 'Donate to this plugin &#187;' ); ?></button>
		</div>

		<?php

	}

	/** ==================================================
	 * Update wp_options table.
	 *
	 * @since 1.00
	 */
	private function options_updated() {

		if ( isset( $_POST['Manageset'] ) && ! empty( $_POST['Manageset'] ) ) {
			if ( check_admin_referer( 'spp_set', 'simplepaypalpayment_set' ) ) {
				$paypal_settings = get_option( 'simplepaypalpayment_ids' );
				if ( isset( $_POST['sandboxmode'] ) && ! empty( $_POST['sandboxmode'] ) ) {
					$paypal_settings['sandbox'] = 1;
				} else {
					$paypal_settings['sandbox'] = false;
				}
				if ( isset( $_POST['sandboxid'] ) && ! empty( $_POST['sandboxid'] ) ) {
					$paypal_settings['sandbox_id'] = $this->encrypt( sanitize_text_field( wp_unslash( $_POST['sandboxid'] ) ) );
				}
				if ( isset( $_POST['productionid'] ) && ! empty( $_POST['productionid'] ) ) {
					$paypal_settings['production_id'] = $this->encrypt( sanitize_text_field( wp_unslash( $_POST['productionid'] ) ) );
				}
				update_option( 'simplepaypalpayment_ids', $paypal_settings );
				$simplepaypalpayment_settings = get_option( 'simplepaypalpayment_settings' );
				if ( isset( $_POST['spp_locale'] ) && ! empty( $_POST['spp_locale'] ) ) {
					$simplepaypalpayment_settings['locale'] = sanitize_text_field( wp_unslash( $_POST['spp_locale'] ) );
				}
				if ( isset( $_POST['spp_size'] ) && ! empty( $_POST['spp_size'] ) ) {
					$simplepaypalpayment_settings['size'] = sanitize_text_field( wp_unslash( $_POST['spp_size'] ) );
				}
				if ( isset( $_POST['spp_color'] ) && ! empty( $_POST['spp_color'] ) ) {
					$simplepaypalpayment_settings['color'] = sanitize_text_field( wp_unslash( $_POST['spp_color'] ) );
				}
				if ( isset( $_POST['spp_shape'] ) && ! empty( $_POST['spp_shape'] ) ) {
					$simplepaypalpayment_settings['shape'] = sanitize_text_field( wp_unslash( $_POST['spp_shape'] ) );
				}
				if ( isset( $_POST['spp_label'] ) && ! empty( $_POST['spp_label'] ) ) {
					$simplepaypalpayment_settings['label'] = sanitize_text_field( wp_unslash( $_POST['spp_label'] ) );
				}
				if ( isset( $_POST['spp_amount'] ) && ! empty( $_POST['spp_amount'] ) ) {
					$simplepaypalpayment_settings['amount'] = intval( $_POST['spp_amount'] );
				}
				if ( isset( $_POST['spp_currency'] ) && ! empty( $_POST['spp_currency'] ) ) {
					$simplepaypalpayment_settings['currency'] = sanitize_text_field( wp_unslash( $_POST['spp_currency'] ) );
				}
				if ( isset( $_POST['spp_before'] ) && ! empty( $_POST['spp_before'] ) ) {
					$simplepaypalpayment_settings['before'] = htmlentities( sanitize_text_field( wp_unslash( $_POST['spp_before'] ) ) );
				}
				if ( isset( $_POST['spp_after'] ) && ! empty( $_POST['spp_after'] ) ) {
					$simplepaypalpayment_settings['after'] = htmlentities( sanitize_text_field( wp_unslash( $_POST['spp_after'] ) ) );
				}
				if ( isset( $_POST['spp_remove'] ) && ! empty( $_POST['spp_remove'] ) ) {
					$simplepaypalpayment_settings['remove'] = sanitize_text_field( wp_unslash( $_POST['spp_remove'] ) );
				}
				update_option( 'simplepaypalpayment_settings', $simplepaypalpayment_settings );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html__( 'Settings' ) . ' --> ' . esc_html__( 'Settings saved.' ) . '</li></ul></div>';
			}
		}

	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.00
	 */
	public function register_settings() {

		if ( ! get_option( 'simplepaypalpayment_ids' ) ) {
			$paypal_tbl = array(
				'sandbox'       => 1,
				'sandbox_id'    => null,
				'production_id' => null,
			);
			update_option( 'simplepaypalpayment_ids', $paypal_tbl );
		}

		if ( get_option( 'simplepaypalpayment_settings' ) ) {
			$spp_settings = get_option( 'simplepaypalpayment_settings' );
			if ( ! array_key_exists( 'remove', $spp_settings ) ) {
				/* ver 1.05 later */
				$spp_settings['remove'] = null;
				update_option( 'simplepaypalpayment_settings', $spp_settings );
			}
		} else {
			$settings_tbl = array(
				'locale'   => 'en_US',
				'size'     => 'small',
				'color'    => 'gold',
				'shape'    => 'pill',
				'label'    => 'checkout',
				'amount'   => 10,
				'currency' => 'USD',
				'before'   => null,
				'after'    => null,
				'remove'   => null,
			);
			update_option( 'simplepaypalpayment_settings', $settings_tbl );
		}

	}

	/**
	 * Crypt AES 256
	 * https://blog.ohgaki.net/encrypt-decrypt-using-openssl
	 *
	 * @param data $data  data.
	 * @return base64 encrypted  encrypted.
	 */
	private function encrypt( $data ) {

		$password = 'simple_paypal_payment';

		/* Set a random salt */
		$salt = openssl_random_pseudo_bytes( 16 );

		$salted = '';
		$dx     = '';
		/* Salt the key(32) and iv(16) = 48 */
		$length = '';
		while ( $length < 48 ) {
			$dx      = hash( 'sha256', $dx . $password . $salt, true );
			$salted .= $dx;
			$length  = strlen( $salted );
		}

		$key = substr( $salted, 0, 32 );
		$iv  = substr( $salted, 32, 16 );

		$encrypted_data = openssl_encrypt( $data, 'AES-256-CBC', $key, true, $iv );
		return base64_encode( $salt . $encrypted_data );
	}

	/**
	 * Decrypt AES 256
	 * https://blog.ohgaki.net/encrypt-decrypt-using-openssl
	 *
	 * @param data $edata  edata.
	 * @return decrypted $data  data.
	 */
	public function decrypt( $edata ) {

		$password = 'simple_paypal_payment';

		$data = base64_decode( $edata );
		$salt = substr( $data, 0, 16 );
		$ct   = substr( $data, 16 );

		$rounds  = 3; /* depends on key length */
		$data00  = $password . $salt;
		$hash    = array();
		$hash[0] = hash( 'sha256', $data00, true );
		$result  = $hash[0];
		for ( $i = 1; $i < $rounds; $i++ ) {
			$hash[ $i ] = hash( 'sha256', $hash[ $i - 1 ] . $data00, true );
			$result    .= $hash[ $i ];
		}
		$key = substr( $result, 0, 32 );
		$iv  = substr( $result, 32, 16 );

		return openssl_decrypt( $ct, 'AES-256-CBC', $key, true, $iv );
	}

}


