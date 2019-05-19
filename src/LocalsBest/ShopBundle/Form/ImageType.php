<?php

namespace LocalsBest\ShopBundle\Form;

use LocalsBest\ShopBundle\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ImageType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderNumber', Type\TextType::class, [
                'label' => 'Order Number'
            ])
            ->add('file', VichFileType::class, [
                'required'      => false,
                'allow_delete'  => false,
                'label'         => false,
                'attr' => [
                    'accept' => 'image/jpeg, image/gif, image/png'
                ],
            ]);
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Image::class
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'localsbest_shopbundle_image';
    }
}
