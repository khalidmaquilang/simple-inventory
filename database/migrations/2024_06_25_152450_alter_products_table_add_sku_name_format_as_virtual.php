<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $connectionName = config('database.default');
            $connection = DB::connection($connectionName);

            if ($connection->getDriverName() === 'sqlite') {
                $table->string('sku_name_format')->virtualAs("sku || ' - ' || name")->after('name');
            } else {
                $table->string('sku_name_format')->virtualAs("concat(sku, ' - ', name)")->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku_name_format');
        });
    }
};
