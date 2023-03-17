<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enrols', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('branch_id')->unsigned();
            $table->bigInteger('session_id')->unsigned();
            $table->bigInteger('department_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('s_class_id')->unsigned();
            $table->date('enrol_date');
            $table->string('regno')->unique();
            $table->string('rollno')->unique()->nullable();
            $table->bigInteger('section_id')->unsigned();
            $table->date('dob');
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->string('mothertongue')->nullable();
            $table->bigInteger('state_id')->nullable()->unsigned();
            $table->bigInteger('local_government_id')->nullable()->unsigned();
            $table->string('city')->nullable();
            $table->string('mobile')->nullable();
            $table->bigInteger('guardian_id')->unsigned()->nullable();
            $table->text('address');
            $table->enum('gender', ['Male', 'Female']);
            $table->enum('status', ['active', 'suspension', 'expulsion', 'authentication'])->default('active');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('s_class_id')->references('id')->on('s_classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('local_government_id')->references('id')->on('local_governments')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('guardian_id')->references('id')->on('guardians')->onDelete('cascade');
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
        Schema::dropIfExists('enrols');
    }
};
