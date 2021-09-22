<?php

namespace App\Entity;

use App\Repository\UserNewsletterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserNewsletterRepository::class)
 */
class UserNewsletter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    /**
     * @ORM\ManyToOne(targetEntity="Newsletter")
     * @ORM\JoinColumn(name="news_id", referencedColumnName="id")
     */
    private $news;

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

    public function getNews(): ?Newsletter
    {
        return $this->news;
    }

    public function setNews(?Newsletter $news): self
    {
        $this->news = $news;

        return $this;
    }
    


}
