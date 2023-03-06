<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('no')->default(NULL);
            $table->string('title')->default(NULL);
            $table->string('publication')->default(NULL);
            $table->string('index')->default(NULL);
            $table->string('quartile')->default(NULL);
            $table->integer('year')->default(NULL);
            $table->string('authors')->default(NULL);
            $table->text('abstracts')->default(NULL);
            $table->text('keywords')->default(NULL);
            $table->string('language')->default(NULL);
            $table->string('type')->default(NULL);
            $table->string('publisher')->default(NULL);
            $table->text('references_ori')->default(NULL);
            $table->text('references_filter');
            $table->integer('cited')->default(0);
            $table->integer('cited_gs')->default(0);
            $table->string('citing_new')->default(NULL);
            $table->string('keyword')->default(NULL);
            $table->string('edatabase')->default(NULL);
            $table->string('edatabase_2')->default(NULL);
            $table->string('nation_first_author')->default(NULL);
            $table->string('file')->default(NULL);
            $table->foreignId('project_id');
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
        Schema::dropIfExists('articles');
    }
};
