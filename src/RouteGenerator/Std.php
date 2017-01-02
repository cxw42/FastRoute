<?php

namespace FastRoute\RouteGenerator;

use FastRoute\BadRouteException;
use FastRoute\RouteGenerator;

/**
 * Generate route strings
 *
 * E.g., gen('user', array('name'=>'hello')) -> '/user/hello'
 */
class Std implements RouteGenerator {
    private $parsedRoutes;  // from the RouteCollector
    private $shouldValidate;

    /**
     * Determine which placeholders a particular branch needs
     */
    private function valsNeededByBranch($branch) {
        $retval = [];
        foreach($branch as $piece) {
            if(is_array($piece)) {
                $retval[] = $piece[0];
            }
        }
        return $retval;
    } //valsNeededByBranch

    // === Public ========================================================

    /**
     * Creates a new generator
     *
     * @param array $parsedRoutes Parsed route data from RouteCollector.
     *              $parsedRoutes is a hash from route name to an array
     *              of "branches" indexed by number.  Each branch is an
     *              array of pieces of the route.
     * @param boolean $shouldValidate (optional, default false) If true, check
     *          provided parameters to make sure they fit the regexes for
     *          the placeholders.
     */
    public function __construct($parsedRoutes, $shouldValidate = false) {
        $this->parsedRoutes = $parsedRoutes;
        $this->shouldValidate = $shouldValidate;
    } //__construct

    public function gen($routename, $values = []) {
        if(!array_key_exists($routename, $this->parsedRoutes)) {
            throw new BadRouteException("Can't generate URL for unknown route $routename");
        }

        $haveVals = array_keys($values);  // which placeholders, if any, we have
        $routeData = $this->parsedRoutes[$routename];

        for($branchidx = count($routeData)-1; $branchidx >= 0; --$branchidx) {

            // Do we have the pieces we need?
            $valsNeeded = $this->valsNeededByBranch($routeData[$branchidx]);
            $matches = array_intersect($haveVals, $valsNeeded);
            if(count($matches) !== count($valsNeeded)) {
                continue;
            }

            // We do, so fill them in
            $retval = "";
            foreach($routeData[$branchidx] as $piece) {
                if(is_array($piece)) {
                    //TODO implement validation here
                    $retval .= (string)$values[$piece[0]];
                } else {
                    $retval .= $piece;
                }
            } //foreach $piece
            return $retval;     // Successful exit

        } //foreach route

        // If we get here, we didn't match.
        throw new BadRouteException("Incorrect parameters for $routename");
    } //gen()

}
