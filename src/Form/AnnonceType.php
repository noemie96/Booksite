<?php

namespace App\Form;

use App\Entity\Books;
use App\Entity\Genres;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AnnonceType extends ApplicationType
{
    


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('titre','Le titre du livre'))
            ->add('author', TextType::class, $this->getConfiguration('auteur','Le nom de l\'auteur du livre'))
            ->add('resume', TextType::class, $this->getConfiguration('resume','Le résume du livre'))
            ->add('genres',EntityType::class, $this->getConfiguration('genres',false, [
                'class' => Genres::class,
                'choice_label' => function($genres){
                    return $genres->getName();
                },
                'multiple' => true
            ])
            )
            ->add('price', MoneyType::class, $this->getConfiguration('Prix du livre',"Donner le prix du livre"))
            ->add('image', UrlType::class, $this->getConfiguration('image de couverture','Url de votre image'))
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Books::class,
        ]);
    }
}
