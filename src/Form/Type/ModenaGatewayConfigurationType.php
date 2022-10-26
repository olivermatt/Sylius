<?php

declare(strict_types=1);
          
namespace Modena\PaymentGatewayPlugin\Form\Type;

///namespace Acme\SyliusExamplePlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
            
final class ModenaGatewayConfigurationType extends AbstractType
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
                        'Swedbank' => 'HABAEE2X',
                        'LHV' => 'LHVBEE22',
                        'SEB' => 'EEUHEE2X',
                        'Luminor' => 'NDEAEE2X',
                        'Coop' => 'EKRDEE22',
                        'Citadele' => 'PARXEE22',
                        'Visa & Mastercard' => 'CARD',
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



