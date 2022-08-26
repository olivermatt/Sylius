<?php

declare(strict_types = 1);

namespace ModenaEstonia\SyliusModenaPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ModenaGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('testing', CheckboxType::class, ['label' => 'Testing?']);
        $builder->add('merchant_registry_code', TextType::class, ['label' => 'Merchant Registry Code']);
        $builder->add('Modena_public_key', TextareaType::class, ['label' => 'Modena Public Key']);
        $builder->add('merchant_private_key', TextareaType::class, ['label' => 'Merchant Private Key']);
    }
}