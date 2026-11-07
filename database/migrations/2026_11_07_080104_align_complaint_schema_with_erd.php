<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('instansi')->nullable();
            $table->string('phone_number', 30)->nullable();
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->renameColumn('code', 'complaint_code');
            $table->renameColumn('location_address', 'address');
            $table->string('district')->default('');
            $table->string('village')->default('');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->renameColumn('note', 'notes');
            $table->string('status', 20)->default('ditugaskan')->index();
        });

        Schema::table('complaint_status_histories', function (Blueprint $table) {
            $table->renameColumn('from_status', 'old_status');
            $table->renameColumn('to_status', 'new_status');
            $table->renameColumn('changed_by_id', 'user_id');
            $table->renameColumn('note', 'notes');
        });

        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            foreach (['users', 'categories', 'complaints', 'assignments', 'complaint_status_histories'] as $table) {
                DB::statement("ALTER TABLE {$table} ENGINE=InnoDB");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_status_histories', function (Blueprint $table) {
            $table->renameColumn('notes', 'note');
            $table->renameColumn('user_id', 'changed_by_id');
            $table->renameColumn('new_status', 'to_status');
            $table->renameColumn('old_status', 'from_status');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
            $table->renameColumn('notes', 'note');
        });

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn(['district', 'village', 'latitude', 'longitude']);
            $table->renameColumn('address', 'location_address');
            $table->renameColumn('complaint_code', 'code');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['instansi', 'phone_number']);
        });
    }
};
