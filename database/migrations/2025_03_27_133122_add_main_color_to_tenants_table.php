<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('main_color')->default('#f12727');
        });
    }
    
    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('main_color');
        });
    }
};
