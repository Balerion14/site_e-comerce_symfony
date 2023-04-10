<?php
//Cette classe sert a rien, juste pour le test lors de la creation par default...
namespace App\Form;

use App\Entity\I23Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class I23UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('date_naissance')
            ->add('email')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => I23Utilisateur::class,
        ]);
    }
}
