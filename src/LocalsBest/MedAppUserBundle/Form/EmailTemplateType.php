<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\EmailTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailTemplateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $template = $event->getData();
            $form = $event->getForm();

            // check if the Contact object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"

            if (!$template || null === $template->getId()) {
                $form->add('category', Type\ChoiceType::class, [
                    'required' => false,
                    'placeholder' => false,
                    'choices'  => array(
                        'Expired Listings' => 'Expired Listings',
                        'Non Received Listings' => 'Non Received Listings',
                        'Non Received Documents' => 'Non Received Documents',
                        'Shredding PDF' => 'Shredding PDF',
                    ),
                ])
                ->add('template_number', Type\ChoiceType::class, [
                    'required' => false,
                    'placeholder' => false,
                    'choices'  => array(
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                    ),
                ]);
            } else {
                $form->add('category', Type\HiddenType::class);
                $form->add('template_number', Type\HiddenType::class);
            }
        });
        $builder
            ->add('email_title', Type\TextType::class, [
                'required' => false,
            ])
            ->add('email_body', Type\TextareaType::class, [
                'required' => false,
                'attr' => array(
                    'autofocus' => true,
                    'class'=> 'wysihtml5',
                    'rows' => '6',
                )
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EmailTemplate::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_emailtemplate';
    }
}
