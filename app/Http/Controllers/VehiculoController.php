<?php
namespace App\Http\Controllers;

use App\Models\ClienteVehiculo;
use App\Models\DetalleAlquiler;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\DB;
 /* use Validator; */
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
        /* $this->middleware('can:alquilar.vehiculos')->only('alquilarVehiculo');
        $this->middleware('can:mis-alquilados.vehiculos')->only('vehiculosAlquiladosPropios'); */
    }

    private  $rulesAlquilar=array(
        "vehiculo_id"=>'required|integer',
        "fecha_alquiler"=>'required|date',
        'tiempo_alquiler' => "required|after_or_equal:' . date('Y-m-d H:i:s')'",
        'valor_alquiler' => 'required|numeric|regex:/^[\d]{0,11}(\.[\d]{1,2})?$/',
    );
    private $messagesAlquilar=array(
        'vehiculo_id.required' => 'envie el id del vehiculo.',
        'vehiculo_id.integer' => 'debe ser un numero.',
        'fecha_alquiler.required' => 'fecha de alquiler es requerida',
        'fecha_alquiler.date' => 'debe ser una fecha.',
        'tiempo_alquiler.required' => 'el tiempo de devolucion es requerido.',
        'tiempo_alquiler.dateTime' => 'debe ser fecha y hora.',
        'valor_alquiler.required'=>'precio a pagar requerido',
    );
    private  $rules=array(
        "modelo"=>'required|string|unique:vehiculos',
        "marca"=>'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    );
    private $messages=array(
        'modelo.required' => 'Please enter a modelo.',
        'modelo.unique' => 'Please enter other model.',
        'modelo.string' => 'debe ser string.',
        'marca.required' => 'Please enter a marca.',
        'marca.string' => 'debe ser string.',
        'image.required'=>'imagen requerida',
        'image.image'=>'debe ser imagen',
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehiculos=Vehiculo::all()->where('estado','=','1');
        return response()->json(["vehiculos"=>$vehiculos], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        $validator=Validator::make($request->all(),$this->rules,$this->messages);
        if($validator->fails())
        {
            $messages=$validator->messages();
            return response()->json(["messages"=>$messages], 500);
        }
        $file = $request->image;;
         $obj = Cloudinary::upload($file->getRealPath(),['folder'=>'vehiculos']);
        $public_id = $obj->getPublicId();
        $url =$obj->getSecurePath();
        $vehiculo=new Vehiculo();
        $vehiculo->modelo=$request->modelo;
        $vehiculo->public_id=$public_id;
        $vehiculo->marca=$request->marca;
        $vehiculo->url=$url;
        $vehiculo->estado=1;
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

        $vehiculo=Vehiculo::findOrFail($id);
        return response()->json([
            'vehiculo'=>$vehiculo
        ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id )
    {

        $validator=Validator::make($request->all(),$this->rules,$this->messages);
        if($validator->fails())
        {
            $messages=$validator->messages();
            return response()->json(["messages"=>$messages], 500);
        }
        $vehiculo = Vehiculo::find($id);
        $url = $vehiculo->url;
        $public_id = $vehiculo->public_id;
        if($request->hasFile('image')){
            Cloudinary::destroy($public_id);
            $file = request()->file('image');
            $obj = Cloudinary::upload($file->getRealPath(),['folder'=>'vehiculos']);
            $url = $obj->getSecurePath();
            $public_id = $obj->getPublicId();
        }
        $vehiculo->update([
            "modelo"=>$request->modelo,
            "marca"=>$request->marca,
            "url"=>$url,
            "public_id"=>$public_id
        ]);
        return response()->json([
            'message'=>" successfully updated",
            'vehiculo'=>$vehiculo
        ], Response::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $producto = Vehiculo::find($id);
        $public_id = $producto->public_id;
        Cloudinary::destroy($public_id);
        Vehiculo::destroy($id);
        /* $vehiculo=Vehiculo::findOrFail($id);
        unlink(storage_path('app/public/'.$vehiculo->image));
        /* Storage::disk('public')->delete($vehiculo->image); */
        return response()->json([
            'message'=>"delete vehiculo"
        ]);
    }
    public function alquilarVehiculo(Request $request)
    {
        $validator=Validator::make($request->all(),$this->rulesAlquilar,$this->messagesAlquilar);
        if($validator->fails())
        {
            $messages=$validator->messages();
            return response()->json(["messages"=>$messages], 500);
        }
         $user=auth()->user();
        $vehiculo=Vehiculo::findOrFail($request->vehiculo_id);
        $vehiculo->estado=0;
        $vehiculo->save();
        $alquiler=ClienteVehiculo::create([
            "vehiculo_id"=>$vehiculo->id,
            "user_id"=>$user->id
        ]);
        DetalleAlquiler::create([
            "fecha_alquiler"=>$request->fecha_alquiler,
            "tiempo_alquiler"=>$request->tiempo_alquiler,
            "valor_alquiler"=>$request->valor_alquiler,
            "cliente_vehiculo_id"=>$alquiler->id
        ]);
        return response()->json([
            'message'=>"se alquiló correctamente"
        ],200);
    }
    public function vehiculosAlquilados()
    {
        $vehiculo = DB::table('vehiculos')
        ->where('estado', 0)
        ->join('cliente_vehiculo', 'cliente_vehiculo.vehiculo_id', '=', 'vehiculos.id')
        ->join('users', 'cliente_vehiculo.user_id', '=', 'users.id')
        ->join('detalle_alquiler','cliente_vehiculo.id','=','detalle_alquiler.cliente_vehiculo_id')
        ->select('users.name as nombre_usuario', 'users.cedula as cedula_usuario', 'users.email as email_usuario', 'detalle_alquiler.fecha_alquiler as fecha_alquiler', 'detalle_alquiler.tiempo_alquiler as tiempo_alquiler','vehiculos.marca as marca_vehiculo','vehiculos.modelo as modelo_vehiculo','vehiculos.url as url_vehiculo')
        ->get();
        return response()->json([
            'vehiculos'=>$vehiculo,
        ]);
    }
    public function vehiculosAlquiladosPropios()
    {
        $user=auth()->user();
        $vehiculo = DB::table('vehiculos')
        ->where('estado', 0,)
        ->join('cliente_vehiculo', 'cliente_vehiculo.vehiculo_id', '=', 'vehiculos.id')
        ->join('users', 'cliente_vehiculo.user_id','=',"users.id")
        ->join('detalle_alquiler','cliente_vehiculo.id','=','detalle_alquiler.cliente_vehiculo_id')
         ->select('detalle_alquiler.fecha_alquiler as fecha_alquiler', 'detalle_alquiler.tiempo_alquiler as tiempo_alquiler','vehiculos.marca as marca_vehiculo','vehiculos.modelo as modelo_vehiculo','vehiculos.url as url_vehiculo','detalle_alquiler.valor_alquiler as precio_pagar')
        ->where('users.id','=',$user->id)
        ->get();
        return response()->json([
            'vehiculos'=>$vehiculo,
        ]);
    }
}
