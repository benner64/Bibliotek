<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[Groups(['search'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['search'])]
    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    private Collection $Author;

    #[ORM\Column(nullable: true)]
    private ?int $Pages = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Publisher $Publisher = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $PublishYear = null;

    // #[ORM\ManyToMany(targetEntity: Genre::class, mappedBy: 'Books')]
    // private Collection $genres;

    #[ORM\ManyToOne(inversedBy: 'Books')]
    private ?Series $series = null;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'books')]
    private Collection $Genres;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $CoverImageFile = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $InLibrary = null;

    public function __construct()
    {
        $this->Author = new ArrayCollection();
        // $this->genres = new ArrayCollection();
        $this->Genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthor(): Collection
    {
        return $this->Author;
    }

    public function addAuthor(Author $author): static
    {
        if (!$this->Author->contains($author)) {
            $this->Author->add($author);
        }

        return $this;
    }

    public function removeAuthor(Author $author): static
    {
        $this->Author->removeElement($author);

        return $this;
    }

    public function getPages(): ?int
    {
        return $this->Pages;
    }

    public function setPages(?int $Pages): static
    {
        $this->Pages = $Pages;

        return $this;
    }

    public function getPublisher(): ?Publisher
    {
        return $this->Publisher;
    }

    public function setPublisher(?Publisher $Publisher): static
    {
        $this->Publisher = $Publisher;

        return $this;
    }

    public function getPublishYear(): ?\DateTimeInterface
    {
        return $this->PublishYear;
    }

    public function setPublishYear(?\DateTimeInterface $PublishYear): static
    {
        $this->PublishYear = $PublishYear;

        return $this;
    }

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setSeries(?Series $series): static
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->Genres;
    }

    public function addGenre(Genre $genre): static
    {
        if (!$this->Genres->contains($genre)) {
            $this->Genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static
    {
        $this->Genres->removeElement($genre);

        return $this;
    }

    public function getCoverImageFile(): ?string
    {
        return $this->CoverImageFile;
    }

    public function setCoverImageFile(?string $CoverImageFile): static
    {
        $this->CoverImageFile = $CoverImageFile;

        return $this;
    }

    public function isInLibrary(): ?bool
    {
        return $this->InLibrary;
    }

    public function setInLibrary(bool $InLibrary): static
    {
        $this->InLibrary = $InLibrary;

        return $this;
    }
}
