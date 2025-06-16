<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateApkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apk', function (Blueprint $Table) {
            $Table->integer('a_last_package')->nullable(false)->default(0)->after('a_limit')->comment('最后打包时间');
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
            $Table->dropColumn('a_last_package');
        });
    }
}
