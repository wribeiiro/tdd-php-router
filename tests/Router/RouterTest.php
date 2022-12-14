<?php

namespace WribeiiroTest\Router;

use PHPUnit\Framework\TestCase;
use Wribeiiro\Router\Router;

class RouterTest extends TestCase
{
    public function testRouterSetOfRoutes()
    {
        $_SERVER['REQUEST_URI'] = '/users';

        $router = new Router();

        $router->addRoute('/users', function () {
            return 'Primeira Rota!';
        });

        $result = $router->run();

        $this->assertEquals('Primeira Rota!', $result);
    }

    public function testValidateANoRouteFound()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage('Route Not Found');

        $_SERVER['REQUEST_URI'] = '/products';

        $router = new Router();
        $router->run();
    }

    public function testRouteWithAControllerAssociated()
    {
        $_SERVER['REQUEST_URI'] = '/products';

        $router = new Router();

        $router->addRoute('/products', '\\WribeiiroTest\\Controller\\ProductController@index');

        $result = $router->run();

        $this->assertEquals('Controller Product', $result);
    }

    public function testAWrongFormatToACallControllerAsASecondParameterOfTheOurRouter()
    {
        $this->expectException('\InvalidArgumentException');
        $this->expectExceptionMessage('Wrong format to call a controller!');

        $_SERVER['REQUEST_URI'] = '/products';

        $router = new Router();

        $router->addRoute('/products', '\\WribeiiroTest\\Controller\\ProductController');

        $router->run();
    }

    public function testThrowExcepetionWhenMethodDoesNotExistsInAController()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage('Method does not exists!');

        $_SERVER['REQUEST_URI'] = '/products';

        $router = new Router();

        $router->addRoute('/products', '\\WribeiiroTest\\Controller\\ProductController@getProduct');

        $router->run();
    }

    public function testRouteWithDynamicParameters()
    {
        $_SERVER['REQUEST_URI'] = '/users/10';

        $router = new Router();

        $router->addRoute('/users/{id}', function ($id) {
            return 'Rota com par??metro & par??metro ?? igual a ' . $id;
        });

        $router->addRoute('/products/{id}', '\\WribeiiroTest\\Controller\\ProductController@index');

        $result = $router->run();

        $this->assertEquals('Rota com par??metro & par??metro ?? igual a 10', $result);
    }

    public function testRouteWithPrefix()
    {
        $_SERVER['REQUEST_URI'] = '/users/edit/10';

        $router = new Router();

        $router->prefix('/users', function (Router $router) {
            $router->addRoute('/edit/{id}', function ($id) {
                return 'Rota com prefixo e id ' . $id;
            });
            $router->addRoute('/update/{id}', function ($id) {
                return 'Rota com update prefixo e id ' . $id;
            });
        });

        $result = $router->run();

        $this->assertEquals('Rota com prefixo e id 10', $result);
    }
}
