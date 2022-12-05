<?php

namespace App\Entity;

use App\Repository\WayRepository;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WayRepository::class)]
class Way
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $team = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $date_start = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_end = null;

    #[ORM\Column]
    private array $route_json = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): array
    {
        return $this->team;
    }

    public function setTeam(array $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeImmutable $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeImmutable $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getRouteJson(): array
    {
        return $this->route_json;
    }

    public function setRouteJson(Array $route_json): self
    {
        $this->route_json = $route_json;

        return $this;
    }
}
