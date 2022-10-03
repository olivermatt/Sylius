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
                    'modena.payu_plugin.secure' => 'LIVE',
                    'modena.payu_plugin.sandbox' => 'DEV'
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
                        'modena.payu_plugin.swedbank' => 'SWEDBANK',
                        'modena.payu_plugin.lhv' => 'LHV',
                        'modena.payu_plugin.seb' => 'SEB',
                        'modena.payu_plugin.luminor' => 'LUMINOR',
                        'modena.payu_plugin.coop' => 'COOP',
                        'modena.payu_plugin.citadele' => 'CITADELE',
                        'modena.payu_plugin.paylater' => 'Pay-Later',
                        'modena.payu_plugin.hirepurchase' => 'Hire-Purchase'

                    ],
                    'label' => 'modena.payu_plugin.product',
                ]
                );

            $builder->add('client_id', TextType::class, ['label' => 'Client Id']);
            $builder->add('client_id', TextType::class, ['label' => 'Client secret']);
    }
}



