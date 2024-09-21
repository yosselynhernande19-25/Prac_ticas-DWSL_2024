<?php
require_once 'BaseController.php';

class HomeController extends BaseController {
    public function index() {
        $data = [
            'nombre' => 'Yosselyn Hernandez',
            'edad' => 23,
            'items' => [
                'item 1',
                'item 2',
                'item 3',
                'item 4',
                'item 5'
            ]
        ];

        return $this->view('home', $data);
    }
}
