<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Backfill: enable Open for Collaboration for all sellers/creators.
        // This matches the new default behavior.
        // Spatie permission tables: roles + model_has_roles.

        $sellerUserIds = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', '=', 'App\\Models\\User')
            ->whereIn('roles.name', ['creator', 'seller'])
            ->select('model_has_roles.model_id');

        DB::table('user_options')
            ->whereIn('user_id', $sellerUserIds)
            ->update(['collaboration' => 1]);
    }

    public function down(): void
    {
        // Intentionally no rollback: user preference may have changed.
    }
};
