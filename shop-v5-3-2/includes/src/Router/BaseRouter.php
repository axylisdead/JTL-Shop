<?php declare(strict_types=1);

namespace JTL\Router;

use InvalidArgumentException;
use League\Route\Route;

/**
 * Class BaseRouter
 * @package JTL\Router
 * @since 5.3.0
 */
class BaseRouter extends \League\Route\Router
{
    /**
     * optimized version of League\Route\Route\Router::getNamedRoute() with buildNameIndex() called only once
     * @param string $name
     * @return Route
     * @throws InvalidArgumentException
     */
    public function getNamedRoute(string $name): Route
    {
        if (!$this->routesPrepared) {
            $this->collectGroupRoutes();
            $this->buildNameIndex();
        }
        if (isset($this->namedRoutes[$name])) {
            return $this->namedRoutes[$name];
        }

        throw new InvalidArgumentException(\sprintf('No route of the name (%s) exists', $name));
    }
}
