<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotoEditingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_editings', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('path',2000);
            $table->string('type',25);
            $table->string('data');
            $table->string('output_path',2000)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->foreignIdFor(App\Models\User::class,'user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photo_editings');
    }
}
