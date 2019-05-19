<?php
namespace LocalsBest\ShopBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class QuantityEnough extends Constraint
{
    public $message = 'Not enough quantity for: {{ object }} "{{ item }}".';

    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}