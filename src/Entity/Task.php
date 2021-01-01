<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\Api\Task\TaskCreateController;

use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ApiResource(
 *     denormalizationContext = { "groups" = { "task:write" } },
 *     normalizationContext =   { "groups" = { "task:read"  } },
 *     collectionOperations = {
 *         "get" = {
 *             "security" = "is_granted('ROLE_USER')",
 *             "security_message" = "Only users can list tasks",
 *         },
 *         "post" = {
 *             "security" = "is_granted('ROLE_USER')",
 *             "security_message" = "Only users can create a task",
 *              "denormalizationContext"={"groups"={"task:write"}},
 *              "controller"=TaskCreateController::class
 *         }
 *     }
 * )
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"task:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"task:read", "task:write"})
     */
    private $subject;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"task:read", "task:write"})
     */
    private $completed;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"task:read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"task:read", "task:write"})
     */
    private $user;

    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->completed = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(?bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
