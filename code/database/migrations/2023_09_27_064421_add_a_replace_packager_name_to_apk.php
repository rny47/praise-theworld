<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAReplacePackagerNameToApk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apk', function (Blueprint $Table) {
            $Table->boolean('a_replace_packager_name')->nullable(false)->default(false)->comment('是否替换包名');
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
            $Table->dropColumn('a_replace_packager_name');
        });
    }
}
