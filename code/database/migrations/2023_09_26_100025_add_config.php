<?php

use App\Models\AdminConfigModel;
use Illuminate\Database\Migrations\Migration;

class AddConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AdminConfigModel::create(
            [
                'ac_id' => 1,
                'ac_code' => 'python_path',
                'ac_val' => 'python3',
                'ac_description' => 'python程序位置',
            ],
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        AdminConfigModel::where('ac_id', 1)->delete();
    }
}
