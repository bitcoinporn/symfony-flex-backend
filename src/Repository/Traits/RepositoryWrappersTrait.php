<?php
declare(strict_types=1);
/**
 * /src/Repository/Traits/RepositoryWrappersTrait.php
 *
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 */
namespace App\Repository\Traits;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class RepositoryWrappersTrait
 *
 * @package App\Repository\Traits
 * @author  TLe, Tarmo Leppänen <tarmo.leppanen@protacon.com>
 *
 * @method string        getEntityName(): string
 */
trait RepositoryWrappersTrait
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * Gets a reference to the entity identified by the given type and identifier without actually loading it,
     * if the entity is not yet loaded.
     *
     * @param string $id
     *
     * @return Proxy|null
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function getReference(string $id): ?Proxy
    {
        return $this->getEntityManager()->getReference($this->getEntityName(), $id);
    }

    /**
     * Gets all association mappings of the class.
     *
     * @return array
     */
    public function getAssociations(): array
    {
        return $this->getEntityManager()->getClassMetadata($this->getEntityName())->getAssociationMappings();
    }

    /**
     * Getter method for EntityManager for current entity.
     *
     * @return EntityManager|ObjectManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->managerRegistry->getManagerForClass(static::$entityName);
    }

    /**
     * Method to create new query builder for current entity.
     *
     * @param string $alias
     * @param string $indexBy
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(string $alias = null, string $indexBy = null): QueryBuilder
    {
        $alias = $alias ?? 'entity';

        // Create new query builder
        $queryBuilder = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select($alias)
            ->from($this->getEntityName(), $alias, $indexBy);

        return $queryBuilder;
    }
}