<?php
/**
 * Plugin Name: XTube Toolkit - Core Plugin
 * Plugin URI: https://saieed.unaux.com/
 * Description: Advanced toolkit for XTube theme with video management, analytics, custom post types, and modern features.
 * Version: 3.0.0
 * Author: Saieed Rahman
 * Author URI: https://saieed.unaux.com/
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpst
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.5
 * Requires PHP: 8.0
 * Network: false
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

require_once plugin_dir_path( __FILE__ ) . 'babiato.php';

/**
 * Singleton Class
 */
final class WPSCORE {

	/**
	 * The instance of the CORE plugin
	 *
	 * @var      instanceof WPSCORE $instance
	 * @static
	 */
	private static $instance;

	/**
	 * The config of the CORE plugin
	 *
	 * @var      array $config
	 * @static
	 */
	private static $config;

	/**
	 * __clone method
	 *
	 * @return   void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'WPSCORE' ), '1.0' );}

	/**
	 * __wakeup method
	 *
	 * @return   void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'WPSCORE' ), '1.0' );
	}

	/**
	 * Instance method
	 *
	 * @return   self::$instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPSCORE ) ) {
			self::$instance = new WPSCORE();
			// Load textdomain.
			self::$instance->load_textdomain();
			// Load config file.
			require_once plugin_dir_path( __FILE__ ) . 'config.php';

			// Load log system.
			require_once plugin_dir_path( __FILE__ ) . 'admin/logs/class-wpscore-log.php';

			// Load options system.
			if ( true ) {
				require_once plugin_dir_path( __FILE__ ) . 'xbox/xbox.php';
			}
			if ( is_admin() ) {
				self::$instance->load_hooks();
				self::$instance->auto_load_php_files( 'admin' );
				self::$instance->init( false );
			}
		}
		return self::$instance;
	}

	/**
	 * Load_hooks method
	 *
	 * @return   void
	 */
	public function load_hooks() {

		add_action( 'admin_init', array( $this, 'add_wordfence_compatibily' ) );
		add_action( 'admin_init', array( $this, 'save_default_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'auto_load_scripts' ), 100 );
		add_action( 'admin_init', array( $this, 'reorder_submenu' ) );
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		/**
		 * TODO
		 * Add register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );
		 */
	}

	/**
	 * Bypass Wordfence on ajax call that can be blocked because of requests to adult tubes
	 * admin_init hook callback.
	 *
	 * @see load_hooks()
	 *
	 * @since 1.3.8
	 * @return void
	 */
	public function add_wordfence_compatibily() {
		if ( $this->is_wordfence_activated() ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) ) {
				$post_action = sanitize_text_field( wp_unslash( $_POST['action'] ) );
				if ( $this->is_wpscript_ajax_action( $post_action ) ) {
					$wordfence = wfWAF::getInstance()->getStorageEngine();
					if ( 'enabled' === $wordfence->getConfig( 'wafStatus' ) ) {
						$wordfence->setConfig( 'wafStatus', 'learning-mode' );
					}
				}
			}
		}
	}

	/**
	 * Detect if Wordfence plugin is activated.
	 *
	 * @return boolean True if it is activated, false if not.
	 */
	public function is_wordfence_activated() {
		return is_plugin_active( 'wordfence/wordfence.php' );
	}

