<?php 

namespace MF\Init;

abstract class Bootstrap { 

    private $routes;
    protected $projectName;

    abstract protected function initRoutes();

    public function __construct($projectName)
    {
        $this->projectName = $projectName;
        $this->initRoutes();
        $this->run($this->getUrl());
    }

    public function getRoutes()
    {
        //echo 'Iniciando getRoutes <br>';
        return $this->routes;
    }

    public function setRoutes(array $routes)
    {
        //echo 'Iniciando setRoutes <br>';
        $this->routes = $routes;
    }
    protected function run($url) {
        $routeFound = false;
        foreach ($this->routes as $route) {
            if ($url == $route['route']) {
                $routeFound = true;
                $controllerName = '\\App\\Controllers\\' . $route['controller'];
                $action = $route['action'];

                $controller = new $controllerName();
                $controller->$action();
                break;
            }
        }

        if (!$routeFound) {
            $controllerName = '\\App\\Controllers\\NotFoundController';
            $controller = new $controllerName();
            $controller->index();
        }
	}

	protected function getUrl() {
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}
}

?>