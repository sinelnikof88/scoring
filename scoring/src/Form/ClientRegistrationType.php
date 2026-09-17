<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Client;
use App\Enum\Education;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class ClientRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Имя',
                'constraints' => [
                    new Assert\NotBlank(message: 'Укажите имя'),
                    new Assert\Length(min: 2, max: 100),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия',
                'constraints' => [
                    new Assert\NotBlank(message: 'Укажите фамилию'),
                    new Assert\Length(min: 2, max: 100),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Номер телефона',
                'attr' => ['placeholder' => '+7 (___) ___-__-__'],
                'constraints' => [
                    new Assert\NotBlank(message: 'Укажите номер телефона'),
                    new Assert\Regex(
                        pattern: '/^(\+7|8)[\s\-\(\)]?\d{3}[\s\-\(\)]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/',
                        message: 'Введите российский номер в формате +7XXXXXXXXXX или 8XXXXXXXXXX',
                    ),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Э-почта',
                'constraints' => [
                    new Assert\NotBlank(message: 'Укажите э-почту'),
                    new Assert\Email(message: 'Некорректный адрес э-почты'),
                ],
            ])
            ->add('education', EnumType::class, [
                'class' => Education::class,
                'label' => 'Образование',
                'choice_label' => fn(Education $choice) => $choice->label(),
                'placeholder' => '— Выберите образование —',
                'constraints' => [
                    new Assert\NotNull(message: 'Выберите уровень образования'),
                ],
            ])
            ->add('consentGiven', CheckboxType::class, [
                'label' => 'Я даю согласие на обработку моих личных данных',
                'required' => true,
                'constraints' => [
                    new Assert\IsTrue(message: 'Необходимо согласие на обработку данных'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
