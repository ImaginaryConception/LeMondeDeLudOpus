<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $title;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $author;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $edition;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private $genre;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $other;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $price;

    #[ORM\Column(type: 'text', nullable: true)]
    private $photo;

    #[ORM\OneToOne(mappedBy: 'article', targetEntity: Reservation::class, cascade: ['persist', 'remove'])]
    private $reservation;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, orphanRemoval: true)]
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function setEdition(string $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function setOther(?string $other): self
    {
        $this->other = $other;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): self
    {
        // set the owning side of the relation if necessary
        if ($reservation->getArticle() !== $this) {
            $reservation->setArticle($this);
        }

        $this->reservation = $reservation;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

}
