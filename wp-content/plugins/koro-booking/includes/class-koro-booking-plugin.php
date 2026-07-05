<?php
/**
 * Plugin bootstrap.
 *
 * @package Koro_Booking
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main booking plugin.
 */
final class Koro_Booking_Plugin {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( KORO_BOOKING_FILE, array( $this, 'activate' ) );

		add_action( 'init', array( Koro_Booking_Post_Types::class, 'register' ) );
		add_action( 'init', array( Koro_Booking_Post_Types::class, 'register_meta' ) );
		add_action( 'init', array( Koro_Booking_Orders::class, 'register_post_type' ) );
		add_action( 'init', array( Koro_Booking_Shortcodes::class, 'register' ) );

		add_action( 'add_meta_boxes', array( Koro_Booking_Post_Types::class, 'add_meta_boxes' ) );
		add_action( 'save_post_koro_service', array( Koro_Booking_Post_Types::class, 'save_meta' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'template_redirect', array( $this, 'handle_add_to_cart' ) );
		add_action( 'init', array( $this, 'start_session' ), 1 );
	}

	public function activate(): void {
		Koro_Booking_Post_Types::register();
		Koro_Booking_Orders::register_post_type();
		flush_rewrite_rules();

		$this->create_pages();
		$this->seed_demo_services();
	}

	public function start_session(): void {
		Koro_Booking_Cart::maybe_start_session();
	}

	public function enqueue_assets(): void {
		wp_enqueue_style(
			'koro-booking',
			KORO_BOOKING_URL . 'assets/css/booking.css',
			array(),
			KORO_BOOKING_VERSION
		);

		wp_enqueue_script(
			'koro-booking',
			KORO_BOOKING_URL . 'assets/js/booking.js',
			array(),
			KORO_BOOKING_VERSION,
			true
		);
	}

	public function handle_add_to_cart(): void {
		if ( empty( $_POST['koro_add_to_cart'] ) ) {
			return;
		}

		if ( ! isset( $_POST['koro_add_to_cart_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['koro_add_to_cart_nonce'] ) ), 'koro_add_to_cart' ) ) {
			return;
		}

		$service_id = (int) ( $_POST['service_id'] ?? 0 );
		$quantity   = (int) ( $_POST['quantity'] ?? 1 );

		if ( Koro_Booking_Cart::add( $service_id, $quantity ) ) {
			wp_safe_redirect( add_query_arg( 'added', '1', koro_booking_cart_url() ) );
			exit;
		}
	}

	/**
	 * Create cart and checkout pages on activation.
	 */
	private function create_pages(): void {
		$pages = array(
			'cart'     => array(
				'title'   => 'Cart',
				'content' => '[koro_cart]',
				'option'  => 'koro_booking_cart_page_id',
			),
			'checkout' => array(
				'title'   => 'Checkout',
				'content' => '[koro_checkout]',
				'option'  => 'koro_booking_checkout_page_id',
			),
		);

		foreach ( $pages as $page ) {
			$existing = (int) get_option( $page['option'], 0 );
			if ( $existing > 0 && get_post( $existing ) ) {
				continue;
			}

			$page_id = wp_insert_post(
				array(
					'post_title'   => $page['title'],
					'post_content' => $page['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);

			if ( ! is_wp_error( $page_id ) ) {
				update_option( $page['option'], (int) $page_id );
			}
		}
	}

	/**
	 * Seed demo services for showcase installs.
	 */
	private function seed_demo_services(): void {
		if ( get_option( 'koro_booking_demo_seeded' ) ) {
			return;
		}

		$services = array(
			array(
				'title'    => 'Strategy Consultation',
				'excerpt'  => 'A focused 60-minute session to map goals, constraints, and next steps.',
				'content'  => 'Work directly with our team to clarify priorities and leave with an actionable plan.',
				'price'    => 199.00,
				'duration' => 60,
			),
			array(
				'title'    => 'Design Review',
				'excerpt'  => 'Expert feedback on UX flows, visual hierarchy, and conversion paths.',
				'content'  => 'Bring your mockups or live site — we audit structure, accessibility, and polish.',
				'price'    => 149.00,
				'duration' => 45,
			),
			array(
				'title'    => 'Technical Audit',
				'excerpt'  => 'Architecture, performance, and security review for Laravel or WordPress stacks.',
				'content'  => 'Receive a prioritized report with quick wins and longer-term recommendations.',
				'price'    => 249.00,
				'duration' => 90,
			),
			array(
				'title'    => 'Launch Support',
				'excerpt'  => 'Hands-on help shipping your booking site, store, or client portal.',
				'content'  => 'Deployment checklist, DNS, SSL, payment sandbox verification, and smoke testing.',
				'price'    => 179.00,
				'duration' => 60,
			),
		);

		foreach ( $services as $service ) {
			$existing = get_page_by_title( $service['title'], OBJECT, 'koro_service' );
			if ( $existing instanceof WP_Post ) {
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_title'   => $service['title'],
					'post_excerpt' => $service['excerpt'],
					'post_content' => $service['content'],
					'post_status'  => 'publish',
					'post_type'    => 'koro_service',
				)
			);

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			update_post_meta( (int) $post_id, '_koro_price', $service['price'] );
			update_post_meta( (int) $post_id, '_koro_duration', $service['duration'] );
		}

		update_option( 'koro_booking_demo_seeded', '1' );
	}
}
