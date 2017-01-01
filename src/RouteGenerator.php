<?php

namespace FastRoute;

/**
 * Generate routes.
 *
 * Implementations should collect any necessary data in their constructors.
 */
interface RouteGenerator {
    /**
     * Generates a route string from a parsed route data array.
     *
     * The expected output is defined using an example:
     *
     * For the route string "/fixedRoutePart/{varName}[/moreFixed/{varName2:\d+}]", 
     * with name "my_route", if {varName} is interpreted as
     * a placeholder and [...] is interpreted as an optional route part, the 
     * parsed route data is:
     *
     * 'my_route' => [
     *     // first route: without optional part
     *     [
     *         "/fixedRoutePart/",
     *         ["varName", "[^/]+"],
     *     ],
     *     // second route: with optional part
     *     [
     *         "/fixedRoutePart/",
     *         ["varName", "[^/]+"],
     *         "/moreFixed/",
     *         ["varName2", [0-9]+"],
     *     ],
     * ]
     *
     * Here one route string was converted into two route data arrays,
     * or "branches".
     * RouteGenerator assumes the branches are listed in order
     * from shortest to longest.
     *
     * A route can be generated with this call:
     *
     * $generator = new RouteGenerator($parsedRoutes);
     * $generator->gen('my_route', array('varName'=>'hello'));
     *    --> returns '/fixedRoutePart/hello'
     * $generator->gen('my_route', array('varName'=>'hello', 'varName2'=>42));
     *    --> returns '/fixedRoutePart/hello/moreFixed/42'
     *
     * Throws BadRouteException if $routename is not known.
     *
     * Throws BadRouteException if the parameters given don't match any
     * possible branch for this route.
     *
     * At implementation option, may throw a BadRouteException
     * if the provided values, converted to strings with the PHP defaults,
     * don't match the respective regexes.
     *
     * @param string $routename Which route to generate
     * @param mixed[] $values (optional) Values for the placeholders, 
     *                if any, in the route.
     * 
     * @return string The route URL.
     */
    public function gen($routename, $values = []);
}
