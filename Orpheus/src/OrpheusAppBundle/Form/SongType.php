<?php

namespace OrpheusAppBundle\Form;

use OrpheusAppBundle\Entity\Artist;
use OrpheusAppBundle\Entity\Genre;
use OrpheusAppBundle\Entity\Song;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('coverArtUrl')
            ->add('songUrl')
            ->add('playedCount')
            ->add('artist',EntityType::class,[
                'class' => Artist::class,
                'choice_label' => 'name',
            ])
            ->add('genre',EntityType::class,[
                'class' => Genre::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Song::class,
        ]);
    }
}
