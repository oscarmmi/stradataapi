<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class busquedaCoincidenciasController extends Controller
{
    public function index(Request $request)
    {
        $aResponse = $this->validarDatos($request);  
        if($aResponse['code']){
            return response()->json($aResponse, 400);
        }
        return response()->json([
            'data' => []
        ], 200);
    }

    private function validarDatos($request){
        if(!$request->datos){
            return [
                "code" => 1000, 
                "message" => "Fallo de validación", 
                "errors" => [
                    [
                        "code" => 1001,
                        "field" => "Parametro no definido",
                        "message" => "- El parametro 'datos' no está definido"
                    ]
                ]
            ];
        }
        if(!is_array($request->datos)){
            return [
                "code" => 1000, 
                "message" => "Fallo de validación", 
                "errors" => [
                    [
                        "code" => 1002,
                        "field" => "Tipo incorrecto del parametro",
                        "message" => - "El parametro 'datos' debe ser un objeto"
                    ]
                ]
            ];
        }
        if(!isset($request->datos['nombrecompleto'])){
            return [
                "code" => 1000, 
                "message" => "Fallo de validación", 
                "errors" => [
                    [
                        "code" => 1003,
                        "field" => "Campo no definido",
                        "message" => "- El campo 'nombrecompleto' no está incluido dentro del parametro datos"
                    ]
                ]
            ];
        }
        if(!isset($request->datos['porcentajecoincidencia'])){
            return [
                "code" => 1000, 
                "message" => "Fallo de validación", 
                "errors" => [
                    [
                        "code" => 1004,
                        "field" => "Campo no definido",
                        "message" => "- El campo 'porcentajecoincidencia' no está incluido dentro del parametro datos"
                    ]
                ]
            ];
        }else if(!is_numeric($request->datos['porcentajecoincidencia'])){
            return [
                "code" => 1000, 
                "message" => "Fallo de validación", 
                "errors" => [
                    [
                        "code" => 1004,
                        "field" => "Tipo incorrecto del campo",
                        "message" => "- El campo 'porcentajecoincidencia' debe ser un valor númerico"
                    ]
                ]
            ];
        }else if(!preg_match ("/^[0-9]*$/", $request->datos['porcentajecoincidencia'])){
            return [
                "code" => 1000, 
                "message" => "Fallo de validación", 
                "errors" => [
                    [
                        "code" => 1004,
                        "field" => "Tipo incorrecto del campo",
                        "message" => "- El campo 'porcentajecoincidencia' debe ser un valor entero"
                    ]
                ]
            ];
        }else if(intval($request->datos['porcentajecoincidencia'])<0 || intval($request->datos['porcentajecoincidencia'])>100){
            return [
                "code" => 1000, 
                "message" => "Fallo de validación", 
                "errors" => [
                    [
                        "code" => 1005,
                        "field" => "Tipo incorrecto del campo",
                        "message" => "- El porcentaje de coincidencia debe estar entre el rango de 0 a 100"
                    ]
                ]
            ];
        }
        return [
            "code" => 0
        ];
    }

}
