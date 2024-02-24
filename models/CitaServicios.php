<?php

namespace Model;

class CitaServicios extends ActiveRecord
{
    // base de datos
    protected static $tabla = 'citasservicios';
    protected static $columnasDB = ['id', 'citaId', 'serviciosId'];

    public $id;
    public $citaId;
    public $serviciosId;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->citaId = $args['citaId'] ?? '';
        $this->serviciosId = $args['serviciosId'] ?? '';
    }
}
