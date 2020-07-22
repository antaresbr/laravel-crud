<?php

namespace Antares\Crud\Route;

use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Router;

class CrudResourceRegistrar extends ResourceRegistrar
{
    /**
     * Create a new resource registrar instance.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct($router);

        if (!in_array('metadata', $this->resourceDefaults)) {
            array_unshift($this->resourceDefaults, 'metadata');
        }
    }

    protected function addResourceMetadata($name, $base, $controller, $options)
    {
        $uri = $this->getResourceUri($name) . '/_get-metadata_';

        $action = $this->getResourceAction($name, $controller, 'metadata', $options);

        return $this->router->get($uri, $action);
    }
}
