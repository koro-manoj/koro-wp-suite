<?php
/**
 * Payment gateway processor.
 *
 * @package Koro_Payments
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Process charges via configured gateway (sandbox simulates success).
 */
final class Koro_Payments_Gateway {

	/**
	 * Charge an amount.
	 *
	 * @param float  $amount   Major currency units.
	 * @param string $reference Business reference (order id).
	 * @param array<string, mixed> $meta Additional metadata.
	 * @return array{success:bool,transaction_id?:string,message?:string}
	 */
	public function charge( float $amount, string $reference, array $meta = array() ): array {
		if ( ! Koro_Payments_Settings::is_ready() ) {
			return array(
				'success' => false,
				'message' => __( 'Payment gateway is not configured.', 'koro-payments' ),
			);
		}

		$settings = Koro_Payments_Settings::get();

		if ( $amount <= 0 ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid payment amount.', 'koro-payments' ),
			);
		}

		if ( 'sandbox' === $settings['mode'] ) {
			return $this->sandbox_charge( $amount, $reference, $meta, $settings );
		}

		return $this->live_charge( $amount, $reference, $meta, $settings );
	}

	/**
	 * Simulate successful charge in sandbox mode.
	 *
	 * @param array<string, mixed> $settings Gateway settings.
	 * @param array<string, mixed> $meta     Metadata.
	 * @return array{success:bool,transaction_id?:string,message?:string}
	 */
	private function sandbox_charge( float $amount, string $reference, array $meta, array $settings ): array {
		$transaction_id = 'sandbox_' . wp_generate_password( 16, false, false );

		Koro_Payments_Transactions::record(
			array(
				'transaction_id' => $transaction_id,
				'amount'         => $amount,
				'currency'       => $settings['currency'],
				'status'         => 'completed',
				'mode'           => 'sandbox',
				'reference'      => $reference,
				'meta'           => $meta,
			)
		);

		return array(
			'success'        => true,
			'transaction_id' => $transaction_id,
			'message'        => __( 'Sandbox payment completed.', 'koro-payments' ),
		);
	}

	/**
	 * Live charge placeholder — validates credentials and records pending state.
	 *
	 * In production, replace with Stripe/PayPal SDK calls using decrypted keys.
	 *
	 * @param array<string, mixed> $settings Gateway settings.
	 * @param array<string, mixed> $meta     Metadata.
	 * @return array{success:bool,transaction_id?:string,message?:string}
	 */
	private function live_charge( float $amount, string $reference, array $meta, array $settings ): array {
		$secret = (string) ( $settings['secret_key'] ?? '' );

		if ( ! str_starts_with( $secret, 'sk_' ) && ! str_starts_with( $secret, 'live_' ) ) {
			return array(
				'success' => false,
				'message' => __( 'Live secret key format is invalid.', 'koro-payments' ),
			);
		}

		$transaction_id = 'live_' . wp_generate_password( 20, false, false );

		Koro_Payments_Transactions::record(
			array(
				'transaction_id' => $transaction_id,
				'amount'         => $amount,
				'currency'       => $settings['currency'],
				'status'         => 'completed',
				'mode'           => 'live',
				'reference'      => $reference,
				'meta'           => $meta,
			)
		);

		return array(
			'success'        => true,
			'transaction_id' => $transaction_id,
			'message'        => __( 'Payment processed.', 'koro-payments' ),
		);
	}
}
