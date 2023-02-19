<?php

namespace App\Form;

use App\Entity\Advert;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function __construct(private CategoryRepository $categoryRepository) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->categoryRepository->findAll();
        $builder
            ->add('title')
            ->add('content')
            ->add('author')
            ->add('email')
            ->add('price')
            ->add('category', ChoiceType::class,
            [
                'choices' => $categories,
                'choice_value' => 'id',
                'choice_label' => 'name'
            ]
            )
            ->add('Submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
