<?php

namespace LocalsBest\AdminBundle\Form;

use LocalsBest\UserBundle\Entity\ProductImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductImageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderNumber', Type\NumberType::class, [
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $image = $event->getData();
            $form = $event->getForm();

            // check if the Contact object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Product"
            if (!$image || null === $image->getId()) {
                $form->add('imageFile', Type\FileType::class, [
                    'attr' => [
                        'accept' => 'image/jpeg, image/gif, image/png'
                    ],
                    'required' => false
                ]);
            } else {
                $form->add('imageFile', Type\FileType::class, [
                    'attr' => [
                        'accept' => 'image/jpeg, image/gif, image/png'
                    ],
                    'required' => false
                ]);
            }
        });
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProductImage::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_userbundle_productimage';
    }
}
