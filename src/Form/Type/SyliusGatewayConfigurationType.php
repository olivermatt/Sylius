<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class SyliusGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add(
            'environment',
            ChoiceType::class,
            [
                'choices' => [
                    'Live' => 'LIVE',
                    'Dev' => 'DEV'
                ],
                'label' => 'Modena environment',
            ]
            );

            $builder
            ->add(
                'product',
                ChoiceType::class,
                [
                    'choices' => [
                        'Swedbank' => 'SWEDBANK',
                        'LHV' => 'LHV',
                        'SEB' => 'SEB',
                        'Luminor' => 'LUMINOR',
                        'Cooop' => 'COOP',
                        'Citadele' => 'CITADELE',
                        'Pay-later' => 'PAY-LATER',
                        'Hire-Purchase' => 'HIRE-PURCHASE'

                    ],
                    'label' => 'Modena payment products',
                ]
                );

            $builder->add('client_id', TextType::class, ['label' => 'Client Id']);
            $builder->add('client_secret', TextType::class, ['label' => 'Client secret']);
    }
}



