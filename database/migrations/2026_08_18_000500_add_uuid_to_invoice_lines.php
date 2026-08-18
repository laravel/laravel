<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{
  Schema::table('invoice_lines',function(Blueprint $t){$t->uuid('uuid')->unique()->after('id');});
 }
 public function down():void{
  Schema::table('invoice_lines',function(Blueprint $t){$t->dropUnique(['uuid']);$t->dropColumn('uuid');});
 }
};
