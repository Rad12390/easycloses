<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Buttons;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tooltips = $options['buttons'];

//        $result = [
//            'Social Media - Bucket statuses description' => 'Social Media - Bucket statuses description',
//            'Social Media - Auto post switch description' => 'Social Media - Auto post switch description',
//            'Social Media - Description of the bucket types.' => 'Social Media - Description of the bucket types.',
//            'Social Media - Scheduler description' => 'Social Media - Scheduler description',
//            'Social Media - Bulk status update description' => 'Social Media - Bulk status update description',
//            'Social Media - Listing Templates description' => 'Social Media - Listing Templates description',
//            'Social Media - Which articles fall under "All" tab' => 'Social Media - Which articles fall under "All" tab',
//            'Social Media - Which articles fall under "Scheduled" tab' => 'Social Media - Which articles fall under "Scheduled" tab',
//            'Social Media - Which articles fall under "Not Scheduled" tab' => 'Social Media - Which articles fall under "Not Scheduled" tab',
//            'Social Media - Which articles fall under "Posted" tab' => 'Social Media - Which articles fall under "Posted" tab',
//            'Social Media - Description what means "Goes to"' => 'Social Media - Description what means "Goes to"',
//            'Social Media - Post images upload' => 'Social Media - Post images upload',
//            'Social Media - Article/ listing statuses description' => 'Social Media - Article/ listing statuses description',
//        ];
//
//        foreach ($tooltips as $tooltip) {
//            if ($options['currentValue'] !== null && $options['currentValue'] == $tooltip->getName()) {
//                continue;
//            }
//
//            if ($key = array_search($tooltip->getName(), $result)) {
//                unset($result[$key]);
//            }
//        }

        $builder
            ->add('location', Type\TextType::class, [
                'label' => 'Location(Developer Text)',
                'required' => false,
            ])
            ->add('name', Type\TextareaType::class, [
                'label' => 'Label',
                'required' => true,
            ])
            ->add('link', Type\TextType::class, [
                'label' => 'Link',
                'required' => false,
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Buttons::class,
            'buttons' => [],
            'currentValue' => null,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_button';
    }
}
