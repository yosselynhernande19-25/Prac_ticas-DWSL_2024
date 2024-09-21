<?php

require_once 'Request.php';

class Router {
    protected $routes = [];
    protected $basePath = '';
    protected $authenticated = false; // Variable para verificar si el usuario está autenticado

    public function __construct($basePath = '') {
        $this->basePath = rtrim($basePath, '/'); // Asegúrate de que no haya una barra al final
    }

    public function get($uri, $controllerAction) {
        $this->addRoute('GET', $uri, $controllerAction);
    }

    public function post($uri, $controllerAction) {
        $this->addRoute('POST', $uri, $controllerAction);
    }

    public function put($uri, $controllerAction) {
        $this->addRoute('PUT', $uri, $controllerAction);
    }

    public function delete($uri, $controllerAction) {
        $this->addRoute('DELETE', $uri, $controllerAction);
    }

    public function patch($uri, $controllerAction) {
        $this->addRoute('PATCH', $uri, $controllerAction);
    }

    public function authenticated() {
        $this->authenticated = true; // Marcar al usuario como autenticado
    }

    protected function addRoute($method, $uri, $controllerAction) {
        // Reemplaza {parametro} por (?<parametro>[^\/]+) en la URI
        $pattern = preg_replace_callback('/{([^\/]+)}/', function($match) {
            return "(?<{$match[1]}>[^\/]+)";
        }, $uri);
        $this->routes[$method][$pattern] = [$controllerAction, $this->authenticated]; // Guardar también si la ruta requiere autenticación
        $this->authenticated = false; // Reiniciar la variable de autenticación
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Verifica qué URI se captura
        error_log("URI capturada: " . $uri);

        // Remover el basePath de la URI
        $uri = str_replace($this->basePath, '', $uri);
        error_log("URI después de eliminar basePath: " . $uri);

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $route => list($controllerAction, $authenticated)) {
                $pattern = '#^' . $route . '$#'; // Ajustar el patrón
                if (preg_match($pattern, $uri, $matches)) {
                    list($controllerName, $methodName) = explode('@', $controllerAction);

                    if ($authenticated && !$this->userIsAuthenticated()) {
                        http_response_code(401); // Devuelve un código de estado 401 si el usuario no está autenticado
                        echo 'Acceso no autorizado';
                        return;
                    }

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();

                        if (method_exists($controller, $methodName)) {
                            // Extrae los parámetros y los pasa por separado al método del controlador
                            $params = [];
                            foreach ($matches as $key => $value) {
                                if (!is_numeric($key)) {
                                    $params[] = $value;
                                }
                            }

                            if ((new ReflectionMethod($controllerName, $methodName))->getNumberOfParameters() > count($params)) {
                                array_unshift($params, new Request());
                            }

                            $controller->$methodName(...$params);
                            return;
                        }
                    }
                }
            }
        }

        http_response_code(404);
        echo 'Página no encontrada ['.$method.'] ['.$uri.']';
    }

    // Método ficticio para verificar si el usuario está autenticado
    protected function userIsAuthenticated() {
        // Implementa la lógica de autenticación aquí (por ejemplo, comprueba si hay una sesión activa)
        return isset($_SESSION['user']);
    }
}
?>
