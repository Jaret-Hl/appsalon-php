<?php 
namespace Controllers;

use Model\Cita;
use Model\CitaServicios;
use Model\Servicio;

class APIController{
    public static function index(){
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar(){
        // almacena la cita y devuelve el id
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $id = $resultado['id'];
                
        // almacena las citas y los servicios
        $idServicios = explode(",", $_POST['servicios']);
        // almacena los servicios con el id de la cita
        foreach ($idServicios as $idServicio) {
            $args=[
                'citaId'=> $id,
                'serviciosId'=> $idServicio
            ];
            $citaServicio = new CitaServicios($args);
            $citaServicio->guardar();
        }
        // retornamos una respuesta
        $respuesta = [
            'resultado' => $resultado
        ];
        echo json_encode($respuesta);
    }

    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $cita = Cita::find($_POST['id']);

            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}