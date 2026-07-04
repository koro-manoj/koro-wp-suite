<?php
/**
 * AES-256-GCM encryption for stored credentials.
 *
 * @package Koro_Payments
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Encrypt and decrypt sensitive values using WordPress salts.
 */
final class Koro_Payments_Crypto {

	/**
	 * Derive encryption key from WP salts.
	 */
	private static function key(): string {
		$material = AUTH_KEY . SECURE_AUTH_KEY . LOGGED_IN_KEY . NONCE_KEY;
		return hash( 'sha256', $material, true );
	}

	/**
	 * Encrypt plaintext.
	 *
	 * @param string $plaintext Value to encrypt.
	 * @return string Base64 payload: iv.tag.ciphertext
	 */
	public static function encrypt( string $plaintext ): string {
		if ( '' === $plaintext ) {
			return '';
		}

		$iv         = random_bytes( 12 );
		$tag        = '';
		$ciphertext = openssl_encrypt(
			$plaintext,
			'aes-256-gcm',
			self::key(),
			OPENSSL_RAW_DATA,
			$iv,
			$tag,
			'',
			16
		);

		if ( false === $ciphertext ) {
			return '';
		}

		return base64_encode( $iv . $tag . $ciphertext );
	}

	/**
	 * Decrypt payload.
	 *
	 * @param string $payload Encrypted payload.
	 */
	public static function decrypt( string $payload ): string {
		if ( '' === $payload ) {
			return '';
		}

		$raw = base64_decode( $payload, true );
		if ( false === $raw || strlen( $raw ) < 29 ) {
			return '';
		}

		$iv         = substr( $raw, 0, 12 );
		$tag        = substr( $raw, 12, 16 );
		$ciphertext = substr( $raw, 28 );

		$plaintext = openssl_decrypt(
			$ciphertext,
			'aes-256-gcm',
			self::key(),
			OPENSSL_RAW_DATA,
			$iv,
			$tag
		);

		return false === $plaintext ? '' : $plaintext;
	}
}
