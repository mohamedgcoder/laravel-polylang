<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->string('field');
            $table->text('value');

            $table->morphs('translatable');
            $table->timestamps();

            $table->unique(['locale', 'field', 'translatable_type', 'translatable_id'], 'unique_translation');
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
};
