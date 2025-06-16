<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAIsCompressColum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apk', function (Blueprint $Table) {
            $Table->boolean('a_is_compress')->nullable(false)->default(false)->comment('是否压缩包:1=是,0=否');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apk', function (Blueprint $Table) {
            $Table->dropColumn('a_is_compress');
        });
    }
}
