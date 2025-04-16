<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('super_admin', 'admin', 'client', 'garcom') DEFAULT 'admin'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN type ENUM('super_admin', 'admin', 'client') DEFAULT 'admin'");
    }
};
