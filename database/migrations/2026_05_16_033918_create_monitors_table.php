<?php

use App\Enums\MonitorStatusEnum;
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
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique()->index();
            $table->unsignedTinyInteger('check_interval')->default(5)->index();
            $table->unsignedTinyInteger('threshold')->default(3)->index();
            $table->string('status')->index();
            $table->unsignedInteger('consecutive_failures')->default(0)->index();
            $table->timestamp('last_checked_at')->nullable()->index();
            $table->timestamp('next_check_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
