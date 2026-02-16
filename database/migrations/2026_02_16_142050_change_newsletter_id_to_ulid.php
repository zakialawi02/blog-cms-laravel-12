<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add ulid column
        Schema::table('newsletters', function (Blueprint $table) {
            $table->ulid('ulid')->nullable()->after('id');
        });

        // 2. Populate ulid for existing records
        DB::table('newsletters')->orderBy('id')->chunk(100, function ($newsletters) {
            foreach ($newsletters as $newsletter) {
                DB::table('newsletters')
                    ->where('id', $newsletter->id)
                    ->update(['ulid' => (string) Str::ulid()]);
            }
        });

        // 3. Make ulid not null and switch primary key
        Schema::table('newsletters', function (Blueprint $table) {
            $table->ulid('ulid')->nullable(false)->change();
            $table->dropColumn('id');
        });

        Schema::table('newsletters', function (Blueprint $table) {
            $table->renameColumn('ulid', 'id');
        });

        Schema::table('newsletters', function (Blueprint $table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to integer ID
        Schema::table('newsletters', function (Blueprint $table) {
            $table->unsignedBigInteger('int_id')->nullable()->first();
        });

        // We cannot restore exact original IDs easily if they were auto-increment,
        // but we can re-populate with new sequence.
        // For simplicity in down(), we just re-create the structure.

        $id = 1;
        DB::table('newsletters')->orderBy('created_at')->chunk(100, function ($rows) use (&$id) {
            foreach ($rows as $row) {
                DB::table('newsletters')
                    ->where('id', $row->id) // 'id' is currently the ULID
                    ->update(['int_id' => $id++]);
            }
        });

        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn('id'); // Drop ULID column
        });

        Schema::table('newsletters', function (Blueprint $table) {
            $table->renameColumn('int_id', 'id');
        });

        Schema::table('newsletters', function (Blueprint $table) {
            $table->id('id')->change(); // Make it auto-increment primary key
        });
    }
};
