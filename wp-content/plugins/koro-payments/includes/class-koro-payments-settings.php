<?php
/**
 * Payment settings stored in options (encrypted secrets).
 *
 * @package Koro_Payments
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage gateway configuration.
 */
final class Koro_Payments_Settings {

	public const OPTION_KEY = 'koro_payments_settings';

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function defaults(): array {
		return array(
			'mode'            => 'sandbox',
			'gateway'         => 'koro_stripe',
			'public_key'      => '',
			'secret_key'      => '',
			'webhook_secret'  => '',
			'currency'        => 'USD',
			'enabled'         => 0,
		);
	}

	/**
	 * Get merged settings with decrypted secrets for runtime use.
	 *
	 * @return array<string, mixed>
	 */
	public static function get(): array {
		$stored = get_option( self::OPTION_KEY, array() );
		$settings = wp_parse_args( is_array( $stored ) ? $stored : array(), self::defaults() );

		foreach ( array( 'secret_key', 'webhook_secret' ) as $secret_field ) {
			if ( ! empty( $settings[ $secret_field ] ) ) {
				$settings[ $secret_field ] = Koro_Payments_Crypto::decrypt( (string) $settings[ $secret_field ] );
			}
		}

		return $settings;
	}

	/**
	 * Get settings for admin display (secrets masked).
	 *
	 * @return array<string, mixed>
	 */
	public static function get_for_admin(): array {
		$settings = self::get();

		foreach ( array( 'secret_key', 'webhook_secret' ) as $secret_field ) {
			if ( ! empty( $settings[ $secret_field ] ) ) {
				$settings[ $secret_field ] = self::mask_secret( (string) $settings[ $secret_field ] );
			}
		}

		return $settings;
	}

	/**
	 * Persist settings with encryption for secrets.
	 *
	 * @param array<string, mixed> $input Raw form input.
	 */
	public static function save( array $input ): void {
		$current  = get_option( self::OPTION_KEY, array() );
		$current  = is_array( $current ) ? $current : array();
		$settings = wp_parse_args( $current, self::defaults() );

		$settings['mode']    = in_array( $input['mode'] ?? '', array( 'sandbox', 'live' ), true ) ? $input['mode'] : 'sandbox';
		$settings['gateway'] = sanitize_key( (string) ( $input['gateway'] ?? 'koro_stripe' ) );
		$settings['currency'] = sanitize_text_field( (string) ( $input['currency'] ?? 'USD' ) );
		$settings['enabled'] = ! empty( $input['enabled'] ) ? 1 : 0;
		$settings['public_key'] = sanitize_text_field( (string) ( $input['public_key'] ?? '' ) );

		foreach ( array( 'secret_key', 'webhook_secret' ) as $secret_field ) {
			$value = trim( (string) ( $input[ $secret_field ] ?? '' ) );

			if ( '' === $value || self::is_masked( $value ) ) {
				continue;
			}

			$settings[ $secret_field ] = Koro_Payments_Crypto::encrypt( $value );
		}

		update_option( self::OPTION_KEY, $settings, false );
	}

	/**
	 * Check if payments are enabled and configured.
	 */
	public static function is_ready(): bool {
		$settings = self::get();
		return ! empty( $settings['enabled'] ) && ! empty( $settings['public_key'] ) && ! empty( $settings['secret_key'] );
	}

	/**
	 * Mask secret for display.
	 */
	private static function mask_secret( string $secret ): string {
		$length = strlen( $secret );
		if ( $length <= 8 ) {
			return str_repeat( '*', $length );
		}

		return substr( $secret, 0, 4 ) . str_repeat( '*', max( 4, $length - 8 ) ) . substr( $secret, -4 );
	}

	/**
	 * Detect masked placeholder submitted from admin form.
	 */
	private static function is_masked( string $value ): bool {
		return str_contains( $value, '****' );
	}
}
