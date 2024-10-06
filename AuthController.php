<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    public function index() {
      

        return $this->view('login');

        
    }
}



