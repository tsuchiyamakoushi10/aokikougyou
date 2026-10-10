<?php
/**
 * Aoki Industry theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AOKI_VERSION', '1.0.0' );
define( 'AOKI_DIR', get_template_directory() );
define( 'AOKI_URI', get_template_directory_uri() );

require AOKI_DIR . '/inc/data.php';
require AOKI_DIR . '/inc/setup.php';
require AOKI_DIR . '/inc/template-tags.php';
require AOKI_DIR . '/inc/works.php';
require AOKI_DIR . '/inc/forms.php';
require AOKI_DIR . '/inc/redirects.php';
require AOKI_DIR . '/inc/seo.php';
require AOKI_DIR . '/inc/seed.php';
