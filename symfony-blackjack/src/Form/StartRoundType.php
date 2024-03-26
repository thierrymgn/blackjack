<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class StartRoundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentWallet = $options['currentWallet'];
        $builder
            ->add('wager', NumberType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                    new LessThanOrEqual($currentWallet),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'currentWallet' => 'currentWallet',
            'csrf_protection' => false,
        ]);
    }
}
