<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, get all existing services
        $services = DB::table('services')->get();
        
        // Add new JSON columns temporarily
        Schema::table('services', function (Blueprint $table) {
            $table->json('name_json')->nullable()->after('user_id')->comment('Name in JSON format: {"en":"Name","ar":"الاسم"}');
            $table->json('notes_json')->nullable()->after('renewal_date')->comment('Notes in JSON format: {"en":"Note","ar":"ملاحظة"}');
        });
        
        // Migrate existing data to JSON format
        foreach ($services as $service) {
            DB::table('services')->where('id', $service->id)->update([
                'name_json' => json_encode(['en' => $service->name, 'ar' => null]),
                'notes_json' => $service->notes ? json_encode(['en' => $service->notes, 'ar' => null]) : null,
            ]);
        }
        
        // Drop old columns and rename new ones
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['name', 'notes']);
        });
        
        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('notes_json', 'notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all existing services
        $services = DB::table('services')->get();
        
        // Add old text columns temporarily
        Schema::table('services', function (Blueprint $table) {
            $table->string('name_text')->nullable()->after('user_id');
            $table->text('notes_text')->nullable()->after('renewal_date');
        });
        
        // Migrate JSON data back to text
        foreach ($services as $service) {
            $nameData = json_decode($service->name, true);
            $notesData = $service->notes ? json_decode($service->notes, true) : null;
            
            DB::table('services')->where('id', $service->id)->update([
                'name_text' => $nameData['en'] ?? '',
                'notes_text' => $notesData ? ($notesData['en'] ?? null) : null,
            ]);
        }
        
        // Drop JSON columns and rename text columns
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['name', 'notes']);
        });
        
        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('name_text', 'name');
            $table->renameColumn('notes_text', 'notes');
        });
    }
};
