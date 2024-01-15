<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Vote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('session_id', EntityType::class, [
            'class' => Session::class,
            'choice_label' => 'name',
            ])
            ->add('application_start')
            ->add('application_end')
            ->add('election_start')
            ->add('election_end')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vote::class,
        ]);
    }
}
