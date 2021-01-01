<?php


namespace App\Controller\Api\Task;


use App\Entity\Task;
use Symfony\Component\Security\Core\Security;

class TaskCreateController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Task $data)
    {
        $data->setUser($this->security->getUser());
        return $data;
    }
}
