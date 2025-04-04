<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(inversedBy: 'reservation', targetEntity: Article::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $article;

    #[ORM\OneToOne(mappedBy: 'reservation', targetEntity: ConfirmReservations::class, cascade: ['persist', 'remove'])]
    private $confirmReservations;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getConfirmReservations(): ?ConfirmReservations
    {
        return $this->confirmReservations;
    }

    public function setConfirmReservations(ConfirmReservations $confirmReservations): self
    {
        // set the owning side of the relation if necessary
        if ($confirmReservations->getReservation() !== $this) {
            $confirmReservations->setReservation($this);
        }

        $this->confirmReservations = $confirmReservations;

        return $this;
    }
}
