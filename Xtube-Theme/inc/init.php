<?php if( ! function_exists('is_plugin_active') ) include_once( ABSPATH . 'wp-admin/includes/plugin.php' );?>

<?php if( ! is_plugin_active('wp-script-core/wp-script-core.php') ): ?>

    <a href="https://www.wp-script.com" title="Adult WordPress Themes and Plugins" rel="nofollow noopener" target="_blank"><img src="https://www.wp-script.com/wp-content/themes/wps/img/logo.svg" width="180"></a>

	<p style="text-align:center;"><?php printf(__('PopTube Theme needs %s to be installed and activated.', 'wpst'), '<a href="https://www.wp-script.com/getting-started/" target="_blank">WP-Script Core Plugin</a>'); ?></p>

	<style type="text/css">
		body{
			background-color: #fff;
			text-align: center;
			color: #40515d;
			padding-top: 50px;
			font-family: 'arial';
			font-size: 16px;
		}
		a {
			color: #cf48cf;
		}
        p {
            font-size: 20px;
            margin-top: 60px;
        }
	</style>

<?php die(); endif;?>

<?php if( WPSCORE()->get_product_status( 'VTT' ) != 'connected' ) : ?>

    <a href="https://www.wp-script.com" title="Adult WordPress Themes and Plugins" rel="nofollow noopener" target="_blank"><img src="https://www.wp-script.com/wp-content/themes/wps/img/logo.svg" width="180"></a>

	<p><?php printf(__('Please purchase a VTube plan: %s', 'wpst'), '<a href="https://www.wp-script.com/adult-wordpress-themes/vtube/" title="VTube" target="_blank">VTube Theme</a>'); ?></p>

	<style type="text/css">
		body{
			background-color: #fff;
			text-align: center;
			color: #40515d;
			padding-top: 50px;
			font-family: 'arial';
			font-size: 16px;
		}
		a {
			color: #cf48cf;
		}
        p {
            font-size: 20px;
            margin-top: 60px;
        }
	</style>
<?php die(); endif;?>
