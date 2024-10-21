<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\MyApp;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // admins
    public function admins(){
        return response([ 'admins' => Role::where('name', 'Администратор')->first()->users ]);
    }
    // editors
    public function editors(){
        return response([ 'editors' => Role::where('name', 'Редактор')->first()->users ]);
    }

    // editorsAdd
    public function editorsAdd(Request $request){
        $editor = Role::where('name', 'Редактор')->first();
        User::create([
            'phone' => MyApp::ROLE_PHONE,
            'email' => $request->email,
            'role_id' => $editor->id
        ]);
    }

    // editorsDelete
    public function editorsDelete(Request $request){
        $editor = User::find($request->editorId);
        $editor->delete();
    }
}