<?php
namespace App\Http\Controllers;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Validator;
class VehiculoController extends Controller
{
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
        $vehiculos=Vehiculo::all();
        return response()->json($vehiculos, Response::HTTP_OK);
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
        $producto = Vehiculo::find($id);
        $url = $producto->url;
        $public_id = $producto->public_id;
        if($request->hasFile('image')){
            Cloudinary::destroy($public_id);
            $file = request()->file('image');
            $obj = Cloudinary::upload($file->getRealPath(),['folder'=>'vehiculos']);
            $url = $obj->getSecurePath();
            $public_id = $obj->getPublicId();
        }
        $producto->update([
            "nombre"=>$request->nombre,
            "descripcion"=>$request->descripcion,
            "url"=>$url,
            "public_id"=>$public_id
        ]);
        return response()->json([
            'message'=>" successfully updated",
            'vehiculo'=>$producto
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
            'message'=>"delete correct image"
        ]);
    }
}
