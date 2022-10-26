<?php

namespace Wribeiiro\Router;

class Wildcard
{
	private array $parameters = [];

    /**
     * Undocumented function
     *
     * @param string $uri
     * @param array $routeCollection
     * @return void
     */
	public function resolveRoute(string $uri, array &$routeCollection): void
	{
		$keysRouteCollection = array_keys($routeCollection);
		$routeWithParameters = [];

		foreach($keysRouteCollection as $route) {
			if (preg_match('/{(\w+?)\}/', $route)) {
				$routeWithParameters[] = $route;
			}
		}

		foreach($routeWithParameters as $route) {
			$routeWithoutParameter = preg_replace('/\/{(\w+?)\}/', '', $route); // /users/{id} -> /users
			$uriWithoutParameter   = preg_replace('/\/[0-9]+$/', '', $uri); // /users/10 -> /users

			if ($routeWithoutParameter === $uriWithoutParameter) {
				$routeCollection[$uri] = $routeCollection[$route];
				$this->parameters = $this->resolveParameter($uri);
			}
		}
	}

    /**
     * Undocumented function
     *
     * @return array
     */
	public function getParameters(): array
	{
		return $this->parameters;
	}

    /**
     * Undocumented function
     *
     * @param string $uri
     * @return array
     */
	private function resolveParameter(string $uri): array
	{
		$matches = [];

		preg_match('/[0-9]+$/', $uri, $matches);

		return $matches;
	}
}