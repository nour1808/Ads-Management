<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration('Prénom', 'Tapez votre prénom'))
            ->add('lastName', TextType::class, $this->getConfiguration('Nom de famille', 'Tapez votre nom de famille'))
            ->add('email', EmailType::class, $this->getConfiguration('Email', 'Tapez votre Email'))
            ->add('picture', UrlType::class, $this->getConfiguration('Photo de profile', 'URL de votre photo de profile'))
            ->add('hash', PasswordType::class, $this->getConfiguration('Mot de passe', 'Tapez votre Mot de passe'))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration('Confirmation de mot de passe', 'Retapez votre Mot de passe'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Présenez vous en queleques mots.'))
            ->add('description', TextareaType::class, $this->getConfiguration('Déscription détailée', 'c\'est le moment de vous présenter en détails!'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
