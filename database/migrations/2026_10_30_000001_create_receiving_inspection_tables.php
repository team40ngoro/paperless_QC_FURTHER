<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receiving_inspections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('area', 20);
            $table->date('inspection_date');
            $table->string('qc_form_code', 20)->nullable();
            $table->string('prod_form_code', 20)->nullable();
            $table->string('prod_status', 20)->default('DRAFT');
            $table->string('qc_status', 20)->default('DRAFT');
            $table->unsignedBigInteger('prod_filled_by')->nullable();
            $table->timestamp('prod_submitted_at')->nullable();
            $table->unsignedBigInteger('qc_filled_by')->nullable();
            $table->timestamp('qc_submitted_at')->nullable();
            $table->json('production_data')->nullable();
            $table->json('qc_data')->nullable();
            $table->boolean('has_qc_finding')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('legacy_source', 20)->nullable();
            $table->string('legacy_source_id', 100)->nullable();
            $table->boolean('legacy_unlinked')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['area', 'inspection_date']);
            $table->index('prod_status');
            $table->index('qc_status');
            $table->index('has_qc_finding');
            $table->unique(['legacy_source', 'legacy_source_id', 'area'], 'receiving_legacy_unique');

            $table->foreign('prod_filled_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('qc_filled_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('shoestring_qc_defects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receiving_inspection_id');
            $table->string('defect_type');
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();

            $table->index('receiving_inspection_id');
            $table->foreign('receiving_inspection_id')
                ->references('id')
                ->on('receiving_inspections')
                ->cascadeOnDelete();
        });

        Schema::create('inspection_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('receiving_inspection_id');
            $table->string('area', 20);
            $table->string('section', 20);
            $table->string('action', 20);
            $table->string('field_key')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('actor_role')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['receiving_inspection_id', 'created_at']);
            $table->index('action');
            $table->foreign('receiving_inspection_id')
                ->references('id')
                ->on('receiving_inspections')
                ->cascadeOnDelete();
            $table->foreign('actor_user_id')->references('id')->on('users')->nullOnDelete();
        });

        foreach (['Production Input Checker', 'QC Auditor'] as $role) {
            if (!DB::table('roles')->where('role', $role)->exists()) {
                DB::table('roles')->insert([
                    'uuid' => (string) Str::uuid(),
                    'role' => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_audit_logs');
        Schema::dropIfExists('shoestring_qc_defects');
        Schema::dropIfExists('receiving_inspections');
    }
};
