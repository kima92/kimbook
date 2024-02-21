<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('status');
            $table->string('title');
            $table->uuid()->unique();
            $table->foreignIdFor(User::class);
            $table->text('description')->nullable();
            $table->text('input')->nullable();
            $table->string('cover_image')->nullable();
            $table->date('publication_date');
            $table->string('tags');
            $table->float('rating')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index('user_id');
            $table->index('publication_date');
            $table->index('tags');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
