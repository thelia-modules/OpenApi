<?php

namespace OpenApi\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router as BaseRouter;

class AnnotationRouter extends BaseRouter
{
    /** @var FileLocator */
    protected $fileLocator;

    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
        $this->setOptions([]);
    }


    public function getRouteCollection()
    {
        $loader = new AnnotationDirectoryLoader($this->fileLocator, new AnnotatedRouteControllerLoader(new AnnotationReader()));

        $routes = new RouteCollection();
        $routes->addCollection(
            $loader->load(__DIR__.'/../Controller', "annotation")
        );

        foreach ($routes as $route) {
            $route->setPath('/open_api'.$route->getPath());
        }

        return $routes;
    }
}