<?php

namespace App\Http\Controllers;

use App\Models\ClienteVehiculo;
use App\Models\DetalleAlquiler;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehiculoController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:index.vehiculos')->only('index');
        $this->middleware('can:store.vehiculos')->only('store');
        $this->middleware('can:update.vehiculos')->only('update');
        $this->middleware('can:destroy.vehiculos')->only('destroy');
        $this->middleware('can:alquilados.vehiculos')->only('vehiculosAlquilados');
        $this->middleware('can:alquilar.vehiculos-delete')->only('eliminar_alquiler');
        /* $this->middleware('can:alquilar.vehiculos')->only('alquilarVehiculo');
        $this->middleware('can:mis-alquilados.vehiculos')->only('vehiculosAlquiladosPropios'); */
    }

    private  $rulesAlquilar = array(
        "vehiculo_id" => 'required|integer|unique:cliente_vehiculo',
        "fecha_alquiler" => "required|date|",
        'tiempo_alquiler' => "required|date|",
        'valor_alquiler' => 'required|numeric',
    );
    private $messagesAlquilar = array(
        'vehiculo_id.required' => 'envie el id del vehiculo.',
        'vehiculo_id.integer' => 'debe ser un numero.',
        'vehiculo_id.unique' => 'debe escoger un auto no alquilado.',
        'fecha_alquiler.required' => 'fecha de alquiler es requerida',
        'fecha_alquiler.date' => 'debe ser una fecha y hora.',
        'tiempo_alquiler.required' => 'el tiempo de devolucion es requerido.',
        'tiempo_alquiler.date' => 'debe ser fecha y hora.',
        'valor_alquiler.required' => 'precio a pagar requerido',
        'valor_alquiler.numeric' => 'precio a pagar debe ser un numero',
    );
   /*  private  $rulesAlquilar = array(
        "vehiculo_id" => 'required|integer|unique:cliente_vehiculo',
        "fecha_alquiler" => "required|date|after_or_equal:now",
        'tiempo_alquiler' => "required|date|after:fecha_alquiler",
        'valor_alquiler' => 'required|numeric',
    );
    private $messagesAlquilar = array(
        'vehiculo_id.required' => 'envie el id del vehiculo.',
        'vehiculo_id.integer' => 'debe ser un numero.',
        'vehiculo_id.unique' => 'debe escoger un auto no alquilado.',
        'fecha_alquiler.required' => 'fecha de alquiler es requerida',
        'fecha_alquiler.date' => 'debe ser una fecha y hora.',
        'fecha_alquiler.after_or_equal' => 'debe ser una fecha mayor o igual a la de hoy',
        'tiempo_alquiler.required' => 'el tiempo de devolucion es requerido.',
        'tiempo_alquiler.date' => 'debe ser fecha y hora.',
        'tiempo_alquiler.after' => 'debe ser una fecha de entrega valida',
        'valor_alquiler.required' => 'precio a pagar requerido',
        'valor_alquiler.numeric' => 'precio a pagar debe ser un numero',
    ); */

    private  $rules = array(
        "modelo" => 'required|string',
        "marca" => 'required|string|regex:"^[ a-zA-ZñÑáéíóúÁÉÍÓÚ]+$"',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    );
    private  $rulesu = array(
        "modelo" => 'required|string',
        "marca" => 'required|string|regex:"^[ a-zA-ZñÑáéíóúÁÉÍÓÚ]+$"',
    );
    /* private  $rulesui = array(
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

    ); */
    /* private $messagesui = array(
        'image.required' => 'imagen requerida',
        'image.image' => 'debe ser imagen',
        'image.mimes' => 'debe ser formato jpeg,png,jpg,gif,svg',
    ); */
    private $messages = array(
        'modelo.required' => 'Please enter a modelo.',
        'modelo.string' => 'debe ser string.',
        'marca.regex' => 'solo se permiten letras en la marca.',
        'marca.required' => 'Please enter a marca.',
        'marca.string' => 'debe ser string.',
        'image.required' => 'imagen requerida',
        'image.image' => 'debe ser imagen',
        'image.mimes' => 'debe ser formato jpeg,png,jpg,gif,svg',
    );
    private $messagesu = array(
        'modelo.required' => 'Please enter a modelo.',
        'modelo.string' => 'debe ser string.',
        'marca.regex' => 'solo se permiten letras en la marca.',
        'marca.required' => 'Please enter a marca.',
        'marca.string' => 'debe ser string.',
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehiculos = Vehiculo::all()->where('estado', '=', '1');
        return response()->json(["vehiculos" => $vehiculos], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages" => $messages], 500);
        }
        $file = $request->image;
        $obj = Cloudinary::upload($file->getRealPath(), ['folder' => 'vehiculos']);
        $public_id = $obj->getPublicId();
        $url = $obj->getSecurePath();
        $vehiculo = new Vehiculo();
        $vehiculo->modelo = $request->modelo;
        $vehiculo->public_id = $public_id;
        $vehiculo->marca = $request->marca;
        $vehiculo->url = $url;
        $vehiculo->save();
        return response()->json($vehiculo, Response::HTTP_OK);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            return response()->json([
                'vehiculos' => $vehiculo
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            return response()->json([
                'messages' => "Vehiculo no disponible o sin existencia",
                "exception" => $e->getMessage()

            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->rulesu, $this->messagesu);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages" => $messages], 500);
        }
        try{
            $vehiculo = Vehiculo::findOrFail($id);
        $url = $vehiculo->url;
        $public_id = $vehiculo->public_id;
        if ($request->hasFile('image')) {
            $validator2 = Validator::make($request->all(), $this->rules, $this->messages);
            if ($validator2->fails()) {
                $messages = $validator2->messages();
                return response()->json(["messages" => $messages], 500);
            }
            Cloudinary::destroy($public_id);
            $file = request()->file('image');
            $obj = Cloudinary::upload($file->getRealPath(), ['folder' => 'vehiculos']);
            $url = $obj->getSecurePath();
            $public_id = $obj->getPublicId();
        }
        $vehiculo->update([
            "modelo" => $request->modelo,
            "marca" => $request->marca,
            "url" => $url,
            "public_id" => $public_id
        ]);
        return response()->json([
            'message' => " successfully updated",
            'vehiculo' => $vehiculo
        ], Response::HTTP_OK);
        }catch(\Throwable $e ){
            return response()->json([
                'messages' => "Vehiculo no disponible o sin existencia",
                "exception" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $producto = Vehiculo::findOrFail($id);
            $public_id = $producto->public_id;
            Cloudinary::destroy($public_id);
            Vehiculo::destroy($id);
            return response()->json([
                'messages' => "Eliminado vehiculo"
            ], RESPONSE::HTTP_OK);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => "Vehiculo no disponible o sin existencia",
                "exception" => $e->getMessage()
            ], 500);
        }

    }
    public function alquilarVehiculo(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rulesAlquilar, $this->messagesAlquilar);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages" => $messages,"now"=>Carbon::now()], 500);
        }
        try {
            $user = auth()->user();
            $vehiculo = Vehiculo::findOrFail($request->vehiculo_id);
            $vehiculo->estado = 0;
            $vehiculo->save();
            $alquiler = ClienteVehiculo::create([
                "vehiculo_id" => $vehiculo->id,
                "user_id" => $user->id
            ]);
            DetalleAlquiler::create([
                "fecha_alquiler" => $request->fecha_alquiler,
                "tiempo_alquiler" => $request->tiempo_alquiler,
                "valor_alquiler" => $request->valor_alquiler,
                "cliente_vehiculo_id" => $alquiler->id
            ]);

            return response()->json([
                'message' => "se alquiló correctamente",
                "now"=>new Carbon('yesterday')
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => "Ocurrió un error al realizar el alquiler",
                "exception" => $e->getMessage()
            ], 500);
        }
    }
    public function vehiculosAlquilados()
    {
        $vehiculo = DB::table('vehiculos')
            ->where('estado', 0)
            ->join('cliente_vehiculo', 'cliente_vehiculo.vehiculo_id', '=', 'vehiculos.id')
            ->join('users', 'cliente_vehiculo.user_id', '=', 'users.id')
            ->join('detalle_alquiler', 'cliente_vehiculo.id', '=', 'detalle_alquiler.cliente_vehiculo_id')
            ->select('users.name as nombre_usuario', 'users.cedula as cedula_usuario', 'users.email as email_usuario', 'detalle_alquiler.fecha_alquiler as fecha_alquiler', 'detalle_alquiler.tiempo_alquiler as tiempo_alquiler', 'vehiculos.marca as marca_vehiculo', 'vehiculos.modelo as modelo_vehiculo', 'vehiculos.url as url_vehiculo', 'detalle_alquiler.valor_alquiler as precio_pagar', 'cliente_vehiculo.id as id', 'vehiculos.id as vehiculo_id')
            ->get();
        return response()->json([
            'vehiculos' => $vehiculo,
        ]);
    }
    public function vehiculosAlquiladosPropios()
    {
        $user = auth()->user();
        $vehiculo = DB::table('vehiculos')
            ->where('estado', 0,)
            ->join('cliente_vehiculo', 'cliente_vehiculo.vehiculo_id', '=', 'vehiculos.id')
            ->join('users', 'cliente_vehiculo.user_id', '=', "users.id")
            ->join('detalle_alquiler', 'cliente_vehiculo.id', '=', 'detalle_alquiler.cliente_vehiculo_id')
            ->select('detalle_alquiler.fecha_alquiler as fecha_alquiler', 'detalle_alquiler.tiempo_alquiler as tiempo_alquiler', 'vehiculos.marca as marca_vehiculo', 'vehiculos.modelo as modelo_vehiculo', 'vehiculos.url as url_vehiculo', 'detalle_alquiler.valor_alquiler as precio_pagar', 'vehiculos.id as id')
            ->where('users.id', '=', $user->id)
            ->get();
        return response()->json([
            'vehiculos' => $vehiculo,
        ]);
    }
    public function eliminar_alquiler($id)
    {
        try{
            $clientevehiculo = ClienteVehiculo::findOrFail($id);
            $vehiculo = Vehiculo::findOrFail($clientevehiculo->vehiculo_id);
            $vehiculo->estado = 1;
            $vehiculo->save();
            $clientevehiculo->delete();
            return response()->json([
                'messages' => "eliminado correctamente"
            ]);
        }catch (\Throwable $e) {
            return response()->json([
                'messages' => "Ocurrió un error al eliminar el alquiler",
                "exception" => $e->getMessage()
            ], 500);
        }

    }
}
