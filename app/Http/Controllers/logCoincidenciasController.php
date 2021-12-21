<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Busquedas;
use App\Models\DetalleBusquedas;

class logCoincidenciasController extends Controller
{
    public function index(Request $request)
    {
        $aResponse = $this->validarDatos($request);  
        if($aResponse['estado_ejecucion']){          
            return response()->json($aResponse, 400);
        }
        $datos = Busquedas::select(DB::raw('busquedas.uuid, busquedas.nombre_buscado, busquedas.porcentaje_buscado, busquedas.registros_encontrados, busquedas.estado_ejecucion, busquedas.created_at'))
            ->where('uuid', $request->uuid)
            ->limit(1)
            ->get();
        if(!count($datos)){
            return response()->json([
                'uuid_buscado' => $request->uuid, 
                'datos' => [], 
                'detalles' => [], 
                'estado_ejecucion' => 'El uuid buscado no existe en el sistema'
            ], 400);
        }   
        $detalles = DetalleBusquedas::select(DB::raw('detalle_busquedas.nombre_encontrado, detalle_busquedas.tipo_persona, detalle_busquedas.tipo_cargo, detalle_busquedas.departamento, detalle_busquedas.municipio, detalle_busquedas.porcentaje_encontrado'))
                ->where('uuid', $request->uuid)
                ->get();     
        return response()->json([
            'uuid_buscado' => $request->uuid, 
            'datos' => $datos[0], 
            'detalles' => $detalles, 
            'estado_ejecucion' => $datos[0]->estado_ejecucion
        ], 200);
    }

    private function validarDatos($request){
        if(!$request->uuid){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid_buscado"=>'No registra', 
                "errors" => [
                    [
                        "code" => 2001,
                        "field" => "Parametro no definido",
                        "message" => "- El parametro 'uuid' no está definido"
                    ]
                ]
            ];
        }else if(!is_numeric($request->uuid)){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid_buscado"=>$request->uuid, 
                "errors" => [
                    [
                        "code" => 2002,
                        "field" => "Tipo incorrecto del campo",
                        "message" => "- El parametro 'uuid' debe ser un valor númerico"
                    ]
                ]
            ];
        }else if(!preg_match ("/^[0-9]*$/", $request->uuid)){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid_buscado"=>$request->uuid, 
                "errors" => [
                    [
                        "code" => 2003,
                        "field" => "Tipo incorrecto del campo",
                        "message" => "- El parametro 'uuid' debe ser un valor entero"
                    ]
                ]
            ];
        }
        return [
            "estado_ejecucion" => 0
        ];
    }
}
