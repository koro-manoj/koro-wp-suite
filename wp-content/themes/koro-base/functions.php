<?php
/**
 * Theme bootstrap.
 *
 * @package Koro_Base
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KORO_BASE_VERSION', '1.0.0' );
define( 'KORO_BASE_DIR', get_template_directory() );
define( 'KORO_BASE_URI', get_template_directory_uri() );

require_once KORO_BASE_DIR . '/inc/setup.php';
require_once KORO_BASE_DIR . '/inc/enqueue.php';
require_once KORO_BASE_DIR . '/inc/template-tags.php';
require_once KORO_BASE_DIR . '/inc/customizer.php';
