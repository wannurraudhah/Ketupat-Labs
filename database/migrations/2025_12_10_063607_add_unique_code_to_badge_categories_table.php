<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
/*  public function up()
    {
        Schema::table('badge_categories', function (Blueprint $table) {
            $table->unique('code');
        });
    } */
    
    public function down()
    {
        Schema::table('badge_categories', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });
    }
    
};
