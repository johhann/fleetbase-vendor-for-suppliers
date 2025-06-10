<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up(): void
    {
        Schema::create('vendors_for_suppliers', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->uuid('uuid')->unique(); // CHAR(36) NOT NULL UNIQUE
            $table->uuid('company_uuid'); // CHAR(36) NOT NULL

            $table->string('name'); // VARCHAR(255) NOT NULL
            $table->string('email'); // VARCHAR(255) NOT NULL
            $table->string('phone', 20); // VARCHAR(20) NOT NULL
            $table->text('address')->nullable(); // TEXT NULL

            $table->enum('status', ['active', 'inactive'])->default('active'); // ENUM
            $table->string('qr_code_path', 500)->nullable(); // VARCHAR(500)
            $table->text('qr_code_data')->nullable(); // TEXT
            $table->string('qr_code_url', 500)->nullable(); // VARCHAR(500)

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at

            // Indexes
            $table->index('company_uuid', 'idx_vendors_company_uuid');
            $table->index('email', 'idx_vendors_email');
            $table->index('status', 'idx_vendors_status');

            // Unique key for company_uuid and email
            $table->unique(['company_uuid', 'email'], 'unique_company_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors_for_suppliers');
    }
}
