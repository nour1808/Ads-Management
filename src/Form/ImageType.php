<?php

namespace App\Form;

use App\Entity\Image;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageType extends ApplicationType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', UrlType::class, $this->getConfiguration('URL', 'Tapez votre URL de votre image'))
            ->add('caption', TextType::class, $this->getConfiguration('CAption', 'Tapez votre CAption'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
