<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

        protected $fillable = [
        'customer_id',
        'name',
        'description',
        'budget_hours',
        'active', // Skal være her for at kunne opdatere feltet
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
}

