<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_id');
            $table->integer('duplicate_fields')->default(0);
            $table->integer('duplicate_null')->default(0);
            $table->integer('sum')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_reports');
    }
}
