<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        BankAccount::insert([
            // ====== Banks ======
            ['name' => 'البنك الأهلي السعودي', 'type' => 'bank', 'image' => 'banks/snb.png'],
            ['name' => 'مصرف الراجحي', 'type' => 'bank', 'image' => 'banks/alrajhi.png'],
            ['name' => 'بنك الرياض', 'type' => 'bank', 'image' => 'banks/riyad.png'],
            ['name' => 'البنك السعودي الفرنسي', 'type' => 'bank', 'image' => 'banks/fransi.png'],
            ['name' => 'ساب', 'type' => 'bank', 'image' => 'banks/sabb.png'],
            ['name' => 'البنك العربي الوطني', 'type' => 'bank', 'image' => 'banks/anb.png'],
            ['name' => 'بنك الجزيرة', 'type' => 'bank', 'image' => 'banks/aljazira.png'],
            ['name' => 'بنك البلاد', 'type' => 'bank', 'image' => 'banks/albilad.png'],
            ['name' => 'بنك الإنماء', 'type' => 'bank', 'image' => 'banks/alinma.png'],
            ['name' => 'البنك الأول', 'type' => 'bank', 'image' => 'banks/saab.png'],

            // ====== Wallets ======
            ['name' => 'STC Pay', 'type' => 'wallet', 'image' => 'wallets/stc_pay.png'],
            ['name' => 'UrPay', 'type' => 'wallet', 'image' => 'wallets/urpay.png'],
            ['name' => 'Apple Pay', 'type' => 'wallet', 'image' => 'wallets/apple_pay.png'],
            ['name' => 'Mada Pay', 'type' => 'wallet', 'image' => 'wallets/mada_pay.png'],
            ['name' => 'Google Pay', 'type' => 'wallet', 'image' => 'wallets/google_pay.png'],
            ['name' => 'PayPal', 'type' => 'wallet', 'image' => 'wallets/paypal.png'],
            ['name' => 'Tamara', 'type' => 'wallet', 'image' => 'wallets/tamara.png'],
            ['name' => 'Tabby', 'type' => 'wallet', 'image' => 'wallets/tabby.png'],
        ]);
    }
}
