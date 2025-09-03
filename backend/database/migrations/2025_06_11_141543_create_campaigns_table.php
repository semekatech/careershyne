<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('campaigns', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('brand'); // Could be a foreign key later
        $table->string('website')->nullable();
        $table->string('status')->default('0');
         $table->string('user_id')->nullable();
        $table->text('about')->nullable();
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->date('application_deadline')->nullable();
        $table->string('locations')->nullable();
        $table->string('target_age')->nullable();
        $table->string('target_interests')->nullable();
        $table->string('primary_goal')->nullable();
        $table->text('kpis')->nullable();
        $table->json('platforms')->nullable(); // Stored as JSON array
        $table->string('content_formats')->nullable();
        $table->string('hashtags')->nullable();
        $table->string('compensation_type')->nullable();
        $table->string('budget')->nullable();
        $table->longText('brief')->nullable();
        $table->longText('guidance')->nullable();
        $table->longText('eligibility')->nullable();
        $table->string('logo')->nullable(); // Store logo file path
        $table->json('assets')->nullable(); // Store array of file paths
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
