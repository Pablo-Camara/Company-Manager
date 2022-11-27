<?php

namespace App\Imports;

use App\Models\User;
use Backpack\PermissionManager\app\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class UsersImport implements ToCollection, WithHeadingRow
{
    public $result;
    private $roles;

    public function __construct(&$totalImported, $roles) {
        $this->result = $totalImported;
        $this->roles = $roles;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $line = 0;
        foreach ($rows as $row) {
            $line++;
            try {
                Validator::make($row->toArray(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'nif' => 'required|min:9|max:9',
                ])->validate();
            } catch (\Throwable $th) {
                $messages = $th->validator->getMessageBag()->messages();
                $messagesConcat = '' . "<br/>";
                foreach($messages as $col => $msg){
                    if (is_array($msg)) {
                        $msg = implode(', ', $msg);
                    }
                    $messagesConcat .= __($msg) . "<br/>";
                }
                \Alert::error(__('Errors in line number') . ' ' . $line . ':' . $messagesConcat)->flash();
                continue;
            }

            $data = [
                'name'     => $row["name"],
                'email'    => $row["email"],
                'nif' => $row["nif"],
                'password' => Hash::make($row["nif"]),
            ];

            $user = User::create($data);

            foreach($this->roles as $roleId) {
                $role = Role::findById($roleId);
                $user->assignRole($role->name);
            }
            $this->result->count++;
        }
    }

}
