<?php

namespace App\Form;

use App\Entity\Books;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class AnnonceEditType extends AbstractType
{
    private function getConfiguration($label,$placeholder, $options=[]){
        return array_merge([
            'label'=>$label,
            'attr'=> [
            'placeholder'=>$placeholder
            ]
        ], $options);
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('titre','Le titre du livre'))
            ->add('author', TextType::class, $this->getConfiguration('auteur','Le nom de l\'auteur du livre'))
            ->add('resume', TextType::class, $this->getConfiguration('resume','Le résume du livre'))
            ->add('genre',TextType::class, $this->getConfiguration('titre','Le titre du livre'))
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
