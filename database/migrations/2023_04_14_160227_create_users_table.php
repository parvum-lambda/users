<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() : void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('users_group_id');
            $table->string('name', 150)->index()->nullable();
            $table->string('username', 50)->index()->nullable();
            $table->string('email', 150)->index()->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('document', 150)->nullable();
            $table->string('phone', 20)->index()->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->string('password', 60);
            $table->boolean('enabled')->index()->default(true);
            $table->timestamps();
            $table->softDeletes()->index();
            $table->unique(['users_group_id', 'username', 'deleted_at']);
            $table->unique(['users_group_id', 'email', 'deleted_at']);
            $table->unique(['users_group_id', 'document', 'deleted_at']);
            $table->unique(['users_group_id', 'phone', 'deleted_at']);

            $table->foreign('users_group_id')
                ->references('id')
                ->on('users_groups')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('users');
    }
};
