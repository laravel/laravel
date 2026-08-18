<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{
  Schema::table('personal_access_tokens',function(Blueprint $t){$t->foreignId('device_id')->nullable()->constrained()->nullOnDelete();});
 }
 public function down():void{
  Schema::table('personal_access_tokens',function(Blueprint $t){$t->dropConstrainedForeignId('device_id');});
 }
};
