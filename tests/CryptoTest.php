<?php

declare(strict_types=1);

namespace Koro\WpSuite\Tests;

use PHPUnit\Framework\TestCase;

final class CryptoTest extends TestCase
{
    public function test_encrypt_decrypt_round_trip(): void
    {
        $plaintext = 'sk_test_demo_secret_key_12345';

        $encrypted = \Koro_Payments_Crypto::encrypt($plaintext);

        $this->assertNotSame($plaintext, $encrypted);
        $this->assertSame($plaintext, \Koro_Payments_Crypto::decrypt($encrypted));
    }

    public function test_empty_string_stays_empty(): void
    {
        $this->assertSame('', \Koro_Payments_Crypto::encrypt(''));
        $this->assertSame('', \Koro_Payments_Crypto::decrypt(''));
    }
}
