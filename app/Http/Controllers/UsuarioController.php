<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class UsuarioController extends Controller
{
    private  $rules = array(
        'name' => 'required|regex:"^[ a-zA-ZñÑáéíóúÁÉÍÓÚ]+$"',
        'email' => 'required|email|unique:users',
        'password' => 'required',
        'cedula' => 'required|unique:users',
    );
    private $messages = array(
        'name.required' => 'Please enter a name.',
        'name.regex' => 'no debe ser un numero, solo letras.',
        'email.unique' => 'ya existe ese email.',
        'cedula.unique' => 'ya existe esa cedula.',
        'cedula.required' => 'cedula es requerida.',
        'email.required' => 'email es requerido.',
        'email.email' => 'debe ser un email correcto.',
        'password.required' => 'debe ingresar una password',
    );
    private  $rulesLogin = array(
        'email' => 'required|email',
        'password' => 'required'
    );
    private $messagesLogin = array(
        'email.unique' => 'ya existe ese email.',
        'email.required' => 'email es requerido.',
        'email.email' => 'debe ser un email correcto.',
        'password.required' => 'debe ingresar una password',
    );
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages" => $messages], 500);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->cedula = $request->cedula;
        $user->assignRole('cliente');
        $user->save();

        return response()->json([
            "status" => 1,
            "messages" => "¡Registro de usuario exitoso!",
        ]);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rulesLogin, $this->messagesLogin);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages" => $messages], 500);
        }

         $user = User::where("email", "=", $request->email)->first();
        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                //creamos el token
                $token = $user->createToken("auth_token")->plainTextToken;
                //si está todo ok
                return response()->json([

                    "messages" => "¡Usuario logueado exitosamente!",
                    "access_token" => $token
                ]);
            }  else {
            return response()->json([
                "status" => 0,
                "messages" => "credenciales incorrectas",
            ], 404);
        }
    }else{
        return response()->json([
            "status" => 0,
            "messages" => "Usuario no registrado",
        ], 404);
    }
}

    public function userProfile()
    {
        $user=auth()->user();
         $usuario= DB::table('users')
        ->where('users.id','=',$user->id)
        ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->select('users.name as nombre_usuario', 'users.cedula as cedula_usuario', 'users.email as email_usuario', 'roles.name as rol')
        ->get();
        return response()->json([
            "msg" => "Acerca del perfil de usuario",
            "user" => $usuario[0],
            /* "usuario"=>$user, */
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => 1,
            "messages" => "Cierre de Sesión",
        ]);
    }

}
