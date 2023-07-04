<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Colour = null;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'Genres')]
    private Collection $books;

    /*#[ORM\ManyToMany(targetEntity: Book::class, inversedBy: 'genres')]
    private Collection $Books;*/

    public function __construct()
    {
        //$this->Books = new ArrayCollection();
        $this->books = new ArrayCollection();
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

    public function getColour(): ?string
    {
        return $this->Colour;
    }

    public function setColour(?string $Colour): static
    {
        $this->Colour = $Colour;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    // public function getBooks(): Collection
    // {
    //     return $this->Books;
    // }

    // public function addBook(Book $book): static
    // {
    //     if (!$this->Books->contains($book)) {
    //         $this->Books->add($book);
    //     }

    //     return $this;
    // }

    // public function removeBook(Book $book): static
    // {
    //     $this->Books->removeElement($book);

    //     return $this;
    // }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->addGenre($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            $book->removeGenre($this);
        }

        return $this;
    }
}
