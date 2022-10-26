<?php

namespace Wribeiiro\Router;

class Router
{
    private array $routeCollection = [];
	private string $uriServer = '';
	private string $prefix = '';

	public function __construct()
	{
		$this->uriServer = $_SERVER['REQUEST_URI'];
	}

	public function addRoute($uri, $callable)
	{
		$uri = ltrim($uri, '/');
		$prefix = $this->prefix ? '/' . ltrim($this->prefix, '/') : '';

		$this->routeCollection[$prefix . '/' . $uri] = $callable;
	}

	public function prefix(string $prefix, $routeGroup): void
	{
		$this->prefix = $prefix;
		$routeGroup($this);
	}

	public function run(): mixed
	{
		//users/{id} <- funcao anonima
		$wildcard = new Wildcard();
		$wildcard->resolveRoute($this->uriServer, $this->routeCollection);

		if(!isset($this->routeCollection[$this->uriServer])) {
			throw new \Exception('Route Not Found');
		}

		$route = $this->routeCollection[$this->uriServer];

		if (is_callable($route)) {
			$parameters = $wildcard->getParameters();

			if (count($parameters)) {
				return $route($parameters[0]);
			}

			return $route();
		}

		if (is_string($route)) {
			return $this->controllerResolver($route);
		}
	}

    /**
     * Undocumented function
     *
     * @param string $route
     * @param array $parameters
     * @return mixed
     */
	private function controllerResolver(string $route, array $parameters = []): mixed
	{
		if (!strpos($route, '@')) {
			throw new \InvalidArgumentException('Wrong format to call a controller!');
		}

		list($controller, $method) = explode('@', $route);

		if (!method_exists(new $controller, $method)) {
			throw new \Exception('Method does not exists!');
		}

		return call_user_func_array([new $controller, $method], $parameters);
	}
}