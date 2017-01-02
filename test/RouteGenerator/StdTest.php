<?php

namespace FastRoute\RouteGenerator;

/**
 * Test \FastRoute\RouteGenerator\Std
 */
class StdTest extends \PhpUnit_Framework_TestCase {
    protected $routeData;
    protected $collector;

    public function populateRoutes(\FastRoute\RouteCollector $r) {
        $this->collector = $r;
        $r->addRoute('GET', '/users', 'get_all_users_handler', 'users');
        // {id} must be a number (\d+)
        $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler', 'user');
        // The /{title} suffix is optional
        $r->addRoute('GET',
            '/articles/{id:\d+}[/{title}]', 'get_article_handler', 'article');
        $r->addRoute('GET',
            '/fixedRoutePart/{varName}[/moreFixed/{varName2:\d+}]',
            'fixed_handler', 'fixed');
        $r->addRoute('GET', '/widget/{var1}/{var2}/bar',
            'widget_handler','widget');
    } //populateRoutes

    protected function setUp() {
        $dispatcher = \FastRoute\simpleDispatcher([$this,'populateRoutes']);
        $this->routeData = $this->collector->getParsedRoutes();
        #print_r($this->routeData);
    }

    /** @dataProvider provideTestGen */
    public function testGen($routeName, $vars, $expectedRouteString) {
        $gen = new Std($this->routeData);
        $routeString = $gen->gen($routeName, $vars);
        $this->assertSame($expectedRouteString, $routeString);
        //$this->assertTrue(true);
    }

    /** @dataProvider provideTestGenError */
    public function testGenError($routeName, $vars,
                                        $expectedExceptionMessage) {
        $gen = new Std($this->routeData);
        $this->setExpectedException('FastRoute\\BadRouteException',
                                        $expectedExceptionMessage);
        $routeString = $gen->gen($routeName, $vars);
    }

    public function provideTestGen() {
        return [
            'users' => [ 'users', [], '/users' ],
            'user42' => [ 'user', ['id'=>42], '/user/42'],
            'article1' => ['article',['id'=>1],'/articles/1'],
            'article999name' =>
                ['article',['id'=>999,'title'=>'foo'],'/articles/999/foo'],
            'fixed1' => ['fixed',['varName'=>'foo'],'/fixedRoutePart/foo'],
            'fixed2' => ['fixed',['varName'=>'foo','varName2'=>1337],
                '/fixedRoutePart/foo/moreFixed/1337'],
            'widget1' => ['widget',['var1'=>'alpha','var2'=>128],
                '/widget/alpha/128/bar'],
        ];
    }

    public function provideTestGenError() {
        return [
            'unknown-route'=>['foo',[],
                "Can't generate URL for unknown route foo"],
            'missing-only-parm'=>['user',[],
                "Incorrect parameters for user"],
            'wrong-parm-name'=>['user',['ID'=>42],
                "Incorrect parameters for user"],
            'insufficient-parms-1'=>['widget',['var1'=>42],
                "Incorrect parameters for widget"],
            'insufficient-parms-2'=>['widget',['var2'=>42],
                "Incorrect parameters for widget"],
            // TODO add tests for RouteGenerator\Std::shouldValidate
        ];
    }
}
