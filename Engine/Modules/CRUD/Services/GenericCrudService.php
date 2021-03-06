<?php
namespace Oforge\Engine\Modules\CRUD\Services;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use ReflectionException;

/**
 * Class GenericCrudService
 *
 * @package Oforge\Engine\Modules\CRUD\Services;
 */
class GenericCrudService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([]);
    }

    /**
     * @param string $class
     *
     * @return int
     * @throws ORMException
     * @throws NonUniqueResultException
     */
    public function count(string $class) : int {
        return $this->getRepository($class)->createQueryBuilder('e')->select('count(e.id)')->getQuery()->getSingleScalarResult();
    }

    /**
     * Get list of entities (data by toArray). If $params not empty, find entities by $params.
     *
     * @param string $class
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $offset
     * @param int|null $limit
     *
     * @return array
     * @throws ORMException
     */
    public function list(string $class, array $criteria = [], array $orderBy = null, $offset = null, $limit = null) : array {
        $repository = $this->getRepository($class);
        /** @var AbstractModel[] $entities */
        $entities = $repository->findBy($criteria, $orderBy, $limit, $offset);
        $result   = [];
        foreach ($entities as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    /**
     * Get single entity data (by toArray) or null if not exist.
     *
     * @param string $class
     * @param int $id
     *
     * @return array|null
     * @throws ORMException
     */
    public function getById(string $class, int $id) {
        $repo   = $this->getRepository($class);
        $result = $repo->findOneBy([
            'id' => $id,
        ]);
        if (isset($result)) {
            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * Create entity if not exist yet.
     * If options contains a key <b>id</b> and an entity with the id exists, an ConfigElementAlreadyExistsException is thrown.
     *
     * @param string $class
     * @param array $options
     *
     * @throws ConfigElementAlreadyExistsException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     */
    public function create(string $class, array $options) {
        $repository = $this->getRepository($class);

        if (isset($options['id'])) {
            $id     = $options['id'];
            $entity = $repository->findOneBy([
                'id' => $id,
            ]);
            if (isset($entity)) {
                throw new ConfigElementAlreadyExistsException("Entity with id '$id' already exists!");
            }
        }
        /** @var AbstractModel $instance */
        $instance = new $class();
        $instance = $instance->fromArray($options);

        $this->entityManager()->persist($instance);
        $this->entityManager()->flush($instance);
        $repository->clear();
    }

    /**
     * Update single entity or multiple entities.<br/>
     * If options contains the key <b>id</b>, a single entity is updated. If entity by id not exist, an NotFoundException is thrown.<br/>
     * If options contains the key <b>data</b>, multiple entities by id => data are updated.
     *
     * @param string $class
     * @param array $options
     *
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function update(string $class, array $options) {
        $repository = $this->getRepository($class);

        if (isset($options['id'])) {
            $id = $options['id'];
            unset($options['id']);
            $entity = $repository->findOneBy([
                'id' => $id,
            ]);
            if (!isset($entity)) {
                throw new NotFoundException("Entity with id '$id' not found!");
            }
            $entity->fromArray($options);
        } elseif (isset($options['data'])) {
            $objectsData = $options['data'];
            foreach ($objectsData as $id => $objectData) {
                // echo "<pre>", print_r($id, true), "</pre>";
                // echo "<pre>", print_r($objectData, true), "</pre>";
                $entity = $repository->findOneBy([
                    'id' => $id,
                ]);
                if (!isset($entity)) {
                    throw new NotFoundException("Entity with id '$id' not found!");
                }
                $entity->fromArray($objectData);
            }
        }
        $this->entityManager()->flush();
        $repository->clear();
    }

    /**
     * Delete entity by id.
     * If entity not exist, an NotFoundException is thrown.
     *
     * @param string $class
     * @param int $id
     *
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(string $class, int $id) {
        $repository = $this->getRepository($class);

        $entity = $repository->findOneBy([
            'id' => $id,
        ]);
        if (!isset($entity)) {
            throw new NotFoundException("Entity with id '$id' not found!");
        }
        $this->entityManager()->remove($entity);
        $this->entityManager()->flush();
        $repository->clear();
    }

}
