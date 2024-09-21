
<?php

require_once 'config/TemplateEngine.php';

class BaseController {
    protected function view($name, $data = []) {
        $template = new TemplateEngine('views/'.$name.'.php');
        return $template->render($data);
    }

    protected function redirect($url) {
        header('Location: '.$url);
        exit();
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function error($message, $code = 500) {
        http_response_code($code);
        echo $message;
        exit();
    }

    protected function abort($code = 404) {
        http_response_code($code);
        echo 'Página no encontrada';
        exit();
    }
}

