<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneratorTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('generator', function (Blueprint $table): void {
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
