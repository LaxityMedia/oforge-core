<?php

namespace Oforge\Engine\Modules\Core\Abstracts;

/**
 * Class AbstractBootstrap
 * Specific Bootstrap classes are needed to either autoload Modules or Plugins
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractBootstrap {
    /**
     * @var string[] $commands
     */
    protected $commands = [];
    /**
     * @var string[] $cronjobs
     */
    protected $cronjobs = [];
    /**
     * @var array $dependencies
     */
    protected $dependencies = [];
    /**
     * @var string[] $endpoints
     */
    protected $endpoints = [];
    /**
     * @var array $middlewares
     */
    protected $middlewares = [];
    /**
     * @var string[] $models
     */
    protected $models = [];
    /**
     * @var array $services
     */
    protected $services = [];
    /**
     * @var int $order
     */
    protected $order = 1337;

    public function install() {
    }

    public function update() {
    }

    public function uninstall() {
    }

    public function activate() {
    }

    public function deactivate() {
    }

    public function load() {
    }

    /**
     * @return string[]
     */
    public function getCommands() {
        return $this->commands;
    }

    /**
     * @return string[]
     */
    public function getCronjobs() {
        return $this->cronjobs;
    }

    /**
     * @return array
     */
    public function getDependencies() : array {
        return $this->dependencies;
    }

    /**
     * @return array
     */
    public function getEndpoints() : array {
        return $this->endpoints;
    }

    /**
     * @return array
     */
    public function getMiddlewares() : array {
        return $this->middlewares;
    }

    /**
     * @return string[]
     */
    public function getModels() {
        return $this->models;
    }

    /**
     * @return array
     */
    public function getServices() : array {
        return $this->services;
    }

    /**
     * @return int
     */
    public function getOrder() : int {
        return $this->order;
    }

}
