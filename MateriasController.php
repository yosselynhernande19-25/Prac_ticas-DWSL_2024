<?php

class MateriasController extends BaseController 
{
    public function index() 
    {
        $materias= DB::table('asignaturas')->get(); 

    
        return $this->view('materias/index', ["materias" => $materias]);
        

    }
}
