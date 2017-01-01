<?php

namespace FastRoute;

class RouteCollector {
    private $routeParser;
    private $dataGenerator;

    private $parsedRoutes;  // The saved route data, for use by RouteGenerator
    private $nextAnonRoute; // Counter for unique route names in parsedRoutes

    /**
     * Constructs a route collector.
     *
     * @param RouteParser   $routeParser
     * @param DataGenerator $dataGenerator
     */
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator) {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->parsedRoutes = [];
        $this->nextAnonRoute = 0;
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed  $handler
     * @param string $routename (optional) Name used to generate this route
     */
    public function addRoute($httpMethod, $route, $handler, $routename='') {
        $routeDatas = $this->routeParser->parse($route);
        if(strlen($routename)==0) {     // assign default route name if missing
            $routename = '__route__' . $this->nextAnonRoute++;
        }
        $this->parsedRoutes[$routename] = $routeDatas;
        foreach ((array) $httpMethod as $method) {
            foreach ($routeDatas as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, $handler);
            }
        }
    }
    
    /**
     * Adds a GET route to the collection
     * 
     * This is simply an alias of $this->addRoute('GET', $route, $handler, $routename)
     *
     * @param string $route
     * @param mixed  $handler
     * @param string $routename (optional)
     */
    public function get($route, $handler, $routename='') {
        $this->addRoute('GET', $route, $handler, $routename);
    }
    
    /**
     * Adds a POST route to the collection
     * 
     * This is simply an alias of $this->addRoute('POST', $route, $handler, $routename)
     *
     * @param string $route
     * @param mixed  $handler
     * @param string $routename (optional)
     */
    public function post($route, $handler, $routename='') {
        $this->addRoute('POST', $route, $handler, $routename);
    }
    
    /**
     * Adds a PUT route to the collection
     * 
     * This is simply an alias of $this->addRoute('PUT', $route, $handler, $routename)
     *
     * @param string $route
     * @param mixed  $handler
     * @param string $routename (optional)
     */
    public function put($route, $handler, $routename='') {
        $this->addRoute('PUT', $route, $handler, $routename);
    }
    
    /**
     * Adds a DELETE route to the collection
     * 
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler, $routename)
     *
     * @param string $route
     * @param mixed  $handler
     * @param string $routename (optional)
     */
    public function delete($route, $handler, $routename='') {
        $this->addRoute('DELETE', $route, $handler, $routename);
    }
    
    /**
     * Adds a PATCH route to the collection
     * 
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler, $routename)
     *
     * @param string $route
     * @param mixed  $handler
     * @param string $routename (optional)
     */
    public function patch($route, $handler, $routename='') {
        $this->addRoute('PATCH', $route, $handler, $routename);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler, $routename)
     *
     * @param string $route
     * @param mixed  $handler
     * @param string $routename (optional)
     */
    public function head($route, $handler, $routename='') {
        $this->addRoute('HEAD', $route, $handler, $routename);
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData() {
        return $this->dataGenerator->getData();
    }

    /**
     * Returns the collected parsed routes.
     *
     * For use by the RouteGenerator.
     *
     * @return array
     */
    public function getParsedRoutes() {
        return $this->parsedRoutes;
    }
}
