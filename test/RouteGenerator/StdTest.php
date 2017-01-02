<?php

namespace FastRoute\RouteGenerator;

class StdTest extends \PhpUnit_Framework_TestCase {
    protected $routeData;
    protected $collector;

    public function populateRoutes(\FastRoute\RouteCollector $r) {
        $this->collector = $r;
        $r->addRoute('GET', '/users', 'get_all_users_handler', 'users');
        // {id} must be a number (\d+)
        $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler', 'user');
        // The /{title} suffix is optional
        $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler',
                        'article');
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
    public function TODO_testGenError($routeString, $expectedExceptionMessage) {
        $parser = new Std();
        $this->setExpectedException('FastRoute\\BadRouteException', $expectedExceptionMessage);
        $parser->parse($routeString);
    }

    public function provideTestGen() {
        return [
            'users' => [ 'users', [], '/users' ],
            'user42' => [ 'user', ['id'=>42], '/user/42'],
            'article1' => ['article',['id'=>1],'/articles/1'],
            'article999name' =>
                ['article',['id'=>999,'title'=>'foo'],'/articles/999/foo'],
        ];
    }

    public function provideTestGenError() {
        return [
            [
                '/test[opt',
                "Number of opening '[' and closing ']' does not match"
            ],
            [
                '/test[opt[opt2]',
                "Number of opening '[' and closing ']' does not match"
            ],
            [
                '/testopt]',
                "Number of opening '[' and closing ']' does not match"
            ],
            [
                '/test[]',
                "Empty optional part"
            ],
            [
                '/test[[opt]]',
                "Empty optional part"
            ],
            [
                '[[test]]',
                "Empty optional part"
            ],
            [
                '/test[/opt]/required',
                "Optional segments can only occur at the end of a route"
            ],
        ];
    }
}
