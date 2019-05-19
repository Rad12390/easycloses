<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\BlankDocument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class BlankDocumentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $documents = $options['documents'];
        $business = $options['business'];
        $titleValue = $options['titleValue'];

        if($business !== null) {
            if($business->getTypes()->first()->getId() == 23) {
                $choices['listing'] = 'Listing';
                $choices['closing'] = 'Closing';
            }
        }
        $choices['general'] = 'General';

        $builder
            ->add('type', Type\ChoiceType::class, [
                'choices' => array_flip($choices),
                'placeholder' => false,
                'required'      => false,
            ])
            ->add('file', VichFileType::class, [
                'required'      => false,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => true, // not mandatory, default is true
            ])
            ->add('ableRoles', Type\ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label'    => 'For Roles',
                'choices' => [
                    'Manager' => 'ROLE_MANAGER',
                    'Assistant Manager' => 'ROLE_ASSIST_MANAGER',
                    'Team Leader' => 'ROLE_TEAM_LEADER',
                    'Agent' => 'ROLE_AGENT',
                    'Client' => 'ROLE_CLIENT',
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($documents, $titleValue) {
            $form = $event->getForm();

            if($titleValue != '' && !in_array($titleValue, array_keys($documents))) {
                $form->add('title', Type\TextType::class, [
                    'required' => false,
                ]);
            } else {
                $form->add('title', Type\ChoiceType::class, [
                    'choices' => array_flip($documents),
                    'required' => false,
                ]);
            }
        });
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlankDocument::class,
            'documents' => [],
            'business' => null,
            'titleValue' => '',
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_blankdocument';
    }
}
