<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneratorTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('generator', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name', 255);
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->binary('photo');
            $table->boolean('CONFIRMED');
            $table->char('name', 100)->nullable();
            $table->decimal('amount', 8, 2);
            $table->double('amountDouble', 8, 2);
            $table->float('amountFloat', 8, 2);
            $table->time('sunrise');
        });
    }
}
