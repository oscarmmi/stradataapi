<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Diccionario;
use App\Models\Busquedas;
use App\Models\DetalleBusquedas;

class busquedaCoincidenciasController extends Controller
{
    public function index(Request $request)
    {
        $aResponse = $this->validarDatos($request);  
        if($aResponse['estado_ejecucion']){
            $aResponse['uuid'] = $this->crearRegistroBusqueda($aResponse);            
            return response()->json($aResponse, 400);
        }
        $datos = Diccionario::select(DB::raw('diccionario.nombre AS nombre_encontrado, diccionario.tipo_persona, diccionario.tipo_cargo, diccionario.departamento, diccionario.municipio, 0 AS porcentaje_encontrado'))->get();
        $nombre_buscado = trim($request->datos['nombre_buscado']);
        $resultado = [];
        foreach($datos as &$item){
            similar_text($item->nombre_encontrado, $nombre_buscado, $percent);
            $item->porcentaje_encontrado = intval($percent);
            if($item->porcentaje_encontrado === intval($request->datos['porcentaje_buscado'])){
                $resultado[] = $item;    
            }
        }
        $estado_ejecucion = "Exitoso, registros encontrados";
        $contadorRegistros = count($resultado);
        if(!$contadorRegistros){
            $estado_ejecucion = "Exitoso, sin coincidencias";
        }
        $uuid = $this->crearRegistroBusqueda([
            'nombre_buscado' => $nombre_buscado, 
            'porcentaje_buscado' => $request->datos['porcentaje_buscado'], 
            'registros_encontrados' => $contadorRegistros, 
            'estado_ejecucion' => $estado_ejecucion
        ]);
        
        foreach($resultado as $detalleItem){
            $detalle = new DetalleBusquedas;
            $detalle->nombre_encontrado = $detalleItem->nombre_encontrado;
            $detalle->tipo_persona = $detalleItem->tipo_persona;
            $detalle->tipo_cargo = $detalleItem->tipo_cargo;
            $detalle->departamento = $detalleItem->departamento;
            $detalle->municipio = $detalleItem->municipio;
            $detalle->porcentaje_encontrado = $detalleItem->porcentaje_encontrado;
            $detalle->uuid = $uuid;
            $detalle->save();
        }
        return response()->json([
            'datos' => $resultado, 
            'estado_ejecucion'=> $estado_ejecucion, 
            'nombre_buscado'=> $nombre_buscado,
            'uuid'=> $uuid
        ], 200);
    }

    private function crearRegistroBusqueda($aDatos){
        $busqueda = new Busquedas;
        $nombre_buscado = NULL;
        if($aDatos['nombre_buscado']){
            $nombre_buscado = $aDatos['nombre_buscado'];
        }
        $busqueda->nombre_buscado = $nombre_buscado;
        $porcentaje_buscado = NULL;
        if($aDatos['porcentaje_buscado']){
            $porcentaje_buscado = $aDatos['porcentaje_buscado'];
        }
        $busqueda->porcentaje_buscado = $porcentaje_buscado;
        $registros_encontrados = 0;
        if(isset($aDatos['registros_encontrados'])){
            $registros_encontrados = $aDatos['registros_encontrados'];
        }
        $busqueda->registros_encontrados = $registros_encontrados;
        $busqueda->estado_ejecucion = $aDatos['estado_ejecucion'];
        $busqueda->save();
        return $busqueda->uuid;
    }

    private function validarDatos($request){
        if(!$request->datos){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid"=>0, 
                "nombre_buscado"=>'', 
                "porcentaje_buscado"=> '', 
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
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid"=>0, 
                "nombre_buscado"=>'', 
                "porcentaje_buscado"=> '', 
                "errors" => [
                    [
                        "code" => 1002,
                        "field" => "Tipo incorrecto del parametro",
                        "message" => - "El parametro 'datos' debe ser un objeto"
                    ]
                ]
            ];
        }
        if(!isset($request->datos['nombre_buscado'])){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid"=>0, 
                "nombre_buscado"=>'', 
                "porcentaje_buscado"=> '', 
                "errors" => [
                    [
                        "code" => 1003,
                        "field" => "Campo no definido",
                        "message" => "- El campo 'nombre_buscado' no está incluido dentro del parametro datos"
                    ]
                ]
            ];
        }
        if(!isset($request->datos['porcentaje_buscado'])){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid"=>0, 
                "nombre_buscado"=>$request->datos['nombre_buscado'], 
                "porcentaje_buscado"=> '', 
                "errors" => [
                    [
                        "code" => 1004,
                        "field" => "Campo no definido",
                        "message" => "- El campo 'porcentaje_buscado' no está incluido dentro del parametro datos"
                    ]
                ]
            ];
        }else if(!is_numeric($request->datos['porcentaje_buscado'])){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid"=>0, 
                "nombre_buscado"=>$request->datos['nombre_buscado'], 
                "porcentaje_buscado"=> $request->datos['porcentaje_buscado'], 
                "errors" => [
                    [
                        "code" => 1004,
                        "field" => "Tipo incorrecto del campo",
                        "message" => "- El campo 'porcentaje_buscado' debe ser un valor númerico"
                    ]
                ]
            ];
        }else if(!preg_match ("/^[0-9]*$/", $request->datos['porcentaje_buscado'])){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid"=>0, 
                "nombre_buscado"=>$request->datos['nombre_buscado'], 
                "porcentaje_buscado"=> $request->datos['porcentaje_buscado'], 
                "errors" => [
                    [
                        "code" => 1004,
                        "field" => "Tipo incorrecto del campo",
                        "message" => "- El campo 'porcentaje_buscado' debe ser un valor entero"
                    ]
                ]
            ];
        }else if(intval($request->datos['porcentaje_buscado'])<0 || intval($request->datos['porcentaje_buscado'])>100){
            return [
                "estado_ejecucion"=> 'Error del sistema', 
                "uuid"=>0, 
                "nombre_buscado"=>$request->datos['nombre_buscado'], 
                "porcentaje_buscado"=> $request->datos['porcentaje_buscado'], 
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
            "estado_ejecucion" => 0
        ];
    }

}
