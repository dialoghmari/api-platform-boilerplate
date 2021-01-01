<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Task;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;


// This extension allows you to add a condition on SQL queries in order to search for items linked to the current user
// On the other hand, the administrator can see all the items

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass, $operationName);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        // $this->addWhere($queryBuilder, $resourceClass, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, $operationName): void
    {
        if (!in_array($resourceClass, array(Task::class,"App\Entity\Task")) || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        switch ($operationName) {
            case "get":
                $queryBuilder->andWhere(sprintf('%s.user = :current_user', $rootAlias));
                $queryBuilder->setParameter('current_user', $user->getId());
                break;
            default:
                break;
        }
    }
}
