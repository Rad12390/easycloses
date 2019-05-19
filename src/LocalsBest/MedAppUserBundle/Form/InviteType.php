<?php

namespace LocalsBest\UserBundle\Form;

use LocalsBest\UserBundle\Entity\Invite;
use LocalsBest\UserBundle\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $clientRoleAble = $options['clientRoleAble'];
        $teamRoleAble = $options['teamRoleAble'];

        $builder->add('email', Type\EmailType::class, [
            'attr' => [
                'class' => 'form-control placeholder-no-fix add_email',
                'placeholder' => 'Email',
                'autofocus' => ''
            ]
        ]);

        if ($user->getRole()->getRole() !== 'ROLE_ADMIN') {
            $builder->add('role', EntityType::class, [
                'class' => Role::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => '- Select a Role - ',
                'empty_data' => null,
                'attr' => array(
                    'class' => ' form-control',
                ),
                'query_builder' => function ($er) use ($user, $clientRoleAble, $teamRoleAble) {
                    $query = $er->createQueryBuilder('r')
                        ->where('r.level > ?1')
                        ->andWhere('r.role <> ?2')
                        ->setParameter(1, $user->getRole()->getLevel())
                        ->setParameter(2, 'ROLE_VENDOR')
                        ->addOrderBy('r.level', 'ASC');

                    if ($clientRoleAble === false) {
                        $query
                            ->andWhere('r.role <> :clientRole')
                            ->setParameter(':clientRole', 'ROLE_CLIENT')
                        ;
                    }

                    if ($teamRoleAble === false) {
                        $query
                            ->andWhere('r.role <> :teamRole')
                            ->setParameter(':teamRole', 'ROLE_TEAM_LEADER')
                        ;
                    }

                    return $query;
                }
            ]);
        } else {
            $builder->add('role', EntityType::class, [
                'class' => Role::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => '- Select a Role - ',
                'empty_data' => null,
                'attr' => array(
                    'class' => ' form-control',
                ),
                'query_builder' => function ($er) use ($user) {
                    return $er->createQueryBuilder('r')
                        ->addOrderBy('r.level', 'ASC');
                }
            ]);
        }
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invite::class,
            'user' => null,
            'clientRoleAble' => false,
            'teamRoleAble' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'invite';
    }
}
