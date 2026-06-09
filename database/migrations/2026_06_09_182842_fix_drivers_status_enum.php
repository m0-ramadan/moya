<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE drivers MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");

        DB::statement("
            UPDATE drivers d
            LEFT JOIN users u ON u.id = d.user_id
            SET d.status = CASE
                WHEN u.status = 'banned' THEN 'suspended'
                WHEN d.is_verified = 1 AND d.is_active = 1 THEN 'active'
                WHEN d.is_verified = 0 AND d.rejection_reason IS NULL THEN 'pending'
                WHEN d.is_active = 0 THEN 'inactive'
                ELSE 'inactive'
            END
        ");

        DB::statement("
            ALTER TABLE drivers
            MODIFY status ENUM('pending', 'active', 'inactive', 'suspended')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE drivers MODIFY status VARCHAR(20) NOT NULL DEFAULT 'resident'");

        DB::statement("
            UPDATE drivers
            SET status = CASE
                WHEN citizenship IN ('saudi', 'resident') THEN citizenship
                ELSE 'resident'
            END
        ");

        DB::statement("
            ALTER TABLE drivers
            MODIFY status ENUM('saudi', 'resident', 'gulf', 'visitor')
            NOT NULL DEFAULT 'resident'
        ");
    }
};
