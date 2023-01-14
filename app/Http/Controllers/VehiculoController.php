<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Validator;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class VehiculoController extends Controller
{
    private  $rules=array(
        "modelo"=>'required|string',
        "marca"=>'required|string',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    );
    private $messages=array(
        'modelo.required' => 'Please enter a modelo.',
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
        return response()->json([
            "$vehiculos" => $vehiculos
        ], Response::HTTP_OK);
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
        $vehiculo = new Vehiculo($request->all());
        $vehiculo->estado=1;
        $path = $request->image->store('public/vehiculo');
//        $path = $request->image->storeAs('public/articles', $request->user()->id . '_' . $article->title . '.' . $request->image->extension());

        $vehiculo->image ='vehiculo/' . basename(time().'-'.$path);
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
        }$this->$vehiculo=Vehiculo::findOrFail($id);
        $this->vehiculo->modelo=$request->modelo;
        $vehiculo->marca=$request->marca;
        $path = $request->image->store('public/vehiculo');
        unlink(storage_path('app/public/'.$vehiculo->image));
        $vehiculo->image = 'vehiculo/' . basename(time().'-'.$path);
        $vehiculo->save();
        return response()->json([
            'message'=>" successfully updated",
            'vehiculo'=>$request->all()
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
        $vehiculo=Vehiculo::findOrFail($id);
        unlink(storage_path('app/public/'.$vehiculo->image));
        /* Storage::disk('public')->delete($vehiculo->image); */
        $vehiculo->delete();
        return response()->json([
            'message'=>"delete correct image"
        ]);
    }
}
