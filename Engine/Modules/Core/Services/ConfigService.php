<?php

namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Models\Config\Element;
use Oforge\Engine\Modules\Core\Models\Config\Value;

class ConfigService
{
    /**
     * Insert a Config Entry into the database
     *
     * "name" => "",
     * "label" => "",
     * "type" => "boolean" | "string" | "number" | "integer" | "select",
     * "required" => true | false,
     * "options" => ["", ...],
     * "default" => ""
     *
     *
     * @param array $options
     *
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Array $options)
    {
        if ($this->isValid($options)) {

            $element = Element::create($options);
            $em = Oforge()->DB()->getEnityManager();

            $em->persist($element);
            $em->flush();

            if (isset($options["default"])) {
                $element->setValues([
	                Value::create(["value" => $options["default"], "element" => $element])
                ]);

                $em->persist($element);
                $em->flush();
            }
        }
    }

    /**
     * Update a set of config entries
     *
     * @param array $options
     *
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(Array $options)
    {
        /**
         * Check if the element is already within the system
         */

        $em = Oforge()->DB()->getEnityManager();
        $repo = $em->getRepository(Element::class);

        $element = $repo->findOneBy(["name" => strtolower($options["name"])]);
        if (isset($element)) {
            $this->updateConfig($element, $options);
        } else {
            $this->add($options);
        }
    }

    /**
     * Update a config entry
     *
     * @param Element $elm
     * @param array $options
     */
    private function updateConfig(Element $elm, Array $options)
    {
        // TODO: update config
    }

    /**
     * Check if the options are valid
     *
     * @param array $options
     *
     * @return bool
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     */
    private function isValid(Array $options)
    {
        /**
         * Check if required keys are within the options
         */
        $keys = ["name", "label", "type", "group"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) throw new ConfigOptionKeyNotExistsException($key);
        }

        /**
         * Check if the element is already within the system
         */
        $repo = Oforge()->DB()->getEnityManager()->getRepository(Element::class);

        $element = $repo->findOneBy(["name" => strtolower($options["name"])]);
        if (isset($element)) throw new ConfigElementAlreadyExistsException(strtolower($options["name"]));

        /**
         * Check if required keys are within the options
         */
        $types = ["boolean", "string", "number", "integer", "select"];

        if (!in_array($options["type"], $types)) throw new InvalidArgumentException("Type " . $options['type'] . " is not a valid type.");

        /**
         * Check if correct type are set
         */
        if (isset($options["required"]) && !is_bool($options["required"])) throw new InvalidArgumentException("Required value should be of type bool. ");
        if (isset($options["options"]) && !is_array($options["options"])) throw new InvalidArgumentException("Options value should be of type array. ");
        if (isset($options["position"]) && !is_integer($options["position"])) throw new InvalidArgumentException("Position value should be of type integer. ");

        /**
         * Check if correct type are set
         */
        $keys = ["name", "label", "description", "group"];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) throw new InvalidArgumentException("$key value should be of type string.");
        }

        return true;
    }

    /**
     * Remove Options
     */
    public function remove()
    {
        // TODO: implement remove function
    }

    /**
     * Get a specific configuration value
     *
     * @param string $key
     * @param integer $scope
     *
     * @return mixed
     * @throws ConfigElementNotFoundException
     * @throws ORMException
     */
    public function get(string $key, ?integer $scope = null)
    {
        $em = Oforge()->DB()->getEnityManager();
        $repo = $em->getRepository(Element::class);
        $element = $repo->findBy(["name" => $key]);

        if (!isset($element) || sizeof($element) == 0) throw new ConfigElementNotFoundException($key, $scope);

        foreach ($element[0]->getValues() as $value) {
            /** @var Value $value */
            if ($value->getScope() == $scope) {
                return $value->getValue();
            }
        }

        throw new ConfigElementNotFoundException($key, $scope);
    }

    /**
     * Set a specific configuration
     *
     * @param string $key
     * @param $configvalue
     * @param int|null $scope
     *
     * @return bool
     * @throws ConfigElementNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function set(string $key, $configvalue, int $scope = null)
    {
        $em = Oforge()->DB()->getEnityManager();
        $repo = $em->getRepository(Element::class);
        $element = $repo->findBy(["name" => $key]);

        if (!isset($element)) throw new ConfigElementNotFoundException($key, $scope);

        foreach ($element[0]->getValues() as $value) {
            /** @var Value $value */
            if ($value->getScope() == $scope) {
                $value->setValue($configvalue);

                $em->persist($element[0]);
                $em->flush();

                return true;
            }
        }
        throw new ConfigElementNotFoundException($key, $scope);
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function groups() {
        $em = Oforge()->DB()->getEnityManager();
        $query = $em->createQueryBuilder()
            ->select("e.group")
            ->from(Element::class, "e")
            ->distinct(true);
        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param $groupName
     *
     * @return array|object[]
     * @throws ORMException
     */
    public function list($groupName) {
        $em = Oforge()->DB()->getEnityManager();
        $elements = $em->getRepository(Element::class)->findBy(array('group' => $groupName));
        return $elements;
    }
}
