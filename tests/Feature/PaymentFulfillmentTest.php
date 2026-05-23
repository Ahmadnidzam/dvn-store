<?php

namespace Tests\Feature;

use App\Http\Controllers\Payment\MidtransCallbackController;
use App\Models\Pengguna;
use App\Models\Platform;
use App\Models\Transaksi;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use ReflectionMethod;
use Tests\TestCase;

class PaymentFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_fulfillment_only_credits_wallet_once(): void
    {
        $developer = Pengguna::create([
            'name' => 'Dev',
            'email' => 'dev@example.test',
            'password' => Hash::make('password'),
            'role' => 'developer',
            'status' => 'active',
        ]);

        $buyer = Pengguna::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.test',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $platform = Platform::create([
            'dev_id' => $developer->id,
            'category' => 'game',
            'nama_platform' => 'Idempotent Quest',
            'slug' => 'idempotent-quest',
            'genre' => 'Action',
            'harga' => 1000,
            'deskripsi' => str_repeat('A', 60),
            'scan_status' => 'clean',
            'is_published' => true,
        ]);

        $wallet = Wallet::create(['dev_id' => $developer->id, 'saldo' => 0]);

        $transaksi = Transaksi::create([
            'user_id' => $buyer->id,
            'platform_id' => $platform->id,
            'tipe' => 'purchase',
            'amount' => 1000,
            'platform_fee' => 100,
            'net_amount' => 900,
            'kode_transaksi' => 'PUR-TEST-1',
            'midtrans_order_id' => 'DVN-TEST-1',
            'status' => 'paid',
        ]);

        $controller = new MidtransCallbackController();
        $fulfill = new ReflectionMethod($controller, 'fulfill');
        $fulfill->setAccessible(true);

        $fulfill->invoke($controller, $transaksi);
        $fulfill->invoke($controller, $transaksi);

        $this->assertSame(900, $wallet->fresh()->saldo);
        $this->assertSame(1, WalletTransaction::where('transaksi_id', $transaksi->id)->where('tipe', 'credit')->count());
    }
}
