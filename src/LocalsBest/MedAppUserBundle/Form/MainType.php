<?php

namespace LocalsBest\UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalsBest\UserBundle\Entity\IndustryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainType extends AbstractType
{
    protected $categories;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->categories = $options['categories'];

        $builder->add('businessType', EntityType::class, [
            'label' => 'Business Category',
            'class'     => IndustryType::class,
            'query_builder' => function(EntityRepository $repository) {
                if(count($this->categories) > 0){
                    $result = $repository->createQueryBuilder('t')->where('t.id IN (:categories)')->setParameter('categories', $this->categories)->orderBy('t.name', 'ASC');
                } else {
                    $result = $repository->createQueryBuilder('t')->orderBy('t.name', 'ASC');
                }
                return $result;
            },
            'choice_label'  => 'name',
            'placeholder' => 'All',
            'empty_data' => 'all',
            'attr' => array(
                'class' => 'form-control placeholder-no-fix',
                'style' => 'margin-left: 5px;',
             )
        ]);
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'categories' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'main';
    }
}
