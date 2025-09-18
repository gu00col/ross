<?php

namespace MF\Controller;
require_once('./logs.php');
abstract class Action
{

    protected $view;
    private $layout_path = '/../Views/layouts/';
    private $sections = [];
    private $currentSection = null;

    public function __construct()
    {
        $this->view = new \stdClass();
    }

    protected function render($view, $layout) {
        $this->view->page = $view;
        $layoutPath = __DIR__ . '/../../../App/Views/layouts/' . $layout . '.phtml';

        if (file_exists($layoutPath)) {
            require_once $layoutPath;
        } else {
            $this->content();
        }
    }

    protected function content() {
        $class_atual = get_class($this);

        $class_atual = str_replace('App\\Controllers\\', '', $class_atual);
        $class_atual = strtolower(str_replace('Controller', '', $class_atual));

        require_once __DIR__ . "/../../../App/Views/".$class_atual."/".$this->view->page.".phtml";
    }

    protected function beginSection($name) {
        $this->currentSection = $name;
        ob_start();
    }

    protected function endSection() {
        $this->sections[$this->currentSection] = ob_get_clean();
        $this->currentSection = null;
    }

    protected function renderSection($name) {
        if (isset($this->sections[$name])) {
            echo $this->sections[$name];
        }
    }

}

?>
