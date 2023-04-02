<?php

namespace App\Providers;

use Closure;
use FilesystemIterator;
use Generator;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use RegexIterator;
use Support\Attributes\Router\ClassParser\ClassParser;
use Support\Attributes\Router\Constraints\Where;
use Support\Attributes\Router\Options\Option;
use Support\Attributes\Router\Route;
use Support\Attributes\Router\RouteGroup;
use Support\Attributes\Router\Shortcuts\Shortcut;

class RouteAttributesServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register() : void
    {
        $this->booted(function () {
            if (! $this->app->routesAreCached()) {
                $this->createRoutes();
            }
        });
    }

    /**
     * @return void
     *
     * @throws ReflectionException
     */
    private function createRoutes() : void
    {
        foreach ($this->getClasses() as $fullClass) {
            $class = new ReflectionClass($fullClass);
            if ($class->isAbstract()) {
                continue;
            }

            $extendedClasses = [];

            self::getExtendedList($class, $extendedClasses);

            $classStack = array_reverse($extendedClasses);

            $classStack[] = $class;

            $routes = function () use ($class) {
                foreach ($this->getClassRouteActions($class) as $action) {
                    $route = Router::{$action['method']}($action['attrs']['uri'], $action['attrs']);
                    $this->appendConstraints($route, $action['constraints']);
                }
            };

            self::wrapRoutes($classStack, $routes)();
        }
    }

    /**
     * @return Generator
     */
    private function getClasses() : Generator
    {
        $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('src/App/Api'), FilesystemIterator::SKIP_DOTS));
        $regexIterator = new RegexIterator($directoryIterator, '/Controller\.php$/');

        foreach ($regexIterator as $phpFile) {
            $path = $phpFile->getRealPath();

            if (! is_file($path)) {
                continue;
            }

            $classParser = new ClassParser(file_get_contents($path));

            foreach ($classParser->getClasses() as $class) {
                yield $class;
            }
        }

        $directoryIterator->endIteration();
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param array $stack
     *
     * @return void
     *
     * @throws ReflectionException
     */
    public static function getExtendedList(ReflectionClass $reflectionClass, array &$stack) : void
    {
        $extensionClassName = get_parent_class($reflectionClass->getName());

        if (! $extensionClassName) {
            return;
        }

        $extensionClass = new ReflectionClass($extensionClassName);
        $stack[] = $extensionClass;

        self::getExtendedList($extensionClass, $stack);
    }

    /**
     * @param ReflectionClass $class
     *
     * @return Generator
     */
    private function getClassRouteActions(ReflectionClass $class) : Generator
    {
        foreach ($class->getMethods() as $method) {
            $attributes = $method->getAttributes();

            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();

                if ($attributeInstance instanceof Route) {
                    $routeAttrs = $attributeInstance->toArray();

                    $routeAttrs['uses'] = join('@', [$class->getName(), $method->getName()]);
                    $routeAttrs = self::getAdditionalAttributes($routeAttrs, $attributes);
                    $routeConstraints = self::getConstraintsFromAttributes($attributes);

                    yield [
                        'method'      => $attributeInstance->method,
                        'attrs'       => $routeAttrs,
                        'constraints' => $routeConstraints
                    ];
                }
            }
        }
    }

    /**
     * @param array $parsedAttributes
     * @param ReflectionAttribute[] $attributes
     *
     * @return array
     */
    private static function getAdditionalAttributes(array $parsedAttributes, array $attributes) : array
    {
        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();

            if ($attributeInstance instanceof Option) {
                $attributeInstance($parsedAttributes);
            }

            if ($attributeInstance instanceof Shortcut) {
                $attributeInstance($parsedAttributes);
            }
        }

        return $parsedAttributes;
    }

    /**
     * @param ReflectionAttribute[] $attributes
     *
     * @return array
     */
    private static function getConstraintsFromAttributes(array $attributes) : array
    {
        $constraints = [];

        foreach ($attributes as $attribute) {
            $attributeInstance = $attribute->newInstance();
            if (! ($attributeInstance instanceof Where)) {
                continue;
            }

            $constraints[] = $attributeInstance->toArray();
        }

        return $constraints;
    }

    /**
     * @param IlluminateRoute $route
     * @param array $constraints
     *
     * @return void
     */
    private function appendConstraints(IlluminateRoute $route, array $constraints) : void
    {
        foreach ($constraints as $constraint) {
            $route->where(...$constraint);
        }
    }

    /**
     * @param ReflectionClass[] $stack
     * @param Closure $routes
     * @param int $current
     *
     * @return Closure
     */
    public static function wrapRoutes(array $stack, Closure $routes, int $current = 0) : Closure
    {
        $currentClass = $stack[$current++] ?? null;

        if (! $currentClass) {
            return $routes;
        }

        $routeGroupClassAttr = $currentClass->getAttributes(RouteGroup::class)[0] ?? null;

        if (! $routeGroupClassAttr) {
            return self::wrapRoutes($stack, $routes, $current);
        }

        $routeGroupClassAttrInstance = $routeGroupClassAttr->newInstance();

        assert($routeGroupClassAttrInstance instanceof RouteGroup);

        $groupAttrs = self::getAdditionalAttributes($routeGroupClassAttrInstance->toArray(), $currentClass->getAttributes());

        return function () use ($groupAttrs, $stack, $routes, $current) {
            Router::group($groupAttrs, self::wrapRoutes($stack, $routes, $current));
        };
    }
}
