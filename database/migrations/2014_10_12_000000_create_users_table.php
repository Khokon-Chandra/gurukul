<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\UserTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments');
            $table->string('name')->index();
            $table->string('username')->unique('username')->index();
            $table->string('email')->unique()->index();
            $table->timestamp('email_verified_at')->nullable()->index();
            $table->string('password')->index();
            $table->boolean('active')->default(true)->index();
            $table->string('last_login_ip')->nullable()->index();
            $table->string('timezone')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();
            $table->timestamp('last_login_at')->useCurrent();
            $table->timestamp('last_performed_at')->nullable();
            $table->boolean('status')->default(0)->comment('0= offline, 1= online');
            $table->enum('type',[UserTypeEnum::MANAGEMENT->value, UserTypeEnum::ADMIN->value, UserTypeEnum::USER->value])->default(UserTypeEnum::USER->value)->index();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
