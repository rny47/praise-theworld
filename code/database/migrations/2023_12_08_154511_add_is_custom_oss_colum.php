<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsCustomOssColum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oss', function (Blueprint $Table) {
            $Table->string('o_host')->nullable(false)->default('')->comment('自定义OSS域名');
            $Table->string('o_type', 200)->nullable(false)->default('')->comment('类型：tencent=腾讯,s3=亚马逊,minio=MinIO')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oss', function (Blueprint $Table) {
            $Table->dropColumn('o_host');
        });
    }
}
