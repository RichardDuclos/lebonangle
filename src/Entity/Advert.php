<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: AdvertRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(denormalizationContext: ['groups' => ['write']])
    ],
    normalizationContext: ['groups' => ['read']]
)]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(OrderFilter::class, properties: ['publishedAt', 'price'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(SearchFilter::class, properties: ['category' => 'exact'])]
class Advert implements TimestampableInterface
{
    use TimestampableTrait;

    #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner le titre de votre annonce')]
    #[Assert\Length(max: 50, maxMessage: 'Le titre ne peut de l\'annonce doit faire moins de 50 caractères')]
    #[Groups(['write'])]
    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner le contenu de votre annonce')]
    #[Assert\Length(max: 500, maxMessage: 'Le titre ne peut de l\'annonce doit faire moins de 500 caractères')]
    #[Groups(['write', 'read'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[Assert\NotBlank(message: 'Votre renseigner le nom de l\'auteur de l\'annonce')]
    #[Assert\Length(max: 50, maxMessage: 'Le nom de l\'auteur doit faire moins de 50 caractères')]
    #[Groups(['write', 'read'])]
    #[ORM\Column(length: 50)]
    private ?string $author = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner votre adresse e-mail')]
    #[Assert\Length(max: 100, maxMessage: 'Votre adresse e-mail doit faire 100 caractères ou moins')]
    #[Assert\Email(message: 'Veuillez renseigner une adresse e-mail valide')]
    #[Groups(['write', 'read'])]
    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[Assert\NotNull(message: 'Veuillez renseigner une catégorie')]
    #[Groups(['write', 'read'])]
    #[ORM\ManyToOne(inversedBy: 'adverts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[Assert\NotBlank(message: 'Veuillez renseigner un prix')]
    #[Assert\Range(notInRangeMessage: 'Le prix doit être compris entre 0 et 100 000', min: 0, max: 100000)]
    #[Assert\Type(type: 'float', message: 'Veuillez renseigner un nombre entier ou décimal')]
    #[Groups(['write', 'read'])]
    #[ORM\Column]
    private ?float $price = null;

    #[Assert\Choice(choices: ['draft', 'published', 'rejected'], message: 'Valeur incorrecte')]
    #[Groups(['read'])]
    #[ORM\Column(length: 255)]
    private ?string $state = "draft";

    #[Groups(['read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[Groups(['read', 'write'])]
    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Picture::class)]
    private Collection $pictures;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setAdvert($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAdvert() === $this) {
                $picture->setAdvert(null);
            }
        }

        return $this;
    }


}
