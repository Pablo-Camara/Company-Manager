<?php

namespace App\Models;

use Backpack\PermissionManager\app\Models\Permission;
class DocumentCategory extends Permission
{

    public function documents() {
        return $this->hasMany(Document::class, 'folder_id');
    }
}
