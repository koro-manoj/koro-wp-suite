<?php
/**
 * Transaction log (custom table).
 *
 * @package Koro_Payments
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persist payment transactions.
 */
final class Koro_Payments_Transactions {

	public const TABLE = 'koro_payment_transactions';

	/**
	 * Create custom table on activation.
	 */
	public static function install(): void {
		global $wpdb;

		$table   = $wpdb->prefix . self::TABLE;
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			transaction_id varchar(64) NOT NULL,
			amount decimal(12,2) NOT NULL DEFAULT 0.00,
			currency char(3) NOT NULL DEFAULT 'USD',
			status varchar(20) NOT NULL DEFAULT 'pending',
			mode varchar(20) NOT NULL DEFAULT 'sandbox',
			reference varchar(64) NOT NULL DEFAULT '',
			meta longtext NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY transaction_id (transaction_id),
			KEY reference (reference),
			KEY status (status)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Insert transaction row.
	 *
	 * @param array<string, mixed> $data Transaction data.
	 */
	public static function record( array $data ): int {
		global $wpdb;

		$table = $wpdb->prefix . self::TABLE;

		$wpdb->insert(
			$table,
			array(
				'transaction_id' => sanitize_text_field( (string) ( $data['transaction_id'] ?? '' ) ),
				'amount'         => (float) ( $data['amount'] ?? 0 ),
				'currency'       => sanitize_text_field( (string) ( $data['currency'] ?? 'USD' ) ),
				'status'         => sanitize_key( (string) ( $data['status'] ?? 'pending' ) ),
				'mode'           => sanitize_key( (string) ( $data['mode'] ?? 'sandbox' ) ),
				'reference'      => sanitize_text_field( (string) ( $data['reference'] ?? '' ) ),
				'meta'           => wp_json_encode( $data['meta'] ?? array() ),
				'created_at'     => current_time( 'mysql', true ),
			),
			array( '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	/**
	 * Fetch recent transactions.
	 *
	 * @return array<int, object>
	 */
	public static function recent( int $limit = 10 ): array {
		global $wpdb;

		$table = $wpdb->prefix . self::TABLE;
		$limit = max( 1, min( 100, $limit ) );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT {$limit}" );

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * Count transactions by status.
	 */
	public static function count_by_status( string $status ): int {
		global $wpdb;

		$table = $wpdb->prefix . self::TABLE;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", $status ) );
	}
}
