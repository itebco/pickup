<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_code')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('address_id');
            $table->dateTime('pickup_date');
            $table->string('pickup_time');
            $table->integer('quantity')->default(1);
            $table->string('method');
            $table->string('status')->default('pending');
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Create index for frequently queried fields
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
