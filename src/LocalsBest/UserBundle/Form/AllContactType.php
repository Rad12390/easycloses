<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\AllContact;
use LocalsBest\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $allowInvite = $options['allowInvite'];
        $this->staff = $options['staff'];

        $builder->add('firstName', Type\TextType::class, [
            'required' => false,
            'label' => 'First Name',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'First Name',
                'autofocus' => ''
            )
        ]);
        $builder->add('lastName', Type\TextType::class, [
            'required' => false,
            'label' => 'Last Name',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Last Name',
                'autofocus' => ''
            )
        ]);
        $builder->add('email', Type\TextType::class, [
            'required' => false,
            'label' => 'Email',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix add_email',
                'placeholder' => 'Email',
                'pattern'=> '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9._%+-]+\.[a-zA-Z]{2,}$',
                'autofocus' => ''
            )
        ]);
        $builder->add('number', Type\TextType::class, [
            'required' => false,
            'label' => 'Phone',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'placeholder' => 'Phone',
                'autofocus' => ''
            )
        ]);
        $builder->add('type', Type\ChoiceType::class, [
            'required' => false,
            'choices' => array(
                'Contact' => 'contact',
                'Lead' => 'lead',
            ),
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user, $allowInvite) {
            /** @var AllContact $contact */
            $contact = $event->getData();
            $form = $event->getForm();

            // check if the Contact object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$contact || null === $contact->getId() || $contact->getGeneratedBy() == $user || $user->getRole()->getLevel() == 4) {
                $form->add('source', Type\TextType::class, [
                    'required' => false,
                    'label_attr' => [
                        'style' => 'padding-left: inherit;',
                    ],
                    'label' => 'Source',
                    'attr' => array(
                        'class' => 'form-control placeholder-no-fix',
                        'placeholder' => 'Source',
                    )
                ]);
            }

            if (!$contact || null === $contact->getId()) {
                $form->add('generated_by', EntityType::class, [
                    'required' => false,
                    'label_attr' => [
                        'style' => 'padding-left: inherit;',
                    ],
                    'class' => User::class,
                    'choices' => $this->staff,
                    'choice_label' => function ($u) {
                        return $u->getFullName();
                    },
                    'placeholder' => '< me >',
                    'attr' => array(
                        'class' => 'form-control placeholder-no-fix',
                    )
                ]);

                if($allowInvite == true) {
                    $form->add('invitation', Type\CheckboxType::class, [
                        'label' => 'Send Portal Invitation',
                        'label_attr' => [
                            'style' => 'padding-left: inherit;',
                        ],
                        'required' => false,
                        'data' => true,
                        'attr' => array(
                            'class' => 'invite_contact',
                        )
                    ]);
                }
            } else {
                if($allowInvite == true) {
                    $form->add('invitation', Type\CheckboxType::class, [
                        'label' => 'Send Portal Invitation',
                        'label_attr' => [
                            'style' => 'padding-left: inherit;',
                        ],
                        'required' => false,
                        'data' => false,
                        'attr' => array(
                            'class' => 'invite_contact',
                        )
                    ]);
                }
            }
        });

        $builder->add('category', Type\ChoiceType::class, [
            'required' => false,
            'label_attr' => [
                'style' => 'padding-left: inherit;',
            ],
            'choices' => array(
                'Buyer' => 'buyer',
                'Seller' => 'seller',
                'Landlord' => 'landlord',
                'Tenant' => 'tenant',
            ),
            'placeholder' => 'N/A',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
            )
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AllContact::class,
            'user' => null,
            'staff' => null,
            'allowInvite' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'contact_all';
    }
}
