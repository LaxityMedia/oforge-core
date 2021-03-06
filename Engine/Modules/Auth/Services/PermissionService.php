<?php

namespace Oforge\Engine\Modules\Auth\Services;

/**
 * Class Permissions
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class PermissionService {
    /** @var array $methods */
    private $methods = [];

    /**
     * @param string $class
     * @param string $method
     * @param string $userType
     * @param int $role
     */
    public function put(string $class, string $method, string $userType, ?int $role = null) {
        $key = $class . ':' . $method;

        $this->methods[$key] = ["class" => $class, "method" => $method, "type" => $userType, "role" => $role];
    }

    /**
     * @param string $class
     * @param string $method
     *
     * @return array|null
     */
    public function get(string $class, string $method) : ?array {
        $key = $class . ':' . $method;
        if (isset($this->methods[$key])) {
            return $this->methods[$key];
        }

        $instance = new $class();
        if (method_exists($instance, 'initPermissions')) {
            $instance->initPermissions();
        }

        return isset($this->methods[$key]) ? $this->methods[$key] : null;
    }

}
