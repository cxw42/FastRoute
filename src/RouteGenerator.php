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
     * Consider the route string
     * `'/fixedRoutePart/{varName}[/moreFixed/{varName2:\d+}]'`,
     * with name "my_route".  `{varName}` is interpreted as
     * a placeholder and `[...]` is interpreted as an optional route part.
     * This matches routes like `"/fixedRoutePart/foo"` and routes like
     * `"/fixedRoutePart/foo/moreFixed/42"`.  These two forms are referred
     * to as "branches".
     *
     * A route can be generated with this call:
     *
     * ```php
     * $generator = new RouteGenerator($parsedRoutes);
     * $generator->gen('my_route', array('varName'=>'hello'));
     *    --> returns '/fixedRoutePart/hello'
     * $generator->gen('my_route', array('varName'=>'hello', 'varName2'=>42));
     *    --> returns '/fixedRoutePart/hello/moreFixed/42'
     * ```
     *
     * Returns the longest branch that can be generated using the
     * given value array.  The "longest branch" is the one that matches
     * the most placeholders.
     *
     * Throws BadRouteException if $routename is not known.
     *
     * Throws BadRouteException if the parameters given don't satisfy any
     * possible branch for this route.
     *
     * At implementation option, may throw a BadRouteException
     * if the provided values, converted to strings with the PHP defaults,
     * don't match the respective regexes.
     * Implementations supporting this option shall inform calling code.
     *
     * @param string $routename Which route to generate
     * @param mixed[] $values (optional) Associative array holding values
     *                  for the placeholders, if any, in the route.
     *                  $values may contain items not referenced by the
     *                  current route.  That is not an error.
     *
     * @return string The route URL.
     */
    public function gen($routename, $values = []);
}
