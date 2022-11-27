<?php

namespace App\Models;

use Backpack\PermissionManager\app\Models\Permission;
use Illuminate\Support\Facades\Storage;

class DocumentCategory extends Permission
{

    public function documents() {
        return $this->hasMany(Document::class, 'folder_id');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            foreach($obj->documents as $document) {
                Storage::disk('documents')->delete($document->location);
            }
        });
    }
}