	/**
	 * Check if a given $ajax_action is a wp-script one
	 *
	 * @param string $ajax_action The ajax action.
	 *
	 * @since 1.3.8
	 * @return bool true if it is a wp-script ajax action, false if not.
	 */
	private function is_wpscript_ajax_action( $ajax_action ) {
		if ( ! $ajax_action ) {
			return false;
		}
		$products_skus = (array) $this->get_products_skus();
		foreach ( $products_skus as $product_sku ) {
			$product_sku = strtolower( 'CORE' === $product_sku ? 'WPSCORE' : $product_sku );
			$ajax_action = strtolower( $ajax_action );
			if ( false !== strpos( $ajax_action, $product_sku ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the current page slug.
	 *
	 * @return string The current page slug.
	 */
	private function get_current_page_slug() {
		if ( ! isset( $_GET['page'] ) ) {
			return '';
		}
		return sanitize_title( wp_unslash( $_GET['page'] ) );
	}

	/**
	 * Method to save default Xbox options on admin_init hook action
	 *
	 * @return void
	 */
	public function save_default_options() {
		check_ajax_referer( 'ajax-nonce', 'nonce', false );
		if ( ! WPSCORE()->php_version_ok() || 'wpscore-dashboard' !== $this->get_current_page_slug() ) {
			return;
		}
		$all_options = xbox_get_all();
		foreach ( (array) $all_options as $xbox_id => $xbox_options ) {
			if ( get_option( $xbox_id ) === false ) {
				$xbox = xbox_get( strtolower( $xbox_id ) );
				if ( $xbox ) {
					$xbox->save_fields( 0, array( 'display_message_on_save' => false ) );
				}
			}
		}
	}

	/**
	 * Method to load js and css files in CORE and all WP-SCRIPT products
	 *
	 * @return void
	 */
	public function auto_load_scripts() {
		// phpcs:disable
		$scripts        = apply_filters( 'WPSCORE-scripts', self::$config['scripts']['js'] + self::$config['scripts']['css'] );
		// phpcs:enable
		$wpscript_pages    = $this->get_pages_slugs();
		$current_page_slug = $this->get_current_page_slug();

		if ( in_array( $current_page_slug, $wpscript_pages, true ) && strpos( $current_page_slug, '-options' ) === false ) {
			global $wp_scripts, $wp_styles;
			// Removing Bootstrap scripts on wp-script pages.
			foreach ( (array) $wp_scripts->registered as $script_key => $script_config ) {
				if ( strpos( $script_config->src, 'bootstrap' ) !== false ) {
					wp_deregister_script( $script_key );
				}
			}
			// Removing Bootstrap styles on wp-script pages.
			foreach ( (array) $wp_styles->registered as $script_key => $script_config ) {
				if ( strpos( $script_config->src, 'bootstrap' ) !== false ) {
					wp_deregister_script( $script_key );
				}
			}
		}

		// add wp-script scripts and css on WP-Script pages.
		foreach ( (array) $scripts as $k => $v ) {
			if ( ! isset( $v['in_pages'] ) || in_array( $current_page_slug, ( 'wpscript_pages' === $v['in_pages'] ? $wpscript_pages : $v['in_pages'] ), true ) ) {
				$type    = explode( '.', $k );
				$type    = end( $type );
				$sku     = explode( '_', $k );
				$sku     = current( $sku );
				$path    = str_replace( array( 'http:', 'https:' ), array( '', '' ), constant( $sku . '_URL' ) . $v['path'] );
				$uri     = str_replace( array( 'http:', 'https:' ), array( '', '' ), constant( $sku . '_DIR' ) . $v['path'] );
				$version = $v['version'] . '.' . filemtime( $uri );
				switch ( $type ) {
					case 'js':
						// exclude script if option pages and script is bootstrap to avoid dropdown conflicts.
						if ( strpos( $current_page_slug, '-options' ) !== false && 'WPSCORE_bootstrap.js' === $k ) {
							break;
						}
						// exclude script if option pages and script is lodash to avoid gutenberg and underscore conflicts.
						if ( strpos( $current_page_slug, '-options' ) !== false && 'WPSCORE_lodash.js' === $k ) {
							break;
						}
						$v['in_footer'] = true; // Force to load scripts in footer to prevent JS loading issues.
						wp_enqueue_script( $k, $path, $v['require'], $version, $v['in_footer'] );
						if ( isset( $v['localize'] ) && ! empty( $v['localize'] ) ) {
							if ( isset( $v['localize']['ajax'] ) && true === $v['localize']['ajax'] ) {
								$v['localize']['ajax'] = array(
									'url'   => str_replace( array( 'http:', 'https:' ), array( '', '' ), admin_url( 'admin-ajax.php' ) ),
									'nonce' => wp_create_nonce( 'ajax-nonce' ),
								);
							}
							wp_localize_script( $k, str_replace( array( '-', '.js' ), array( '_', '' ), $k ), $v['localize'] );
						}
						break;
					case 'css':
						wp_enqueue_style( $k, $path, $v['require'], $version, $v['media'] );
						break;
					default:
						break;
				}
			}
		}
	}

	/**
	 * Auto-loader for PHP files
	 *
	 * @since 1.0.0
	 *
	 * @param string{'admin','public'} $dir Directory where to find PHP files to load.
	 * @static
	 * @return void
	 */
	public function auto_load_php_files( $dir ) {
		$dirs = (array) ( plugin_dir_path( __FILE__ ) . $dir . '/' );
		foreach ( (array) $dirs as $dir ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir ) );
			if ( ! empty( $files ) ) {
				foreach ( $files as $file ) {
					// exlude dir.
					if ( $file->isDir() ) {
						continue; }
					// exlude index.php.
					if ( $file->getPathname() === 'index.php' ) {
						continue; }
					// exlude files != .php.
					if ( substr( $file->getPathname(), -4 ) !== '.php' ) {
						continue; }
					// exlude files from -x suffixed directories.
					if ( substr( $file->getPath(), -2 ) === '-x' ) {
						continue; }
					// exlude -x suffixed files.
					if ( substr( $file->getPathname(), -6 ) === '-x.php' ) {
						continue; }
					// else require file.
					require $file->getPathname();
				}
			}
		}
	}

	/**
	 * Stuff to do on WPSCORE activation. This is a register_activation_hook callback function.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 * @return void
	 */
	public static function activation() {
		WPSCORE()->update_client_signature();
		add_action( 'WPSCORE_init', 'wpscore_cron_init' );
		WPSCORE()->init( true );
	}

	/**
	 * Stuff to do on WPSCORE deactivation. This is a register_deactivation_hook callback function.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 * @return void
	 */
	public static function deactivation() {
		WPSCORE()->update_client_signature();
		wp_clear_scheduled_hook( 'WPSCORE_init' );
		WPSCORE()->init( true );
	}

	/**
	 * Stuff to do on WPSCORE deactivation. This is a register_deactivation_hook callback function.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 * @return void
	 */
	public static function uninstall() {
		delete_option( 'WPSCORE_options' );
		wp_clear_scheduled_hook( 'WPSCORE_init' );
		WPSCORE()->init( true );
	}

	/**
	 * Get server adress.
	 *
	 * @return string The server adress.
	 */
	public function get_server_addr() {
		$server_addr = $this->sanitize_server_var( 'SERVER_ADDR' );
		if ( '' === $server_addr ) {
			$server_addr = $this->sanitize_server_var( 'LOCAL_ADDR' );
		}
		return $server_addr;
	}

	/**
	 * Get server name.
	 *
	 * @return string The server name
	 */
	public function get_server_name() {
		$forbidden_server_names = array( '', '_', '$domain' );
		$server_name            = $this->sanitize_server_var( 'SERVER_NAME' );
		$fallback_server_name   = str_replace( array( 'http://', 'https://' ), array( '', '' ), get_site_url() );
		return ( ! in_array( $server_name, $forbidden_server_names, true ) ) ? $server_name : $fallback_server_name;
	}

	/**
	 * Sanitize a $_SERVER var for a given key.
	 *
	 * @param string $var_key The server var to sanitize.
	 *
	 * @return string The server var value or an empty string if not found.
	 */
	public function sanitize_server_var( $var_key ) {
		$var_key = strtoupper( $var_key );
		return isset( $_SERVER[ $var_key ] ) ? sanitize_text_field( wp_unslash( $_SERVER[ $var_key ] ) ) : '';
	}

	/**
	 * Get all api auth parameters.
	 * Used by WPSCORE_Api class to inject auth params.
	 *
	 * @see \admin\class\WPSCORE_Api
	 *
	 * @since 1.3.9
	 *
	 * @access public
	 * @return array The API Auth params as an array.
	 */
	public function get_api_auth_params() {
		return array(
			'license_key'  => $this->get_license_key(),
			'signature'    => $this->get_client_signature(),
			'server_addr'  => $this->get_server_addr(),
			'server_name'  => $this->get_server_name(),
			'core_version' => WPSCORE_VERSION,
			'time'         => ceil( time() / 1000 ),
		);
	}

	/**
	 * Get license key.
	 *
	 * @return string The WP-Script license key.
	 */
	public function get_license_key() {
		return $this->get_option( 'wps_license' );
	}

	/**
	 * Update license key.
	 *
	 * @param string $new_license_key The new license key to save.
	 *
	 * @return bool true if The WP-Script license key has been updated, false if not.
	 */
	public function update_license_key( $new_license_key ) {
		return $this->update_option( 'wps_license', $new_license_key );
	}

	/**
	 * Get client signature.
	 *
	 * @return stdClass The client signature.
	 */
	public function get_client_signature() {
		return $this->get_option( 'signature' );
	}

	/**
	 * Update client signature.
	 *
	 * @return bool true if The client signature has been updated, false if not.
	 */
	public function update_client_signature() {
		if ( ! $this->get_client_signature() ) {
			return false;
		}
		$signature       = $this->get_client_signature();
		$signature->site = microtime( true );
		return $this->update_option( 'signature', $signature );
	}

	/**
	 * Get WP-Script.com API url
	 *
	 * @deprecated 1.3.9 Use WPSCORE_Api class instead.
	 *
	 * @param string $action The action to call. (i.e. 'init', 'amve/get_feed').
	 * @param string $base64_params The params to pass to call the API.
	 *
	 * @return string The WP-Sccript API url.
	 */
	public function get_api_url( $action, $base64_params ) {
		return WPSCORE_API_URL . '/' . $action . '/' . $base64_params;
	}

	/**
	 * Get all WPSCORE options.
	 *
	 * @return array WPSCORE options.
	 */
	public function get_options() {
		return $this->get_product_options( 'WPSCORE' );
	}

	/**
	 * Get a specific WPSCORE option given its $option_key
	 *
	 * @param string $option_key The option key we want to retrieve.
	 *
	 * @return mixed The WPSCORE option we're looking for.
	 */
	public function get_option( $option_key ) {
		return $this->get_product_option( 'WPSCORE', $option_key );
	}

	/**
	 * Update WPSCORE option.
	 *
	 * @param string $option_key  The option key we want to update.
	 * @param mixed  $new_value   The new value to store.
	 *
	 * @return bool False if value was not updated and true if value was updated.
	 */
	public function update_option( $option_key, $new_value ) {
		return $this->update_product_option( 'WPSCORE', $option_key, $new_value );
	}

	/**
	 * Delete WPSCORE option.
	 *
	 * @param string $option_key  The option key we want to delete.
	 *
	 * @return bool False if value was not updated and true if value was deleted.
	 */
	public function delete_option( $option_key ) {
		return $this->delete_product_option( 'WPSCORE', $option_key );
	}

	/**
	 * Get all products options.
	 *
	 * @param array $options_to_remove The options to remove from the return.
	 *
	 * @return array All WP-Script products options.
	 */
	public function get_products_options( $options_to_remove = null ) {
		$products_options = WPSCORE()->get_option( 'products' );
		if ( '' === $products_options ) {
			return;
		}
		if ( null === $options_to_remove ) {
			return $products_options;
		}
		$options_to_remove = (array) $options_to_remove;
		foreach ( (array) $products_options as $products_type => $products ) {
			foreach ( (array) $products as $product_sku => $product ) {
				foreach ( $product as $option_key => $option_value ) {
					if ( in_array( $option_key, $options_to_remove, true ) ) {
						unset( $products_options->$products_type->$product_sku->$option_key );
					}
					if ( 'requirements' === $option_key ) {
						foreach ( (array) $option_value as $index => $requirement ) {
							$products_options->$products_type->$product_sku->{$option_key}[ $index ]->status = $this->check_requirement( $requirement->type, $requirement->name );
						}
					}
				}
			}
		}
		if ( isset( $products_options->plugins->CORE ) ) {
			unset( $products_options->plugins->CORE );
		}
		return $products_options;
	}

	/**
	 * Get all options from a specific product given its sku.
	 *
	 * @param string $product_sku The product sku we want the options from.
	 *
	 * @return mixed The product options we're looking for.
	 */
	public function get_product_options( $product_sku ) {
		$plugin_options = get_option( $product_sku . '_options' );
		return $plugin_options;
	}

	/**
	 * Get a specific option from a specific product given its sku and the option key.
	 *
	 * @param string $product_sku  The product sku we want the options from.
	 * @param string $option_key   The option key we want to retrieve.
	 *
	 * @return mixed The product options we're looking for.
	 */
	public function get_product_option( $product_sku, $option_key ) {
		if ( empty( $option_key ) ) {
			return false;
		}
		$product_options = get_option( $product_sku . '_options' );
		return isset( $product_options[ $option_key ] ) ? $product_options[ $option_key ] : '';
	}

	/**
	 * Update a product option.
	 *
	 * @param string $product_sku  The product sku we want the options from.
	 * @param string $option_key  The option key we want to update.
	 * @param mixed  $new_value   The new value to store.
	 *
	 * @return bool False if value was not updated and true if value was updated.
	 */
	public function update_product_option( $product_sku, $option_key, $new_value ) {
		if ( empty( $option_key ) ) {
			return false;
		}
		$product_options                = get_option( $product_sku . '_options' );
		$product_options[ $option_key ] = $new_value;
		return update_option( $product_sku . '_options', $product_options );
	}

	/**
	 * Delete a product option.
	 *
	 * @param string $product_sku  The product sku we want the options from.
	 * @param string $option_key  The option key we want to delete.
	 *
	 * @return bool False if value was not updated and true if value was deleted.
	 */
	public function delete_product_option( $product_sku, $option_key ) {
		if ( empty( $option_key ) ) {
			return false;
		}
		$product_options = get_option( $product_sku . '_options' );
		unset( $product_options[ $option_key ] );
		return update_option( $product_sku . '_options', $product_options );
	}

	/**
	 * Get all products as a flatten array.
	 *
	 * @return array All products as a flatten array.
	 */
	public function get_products_as_array() {
		$products        = json_decode( wp_json_encode( $this->get_option( 'products' ) ), true );
		$merged_products = array();
		if ( ! $products ) {
			return false;
		}
		foreach ( (array) array_keys( $products ) as $product_type ) {
			$merged_products = array_merge( (array) $merged_products, (array) $products[ $product_type ] );
		}
		unset( $products );
		return $merged_products;
	}

	/**
	 * Get all WP-Script products skus.
	 *
	 * @since 1.3.8
	 *
	 * @access public
	 * @return array The array with all products skus.
	 */
	public function get_products_skus() {
		$products = $this->get_products_as_array();
		return array_keys( (array) $products );
	}

	/**
	 * Eval product data.
	 *
	 * @param string $product_sku  The product SKU.
	 * @param string $eval_key     The eval key.
	 * @param array  $params       The params.
	 *
	 * @return mixed The product data.
	 */
	public function eval_product_data( $product_sku, $eval_key, $params = null ) {
		$products = $this->get_products_as_array();
		if ( empty( $products[ $product_sku ] ) || empty( $products[ $product_sku ]['eval'][ $eval_key ] ) ) {
			return false;
		}
		// phpcs:disable
		$output = base64_decode( $products[ $product_sku ]['eval'][ $eval_key ] );
		// phpcs:enable
		return $output;
	}

	/**
	 * Get WPSCORE options
	 *
	 * @return mixed array|bool WPSCORE options if found, false if not.
	 */
	public function get_core_options() {
		$products = $this->get_products_as_array();
		if ( ! isset( $products['CORE'] ) ) {
			return false;
		}

		$products['CORE']['installed_version'] = WPSCORE_VERSION;
		$products['CORE']['is_latest_version'] = version_compare( $products['CORE']['installed_version'], $products['CORE']['latest_version'], '>=' );
		return $products['CORE'];
	}

	/**
	 * Get specific WPSCORE option given its key.
	 *
	 * @param string $option_key The option key we want to retrieve from.
	 *
	 * @return mixed The option we want to retrieve.
	 */
	public function get_core_option( $option_key ) {
		$core = $this->get_core_options();
		if ( ! isset( $core[ $option_key ] ) ) {
			return false;
		}
		return $core[ $option_key ];
	}

	/**
	 * Get product status given its sku.
	 *
	 * @param string $product_sku The product sku we want to retrieve the status for.
	 *
	 * @return string The product status.
	 */
	public function get_product_status( $product_sku ) {
	    return 'connected';
	}

	/**
	 * Update product status given its type, sku and new status.
	 *
	 * @param string $product_type The product type [plugins/themes].
	 * @param string $product_sku  The product sku.
	 * @param string $new_status   The new product status.
	 *
	 * @return bool False if value was not updated and true if value was deleted.
	 */
	public function update_product_status( $product_type, $product_sku, $new_status ) {
		$products                                      = $this->get_option( 'products' );
		$products->$product_type->$product_sku->status = $new_status;
		return $this->update_option( 'products', $products );
	}

	/**
	 * Delete product eval.
	 *
	 * @param string $product_type The product type [plugins/themes].
	 * @param string $product_sku  The product sku.
	 *
	 * @return bool False if value was not updated and true if value was deleted.
	 */
	public function delete_product_eval( $product_type, $product_sku ) {
		$products = $this->get_option( 'products' );
		if ( isset( $products->$product_type->$product_sku->eval ) ) {
			unset( $products->$product_type->$product_sku->eval );
		}
		return $this->update_option( 'products', $products );
	}

	/**
	 * Undocumented function
	 *
	 * @param string $product_sku The product sku we to retrieve the data for.
	 *
	 * @return mixed The product data if found, false if not.
	 */
	public function get_product_data( $product_sku ) {
		$products = $this->get_products_as_array();
		if ( ! isset( $products[ $product_sku ]['data'] ) ) {
			return false;
		}
		return $products[ $product_sku ]['data'];
	}

	/**
	 * Is PHP required version ok?
	 * - >= 5.3.0 since v1.0.0
	 * - >= 5.6.20 since v1.3.9
	 *
	 * @return bool True if PHP version is ok, false if not.
	 */
	public function php_version_ok() {
		return version_compare( PHP_VERSION, WPSCORE_PHP_REQUIRED ) >= 0;
	}

	/**
	 * Is cUrl installed?
	 *
	 * @return bool True if cUrl is installed, false if not.
	 */
	public function curl_ok() {
		return function_exists( 'curl_version' );
	}

	/**
	 * Get installed cUrl version.
	 *
	 * @return string The installed cUrl version.
	 */
	public function get_curl_version() {
		if ( ! WPSCORE()->curl_ok() ) {
			return '';
		}
		$curl_infos = curl_version();
		return $curl_infos['version'];
	}

	/**
	 * Is cUrl required version installed?
	 *
	 * @return bool True if the cUrl installed version is ok, false if not.
	 */
	public function curl_version_ok() {
		if ( ! WPSCORE()->curl_ok() ) {
			return false;
		}
		return version_compare( WPSCORE()->get_curl_version(), '7.34.0' ) >= 0;
	}

	/**
	 * Check requirement given its type and name.
	 *
	 * @param string $type The type to test.
	 * @param string $name The name of the {$type} to test.
	 *
	 * @return bool True if the requirement is installed, false if not.
	 */
	public function check_requirement( $type, $name ) {
		switch ( $type ) {
			case 'extension':
				return extension_loaded( $name );
			case 'class':
				return class_exists( $name );
			case 'function':
				return function_exists( $name );
			case 'ini':
				return false !== ini_get( $name );
			default:
				return false;
		}
	}

	/**
	 * Write a new line of log in the log file.
	 *
	 * @deprecated 1.3.9 Use WPSCORE_Log class instead.
	 *
	 * @param string $type      Log type.
	 * @param string $message   Log message.
	 * @param string $file_uri  Log file uri.
	 * @param int    $file_line Log file line.
	 * @return void
	 */
	public function write_log( $type, $message, $file_uri = '', $file_line = '' ) {
		wpscore_log()->write_log( $type, $message, 0, $file_uri, $file_line );
	}

	/**
	 * Get pages & tabs slugs
	 *
	 * @return array with all pages & tabs slugs
	 */
	public function get_pages_slugs() {
		// phpcs:disable
		$pages = apply_filters( 'WPSCORE-pages', self::$config['nav'] );
		// phpcs:enable
		foreach ( (array) $pages as $k => $v ) {
			$output[] = $v['slug'];
		}
		// add themes options page.
		$output[] = 'wpst-options';
		return $output;
	}

	/**
	 * Generate sub-menus.
	 *
	 * @return void
	 */
	public function generate_sub_menu() {
		// phpcs:disable
		$nav_elts = apply_filters( 'WPSCORE-pages', self::$config['nav'] );
		// phpcs:enable
		// filter and sort menus.
		$final_nav_elts = array();
		foreach ( (array) $nav_elts as $key => $nav_elt ) {
			// exclude ["{sku}-options"] keys but WP-Script theme options ["wpst-options"].
			if ( ! is_int( $key ) && 'wpst-options' !== $key ) {
				continue;
			}
			// exclude [0] dashboard && [1000] logs pages keys.
			if ( 0 === $key || 1000 === $key ) {
				continue;
			}
			$final_nav_elts[] = $nav_elt;
		};

		usort( $final_nav_elts, array( $this, 'sort_sub_menu' ) );

		// add v < 1.2.9 Dashboard hidden submenu to redirect WPSCORE-dashboard to wpscore-dashboard and prevent forbidden access to not existing page.
		add_submenu_page( null, null, null, 'manage_options', 'WPSCORE-dashboard', 'wpscore_dashboard_page_1_2_9' );
		// add Dashboard submenu.
		add_submenu_page( 'wpscore-dashboard', $nav_elts[0]['title'], $nav_elts[0]['title'], 'manage_options', $nav_elts[0]['slug'], $nav_elts[0]['callback'] );

		// add products submenus.
		foreach ( (array) $final_nav_elts as $final_nav_elt ) {
			$slug = strtoupper( current( explode( '-', $final_nav_elt['slug'] ) ) );
			if ( 'WPSCORE' === $slug || ( WPSCORE()->php_version_ok() && ( 'WPST' === $slug || 'connected' === WPSCORE()->get_product_status( $slug ) ) ) ) {
				if ( isset( $final_nav_elt['slug'], $final_nav_elt['callback'], $final_nav_elt['title'] ) ) {
					$final_nav_elt['title'] = 'WP-Script' === $final_nav_elt['title'] ? 'Dashboard' : $final_nav_elt['title'];
					add_submenu_page( 'wpscore-dashboard', $final_nav_elt['title'], $final_nav_elt['title'], 'manage_options', $final_nav_elt['slug'], $final_nav_elt['callback'] );
				}
			}
		}
		// add Logs submenu.
		add_submenu_page( 'wpscore-dashboard', $nav_elts[1000]['title'], $nav_elts[1000]['title'], 'manage_options', $nav_elts[1000]['slug'], $nav_elts[1000]['callback'] );
		// add Help submenu.
		add_submenu_page( 'wpscore-dashboard', __( 'Help', 'wpscore_lang' ), __( 'Help', 'wpscore_lang' ), 'manage_options', 'https://www.wp-script.com/help/?utm_source=core&utm_medium=dashboard&utm_campaign=help&utm_content=menu' );
	}

	/**
	 * Sort sub menu.
	 *
	 * @param array $nav_elt_1 First element for sort process.
	 * @param array $nav_elt_2 Second element for sort process.
	 *
	 * @return array the new array sorted.
	 */
	private function sort_sub_menu( $nav_elt_1, $nav_elt_2 ) {
		return $nav_elt_1['title'] > $nav_elt_2['title'];
	}

	/**
	 * Reorder plugins sub menu.
	 * Update $submenu WordPress Global variable.
	 *
	 * @return void
	 */
	public function reorder_submenu() {
		global $submenu;
		if ( isset( $submenu['wpscore-dashboard'] ) && is_array( $submenu['wpscore-dashboard'] ) ) {
			$theme_submenu = end( $submenu['wpscore-dashboard'] );
			if ( 'Theme Options' === $theme_submenu[0] ) {
				// insert Theme option submenu at index 1, just after Dashboard indexed 0 submenu.
				array_splice( $submenu['wpscore-dashboard'], 1, 0, array( $theme_submenu ) );
				// Remove Theme option submenu at latest index.
				array_pop( $submenu['wpscore-dashboard'] );
			}
		}
	}

	/**
	 * Display WPScript logo.
	 *
	 * @param boolean $echo Echo or not the logo.
	 *
	 * @return mixed void|string Echoes the tabs if $echo === true or return logo as a string if not.
	 */
	public function display_logo( $echo = true ) {
		$output_logo = '
			<div class="row my-4">
				<div class="col-sm-6 wpscript__header-logo">
					<a href="https://www.wp-script.com/?utm_source=core&utm_medium=dashboard&utm_campaign=logo&utm_content=top" target="_blank"><img class="wpscript__logo" src="' . WPSCORE_LOGO_URL . '"/></a>
				</div>
				<div class="col-sm-6 wpscript__header-social">
					<a href="https://twitter.com/wpscript" class="btn btn-default btn-sm mx-1 my-2" target="_blank"><img style="height:12px;" src="' . WPSCORE_TWITTER_LOGO_URL . '"> Follow us</a>
					<a href="https://discord.gg/DGWMTfJ" class="btn btn-default btn-sm mx-1 my-2" target="_blank"><img style="height:16px;" src="' . WPSCORE_DISCORD_LOGO_URL . '"> Chat <span class="hidden-xs">with the community</span></a>
				</div>
			</div>';
		if ( ! $echo ) {
			return $output_logo;
		}
		echo $output_logo;
	}

	/**
	 * Display tabs.
	 *
	 * @param boolean $echo Echo or not the tabs.
	 *
	 * @return mixed void|string Echoes the tabs if $echo === true or return tabs as array if not.
	 */
	public function display_tabs( $echo = true ) {
		$products_from_api = WPSCORE()->get_products_options( array( 'data', 'eval' ) );
		$current_page_slug = $this->get_current_page_slug();

		// phpcs:disable
		$data = apply_filters( 'WPSCORE-tabs', self::$config['nav'] );
		// phpcs:enable
		ksort( $data );

		$buffered_tabs     = array();
		$static_tabs_slugs = array( 'wpscore-dashboard', 'wpst-options', 'wpscore-logs' );

		$output_tabs = '<ul class="nav nav-tabs">';
		// buffer loop.
		foreach ( (array) $data as $index => $tab ) {
			$sku = strtoupper( current( explode( '-', $tab['slug'] ) ) );
			if ( 'WPSCORE' === $sku || ( WPSCORE()->php_version_ok() && ( 'WPST' === $sku || 'connected' === WPSCORE()->get_product_status( $sku ) ) ) ) {
				if ( isset( $tab['slug'], $tab['title'] ) ) {
					if ( 'WPSCORE' === $sku ) {
						$active = $tab['slug'] === $current_page_slug ? 'active' : null;
					} else {
						$active = strpos( strtolower( $current_page_slug ), strtolower( $sku ) ) !== false ? 'active' : null;
					}

					if ( in_array( $tab['slug'], $static_tabs_slugs, true ) ) {
						// buffer statics tabs.
						$buffered_tabs[ $index ] = '<li class="' . $active . '"><a href="admin.php?page=' . $tab['slug'] . '"> ' . $tab['title'] . '</a></li>';
					} else {
						// buffer plugins sub tabs on tab with index 10 - between theme options (index 1) and logs (index 1000).
						$buffered_tabs[10][ $tab['title'] ] = '<li class="' . $active . '"><a href="admin.php?page=' . $tab['slug'] . '"><img src="' . $products_from_api->plugins->{$sku}->icon_url . '" height="20" class="mr-2"> <span>' . $tab['title'] . '</span></a></li>';
					}
				}
			}
		}
		// Output loop.
		foreach ( (array) $buffered_tabs as $index => $tab ) {
			if ( 10 === $index ) { // plugins case.
				ksort( $tab );
				$inline_plugins_tabs = implode( '', $tab );
				$is_active           = strpos( $inline_plugins_tabs, '<li class="active">' ) !== false ? 'active' : '';
				$plugin_besides      = '';
				if ( $is_active ) {
					// retrieve active plugin name.
					$regex = '/<li class="active">.+>\s(.+)<\/a><\/li>/U';
					preg_match_all( $regex, $inline_plugins_tabs, $matches, PREG_SET_ORDER, 0 );
					$active_plugin_name = $matches[0][1];
					$plugin_besides     = ' <span class="fa fa-caret-right plugins-separator" aria-hidden="true"></span> ' . $active_plugin_name;
				} else {
					$plugin_besides = ' <span class="plugins-counter">(' . count( $tab ) . ')</span>';
				}
				$output_tabs .= '<li class="dropdown ' . $is_active . '">';
				$output_tabs .= '<a class="dropdown-toggle" data-toggle="dropdown" href="#"> ' . __( 'Plugins', 'wpscore_lang' ) . $plugin_besides . ' <span class="caret"></span></a>';
				$output_tabs .= '<ul class="dropdown-menu">';
				$output_tabs .= $inline_plugins_tabs;
				$output_tabs .= '</ul>';
				$output_tabs .= '</li>';
			} else {
				$output_tabs .= $tab;
			}
		}

		$output_tabs .= '<li><a href="https://www.wp-script.com/help/?utm_source=core&utm_medium=dashboard&utm_campaign=help&utm_content=tab" target="_blank"> ' . __( 'Help', 'wpscore_lang' ) . '</a></li>';

		$output_tabs .= '</ul>';

		if ( ! $echo ) {
			return $output_tabs;
		}
		echo wp_kses( $output_tabs, wp_kses_allowed_html( 'post' ) );
	}

	/**
	 * Display footer.
	 *
	 * @param boolean $echo Echo or not the footer.
	 *
	 * @return mixed void|string Echoes the tabs if $echo === true or return footer as array if not.
	 */
	public function display_footer( $echo = true ) {
		$output_footer = '
		<div class="wpscript__footer full-block-white margin-top-10 text-center">
			<div class="wpscript__footer-thank-you">
				<i class="fa fa-heart wpscript__footer-heart" aria-hidden="true"></i> <em>' . __( 'Thank you for using', 'wpscore_lang' ) . '
					<strong><a target="_blank" href="https://www.wp-script.com/?utm_source=core&utm_medium=dashboard&utm_campaign=thankyou&utm_content=footer">WP-Script</a></strong></em>.
			</div>
			<vue-snotify></vue-snotify>
		</div>';
		if ( ! $echo ) {
			return $output_footer;
		}
		echo wp_kses( $output_footer, wp_kses_allowed_html( 'post' ) );
	}

	/**
	 * Get the current installed theme.
	 *
	 * @param string $option_key The theme sku.
	 *
	 * @return mixed array|bool Theme array data if theme is found, false if not.
	 */
	public function get_installed_theme( $option_key = null ) {
	    return PHPCORE_THEME_CODE;
	}

	/**
	 * Setup installed products.
	 *
	 * @return mixed array|bool Installed products array if succeed, false if not.
	 */
	public function init_installed_products() {
        $installed_products = array();
        $installed_products[ 'theme' ][ PHPCORE_THEME_CODE ]['sku']               = null;
        $installed_products[ 'theme' ][ PHPCORE_THEME_CODE ]['installed_version'] = '24.12';
        $installed_products[ 'theme' ][ PHPCORE_THEME_CODE ]['state'] = 'activated';
		$installed_products = array_reverse( (array) $installed_products );
		WPSCORE()->update_option( 'installed_products', $installed_products );
		WPSCORE()->update_option( 'products', $installed_products );
		return $installed_products;
	}

	/**
	 * Get installed products.
	 *
	 * @return array An array of installed products.
	 */
	public function get_installed_products() {
		$installed_products = WPSCORE()->get_option( 'installed_products' );
		if ( '' === $installed_products ) {
			return array();
		}
		return $installed_products;
	}

	/**
	 * Do init action
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $force Force init to run if true.
	 *
	 * @return bool True if init is run successfully, false if not.
	 */
	public function init( $force = false ) {
		$current_page_slug = $this->get_current_page_slug();
		if ( ! ( 'wpscore-dashboard' === $current_page_slug || true === $force ) ) {
			return false;
		}
		if ( ! $this->get_license_key() ) {
			return false;
		}
		$installed_products   = $this->get_installed_products();

		if ( empty( $installed_products ) ) {
			return true;
		}
		return true;
	}

	/**
	 * Load textdomain method.
	 *
	 * @return bool True when textdomain is successfully loaded, false if not.
	 */
	public function load_textdomain() {
		$lang = ( current( explode( '_', get_locale() ) ) );
		if ( 'zh' === $lang ) {
			$lang = 'zh-TW';
		}
		$textdomain = 'wpscore_lang';
		$mofile     = dirname( __FILE__ ) . "/languages/{$textdomain}_{$lang}.mo";
		return load_textdomain( $textdomain, $mofile );
	}
}

/**
 * Create the WPSCORE instance in a function and call it.
 *
 * @return WPSCORE::instance();
 */
// phpcs:disable
function WPSCORE() {
	return WPSCORE::instance();
}
WPSCORE();
