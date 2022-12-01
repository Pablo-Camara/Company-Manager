<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, CrudTrait, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'nif',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getPermissions() {
        $userPermissions = $this->getAllPermissions();
        $userPermissions = $userPermissions->pluck('id')->toArray();
        return $userPermissions;
    }

    public function getFolders($userPermissions = 'not-set') {
        if ($userPermissions === 'not-set') {
            $userPermissions = $this->getPermissions();
        }
        if ($this->isAdmin()) {
            $folders = DocumentCategory::all();
        } else {
            $folders = DocumentCategory::whereIn('id', $userPermissions)->get();
        }

        return $folders;
    }

    public function isAdmin() {
        return $this->hasRole('Admin');
    }

    public function recentDocuments() {
        $recentDocuments = Document::select([
            'id',
            'name',
            'created_at'
        ]);

        if (!$this->isAdmin()) {
            $foldersWithPermissions = $this->getFolders()->pluck('id')->toArray();
            $recentDocuments = $recentDocuments->whereIn('folder_id', $foldersWithPermissions);
        }

        $recentDocuments = $recentDocuments->orderBy('created_at', 'DESC')
                            ->orderBy('updated_at', 'DESC')
                            ->limit(20)
                            ->get();

        return $recentDocuments;
    }
}
