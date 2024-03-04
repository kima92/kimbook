<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->index()->constrained();
            $table->string("provider_id")->index();
            $table->integer("credits");
            $table->integer("price");
            $table->tinyInteger("status")->index();
            $table->string("message")->nullable();
            $table->timestamps();
        });

        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->index()->constrained();
            $table->morphs("entity");
            $table->integer("amount");
            $table->string("message");
            $table->index(["user_id", "amount"]);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string("payment_token")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("payment_token");
        });
        Schema::dropIfExists('payments');
        Schema::dropIfExists('credits');
    }
}
