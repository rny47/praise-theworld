<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompressColumOnApkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apk', function (Blueprint $Table) {
            $Table->string('a_new_package_name')->nullable(false)->default('')->comment('自动打包新的包名');
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
            $Table->dropColumn('a_new_package_name');
        });
    }
}
