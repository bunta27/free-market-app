<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeMessageReadsTable extends Migration
{
    public function up()
    {
        Schema::create('trade_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['trade_message_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_message_reads');
    }
}