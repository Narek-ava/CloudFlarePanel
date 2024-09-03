<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cloudflare_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('cloudflare_accounts')->onDelete('cascade');
            $table->string('domain_name');
            $table->string('zone_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloudflare_domains');
    }
};
