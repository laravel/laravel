<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void{
  Schema::table('companies',function(Blueprint $t){$t->decimal('low_balance_threshold',12,2)->nullable()->after('receipt_prefix');});
 }
 public function down():void{
  Schema::table('companies',function(Blueprint $t){$t->dropColumn('low_balance_threshold');});
 }
};
