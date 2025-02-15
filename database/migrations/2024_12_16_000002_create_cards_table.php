<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->integer('cod');
            $table->unsignedBigInteger('sum_id');
            $table->string('segment');
            $table->double('credit');
            $table->double('debt');
            $table->double('entry');
            $table->double('tax');
            $table->double('deadline');
            $table->double('installment');
            $table->string('adm');
            $table->double('insurance');
            $table->double('fund');
            $table->rememberToken();
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
        Schema::dropIfExists('cards');
    }
}
