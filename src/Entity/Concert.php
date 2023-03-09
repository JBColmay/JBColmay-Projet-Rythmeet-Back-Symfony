<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\ConcertRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=ConcertRepository::class)
 */
class Concert
{
    /**
     * @Groups({"concerts:read", "concert:read", "concert:list:me"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"concerts:read", "concert:read", "concert:list:me"})
     * 
     * @ORM\Column(type="string", length=64)
     */
    private $artist_name;

    /**
     * @Groups({"concerts:read", "concert:read", "concert:list:me"})
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @Groups({"concerts:read", "concert:read", "concert:list:me"})
     * @ORM\Column(type="string", length=64)
     */
    private $place;

    /**
     * @Groups({"concerts:read", "concert:read", "concert:list:me"})
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @Groups({"concerts:read", "concert:read"})
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Concert")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Groups({"concert:read", "concert:list:me"})
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="concerts")
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="concert")
     */
    private $Comment;

    public function __toString()
    {
        return (string) $this->getId();
    }

    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
        $this->Comment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtistName(): ?string
    {
        return $this->artist_name;
    }

    public function setArtistName(string $artist_name): self
    {
        $this->artist_name = $artist_name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

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

    /**
     * @return Collection<int, User>
     */
    public function getSubscribers(): Collection
    {
        return $this->subscribers;
    }

    public function addSubscriber(User $subscriber): self
    {
        if (!$this->subscribers->contains($subscriber)) {
            $this->subscribers[] = $subscriber;
        }

        return $this;
    }

    public function removeSubscriber(User $subscriber): self
    {
        $this->subscribers->removeElement($subscriber);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComment(): Collection
    {
        return $this->Comment;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->Comment->contains($comment)) {
            $this->Comment[] = $comment;
            $comment->setConcert($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->Comment->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getConcert() === $this) {
                $comment->setConcert(null);
            }
        }

        return $this;
    }
}
