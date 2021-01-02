# Symfony 5 + API Platform boilerplate

This is a boilerplate project that includes the basic features of API-Platform. You can use this code to start a new project or simply to understand the logic behind it.

Each part of my code represents a use case because the functionality itself can be used in multiple situations.

Use cases:

1. Retrieve only user objects
2. Display fields for a specific Role
3. Set the owner on object creating
4. Custom controllers

## Use cases

### 1. Retrieve only user objects

Imagine you have an entity called Task which has a field called user who is the author of this task. Then you want an authenticated user to see only the tasks that belong to him.
In this case, you have several possibilities (which I do not recommend) such as creating a specific controller for each entity linked to an owner (user), but it is a time-consuming method especially if you want to manage pagination.

That is why, I created an Extension for Doctrine in which I added a "Where" condition on the SQL queries for certain entities. I called this extension [CurrentUserExtension](/src/Doctrine/CurrentUserExtension.php) and it was inspired by the [API-platform documentation](https://api-platform.com/docs/core/extensions/#custom-doctrine-orm-extension).

```php
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
```

In case Task ($rootAlias) is "o", this will add "where o.user = 5" for user number 5. Then api-platform will apply its extensions for pagination.

### 2. Display fields for a specific Role

Imagine in the User entity where most of the fields can be managed by any user, but some can only be managed (read or write) by administrator users. For the example I have chosen, to a security matter, only the administrator can see the email address and edit roles and anyone can see the name.

```php
/**
 * @ORM\Column(type="string", length=180, unique=true)
 * @Groups({"user:write", "admin:read"})
 */
private $email;
/**
 * @ORM\Column(type="json")
 * @Groups({"admin:write", "admin:read"})
 */
private $roles = [];
/**
 * @ORM\Column(type="string", length=255)
 * @Groups({"user:read", "user:write"})
 */
private $name;
```

For that I used the serialization groups as indicated in the [api-platform documentation](https://api-platform.com/docs/core/serialization/#changing-the-serialization-context-dynamically) and in addition I implemented a [context builder](/src/Serializer/AdminContextBuilder.php) which adds the permission only to the admin role.
