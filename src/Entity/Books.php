<?php

namespace App\Entity;


use App\Entity\User;
use App\Entity\Comment;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BooksRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BooksRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *  fields={"title"},
 *  message="Une autre livre possède déjà ce titre, merci de le modifier"
 * )
 */
class Books
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=255, minMessage="L\'auteur doit  faire plus de 10 caractères", maxMessage="L\auteur ne peut pas faire plus de 255 caractères")
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=100, minMessage="Votre résumé doit  faire plus de 20 caractères")
     */
    private $resume;


    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=255, minMessage="L\'auteur doit  faire plus de 10 caractères", maxMessage="L\auteur ne peut pas faire plus de 255 caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="relation", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="book", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Genres::class, inversedBy="properties")
     */
    private $genres;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }

    /**
     * Permet d'initialiser le slug automatiquement s'il n'est pas fourni 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function initializeSlug(){
        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

     /**
     * Permet de récup le notation moyenne de mon annonce
     */
    public function getAvgRatings()
    {
        // calculer la somme des notations
        // la fonction php array_reduce permet de réduire le tableau à une seule valeur (attention il faut un tableau et pas une array collection d'où l'utilisation de la méthode toArray()) - 2ème paramètre pour la fonction qui va donner chaque valeur à ajouter et le 3ème paramètre c'est la valeur par défaut
        $sum = array_reduce($this->comments->toArray(),function($total,$comment){
            return $total + $comment->getRating();
        },0);

        // faire la division pour avoir la moyenne (ternaire)
        if(count($this->comments) > 0 ) return $moyenne = round($sum / count($this->comments));

        return 0;
    }

    /**
     * Permet de récupérer le commentaire d'un utilisateur par rapport à une fiche livre
     *
     * @param User $utilisateur
     * @return Comment|null
     */
    public function getCommentFromUtilisateur (User $utilisateur)
    {
        foreach($this->comments as $comment){
            if($comment->getUtilisateur() === $utilisateur) return $comment;
        }

        return null;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }



    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setBooks($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getBooks() === $this) {
                $image->setBooks(null);
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBook($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBook() === $this) {
                $comment->setBook(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Genres[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genres $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
            $genre->addProperty($this);
        }

        return $this;
    }

    public function removeGenre(Genres $genre): self
    {
        if ($this->genres->removeElement($genre)) {
            $genre->removeProperty($this);
        }

        return $this;
    }
    
}
