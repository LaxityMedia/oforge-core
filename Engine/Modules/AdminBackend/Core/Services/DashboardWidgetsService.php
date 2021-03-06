<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\AdminBackend\Core\Models\DashboardWidget;
use Oforge\Engine\Modules\AdminBackend\Core\Models\UserDashboardWidgets;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;

class DashboardWidgetsService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            "default" => DashboardWidget::class,
            "users"   => UserDashboardWidgets::class,
        ]);
    }

    /**
     * @param $widget
     *
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     */
    public function register($widget) {
        if ($this->isValid($widget)) {
            $instance = DashboardWidget::create($widget);
            $this->entityManager()->persist($instance);
            $this->entityManager()->flush();
        }
    }

    /**
     * @param $widgetName
     */
    public function unregister($widgetName) {
        //TODO
    }

    /**
     * @param $userId
     *
     * @return array
     * @throws ORMException
     */
    public function getUserWidgets($userId) : array {
        /** @var null|UserDashboardWidgets[] $result */
        $result = $this->repository("users")->findBy(["userId" => $userId], ['order' => 'ASC']);

        if (!isset($result) || sizeof($result) == 0) {
            $this->initUserWidgets($userId);
            $result = $this->repository("users")->findBy(["userId" => $userId], ['order' => 'ASC']);
        }

        $columns = [];
        if (isset($result)) {
            /** @var DashboardWidget[] $widgets */
            $widgets    = $this->repository()->findAll();
            $widgetsMap = [];

            if (isset($widgets)) {
                foreach ($widgets as $widget) {
                    $widgetsMap[$widget->getId()] = $widget->toArray();
                }
            }

            foreach ($result as $entry) {
                if (isset($widgetsMap[$entry->getWidgetId()])) {
                    if (!isset($columns[$entry->getPosition()])) {
                        $columns[$entry->getPosition()] = [];
                    }

                    array_push($columns[$entry->getPosition()], $widgetsMap[$entry->getWidgetId()]);
                }
            }
        }

        return $columns;
    }

    /**
     * @param $userId
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function initUserWidgets($userId) {
        $widgets = $this->repository()->findAll();

        $position = 0;
        foreach ($widgets as $widget) {
            $userWidget = UserDashboardWidgets::create(["userId"   => $userId,
                                                        "widgetId" => $widget->getId(),
                                                        "order"    => $position++,
                                                        "position" => $widget->getPosition(),
            ]);
            $this->entityManager()->persist($userWidget);
        }

        $this->entityManager()->flush();
    }

    public function getWidgetsData($name) {
        // Check if the element is already within the system
        $element = $this->repository()->findOneBy(["name" => strtolower($name)]);

        if (isset($element)) {
            $className = $element->getAction();
            if (method_exists($className, "getData")) {
                return (new $className())->getData();

            }
        }

        return [];
    }

    /**
     * @param $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     */
    private function isValid($options) {
        // Check if required keys are within the options
        $keys = ["name", "action"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistsException($key);
            }
        }

        // Check if the element is already within the system
        $element = $this->repository()->findOneBy(["name" => strtolower($options["name"])]);
        if (isset($element)) {
            return false;
        }

        //Check if correct type are set
        $keys = ["title", "icon", "action", "name"];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) {
                throw new InvalidArgumentException("$key value should be of type string.");
            }
        }

        return true;
    }
}
