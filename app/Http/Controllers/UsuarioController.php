<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    private  $rules = array(
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed'
    );
    private $messages = array(
        'name.required' => 'Please enter a name.',
        'email.unique' => 'ya existe ese email.',
        'email.required' => 'email es requerido.',
        'email.email' => 'debe ser un email correcto.',
        'password.required' => 'debe ingresar una password',
        'password.confirmed' => 'debe confirmar contraseña',
    );
    public function register(Request $request)
    {
        $request->validate([]);
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages" => $messages], 500);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            "status" => 1,
            "msg" => "¡Registro de usuario exitoso!",
        ]);
    }


    public function login(Request $request)
    {

        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $user = User::where("email", "=", $request->email)->first();

        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                //creamos el token
                $token = $user->createToken("auth_token")->plainTextToken;
                //si está todo ok
                return response()->json([
                    "status" => 1,
                    "msg" => "¡Usuario logueado exitosamente!",
                    "access_token" => $token
                ]);
            }  else {
            return response()->json([
                "status" => 0,
                "msg" => "Usuario no registrado o credenciales incorrectas",
            ], 404);
        }
    }
}

    public function userProfile()
    {
        return response()->json([
            "status" => 0,
            "msg" => "Acerca del perfil de usuario",
            "data" => auth()->user()
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => 1,
            "msg" => "Cierre de Sesión",
        ]);
    }
}
