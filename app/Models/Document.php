<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'file_type',
        'size',
        'documentable_id',
        'documentable_type',
        'uploaded_by',
        'visibility',
        'notes',
    ];

    /**
     * Get the parent documentable model (Request, Quote, etc).
     */
    public function documentable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded the document.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    /**
     * Get the file URL
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }
    
    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        return in_array($this->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }
    
    /**
     * Check if user can access this document
     */
    public function canBeAccessedBy(User $user)
    {
        // Admins can access all documents
        if ($user->isAgency()) {
            return true;
        }
        
        // Public documents can be accessed by anyone related to the entity
        if ($this->visibility === 'public') {
            if ($this->documentable_type === 'App\Models\Request') {
                $request = ServiceRequest::find($this->documentable_id);
                return $request && ($request->customer_id === $user->id || 
                                   $user->isSubagent() && $request->service->subagents->contains($user->id));
            }
            
            if ($this->documentable_type === 'App\Models\Quote') {
                $quote = Quote::find($this->documentable_id);
                return $quote && ($quote->subagent_id === $user->id || 
                                 $quote->request->customer_id === $user->id);
            }
        }
        
        // Private documents can only be accessed by uploader and admins
        if ($this->visibility === 'private') {
            return $this->uploaded_by === $user->id;
        }
        
        // Agency documents can be accessed by anyone in the agency
        if ($this->visibility === 'agency') {
            $documentOwnerAgency = null;
            
            if ($this->documentable_type === 'App\Models\Request') {
                $documentOwnerAgency = ServiceRequest::find($this->documentable_id)->agency_id;
            } elseif ($this->documentable_type === 'App\Models\Quote') {
                $documentOwnerAgency = Quote::find($this->documentable_id)->request->agency_id;
            }
            
            return $documentOwnerAgency && $documentOwnerAgency === $user->agency_id;
        }
        
        return false;
    }
}
