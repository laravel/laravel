<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbstractUpload extends Model
{
    use HasFactory;

    protected $fillable = ['theme', 'file_path', 'user_id', 'abstract_upload_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getFilePathWithoutAbstractsDirectoryAttribute()
    {
        $filePath = $this->file_path;
        // Assuming the file_path looks like "abstracts/IIMATM2024_DIN_1.pdf"
        // We remove the "abstracts/" part from the file path
        return str_replace('abstracts/', '', $filePath);
    }
}
