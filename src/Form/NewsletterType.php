<?php

namespace App\Form;

use App\Entity\Newsletter;
use App\Entity\UserNewsletter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('news',EntityType::class,[
            'class' => Newsletter::class,
            'choice_label' => function ($newsletter) {
                return $newsletter->getName();},
            'multiple' => false,
            'expanded' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserNewsletter::class,
        ]);
    }
}
