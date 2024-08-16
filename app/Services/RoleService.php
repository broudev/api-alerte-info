<?php

namespace App\Services;

use App\Models\Role;

use Illuminate\Support\Facades\DB;

class RoleService
{

    public static function get() 
    {
        try {
            return Role::all();
        } catch (\Throwable $e) {
            return "bad_request";
        }
        
    }

    public static function add($data)
    {
        try {
            if(isset($data)):
            
                $lenght= 100;
                $keys = substr(str_shuffle(
                    str_repeat($x = 'abcdefghqoujpzy1234567890', ceil($lenght / strlen($x)))
                ), 3, $lenght);
    
                $add_role = new Role();
                
                $add_role->role = $data->role;
                $add_role->rfk = $keys;
    
    
                if($add_role->save()):
                    $history = HistoriqueServices::manager("Ajout d'un rôle #ID".$add_role->id, $data->author);
                    return $history;
                else:
                    return "error";
                endif;
            endif;
        } catch (\Throwable $e) {
            return "bad_request";
        }
        
    }

    public static function edit($id)
    {
        try {
            if(isset($id)):
            
                $edit_role = DB::table('roles')->where('id',$id)->first();
    
                if(!$edit_role):
                    return "error";
                else:
                    return $edit_role;
                endif;
            endif;
        } catch (\Throwable $e) {
            return "bad_request";
        }
        
    }


    public static function update($data, $id)
    {
        try {
            if(isset($data)):
                $lenght= 100;
                $keys = substr(str_shuffle(
                    str_repeat($x = 'abcdefghqoujpzy1234567890', ceil($lenght / strlen($x)))
                ), 3, $lenght);
    
                $role = Role::where('id',$id)->first();
                $role->role = $data->role;
                $role->rfk = $keys;
                
                if($role->save()):
                    $history = HistoriqueServices::manager("Modification d'un rôle #ID".$role->id, $data->author);
                    return $history;
                else:
                    return "error";
                endif;
            endif;
        } catch (\Throwable $e) {
            return "bad_request";
        }
        
    }

    public static function destroy($id,$author)
    {
        try {
            if(isset($id)):
                HistoriqueServices::manager("Suppression d'un rôle #ID".$id, $author);
                $delete_role = DB::table('roles')->where('id',$id)->delete();
    
                if(!$delete_role):
                    return "error";
                else:
                    return "success";
                endif;
            endif;
        } catch (\Throwable $e) {
            return "bad_request";
        }
        
    }
}