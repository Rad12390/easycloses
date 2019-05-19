<?php
namespace LocalsBest\ShopBundle\Validator\Constraints;

use LocalsBest\ShopBundle\Entity\Combo;
use LocalsBest\ShopBundle\Entity\ComboSkuSet;
use LocalsBest\ShopBundle\Entity\Item;
use LocalsBest\ShopBundle\Entity\Package;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class QuantityEnoughValidator extends ConstraintValidator
{
    public function validate($protocol, Constraint $constraint)
    {
        $protocolQuantity = $protocol->getQuantity();

        if ($protocol instanceof Package) {
            foreach ($protocol->getSets() as $set) {
                /** @var Item $item */
                $itemTotalQuantity = 0;
                $item = $set->getItem();
                if($item){
                $itemTotalQuantity = $item->getQuantity();
                }
                $protocolItemQuantity = $protocolQuantity * $set->getQuantity();

                if ($itemTotalQuantity < $protocolItemQuantity) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ item }}', $set->getItem()->getTitle())
                        ->setParameter('{{ object }}', 'Item')
                        ->addViolation();
                }
            }
        } elseif ($protocol instanceof Combo) {
            /** @var ComboSkuSet $set */
            foreach ($protocol->getSkuSets() as $set) {
                $sku = $set->getSku();
                if (!is_null($sku->getPackage())) {
                    $package = $sku->getPackage();

                    $packageTotalQuantity = $package->getQuantity();
                    $protocolPackageQuantity = $protocolQuantity * $set->getQuantity();

                    if ($packageTotalQuantity < $protocolPackageQuantity) {
                        $this->context->buildViolation($constraint->message)
                            ->setParameter('{{ item }}', $package->getTitle())
                            ->setParameter('{{ object }}', 'Package')
                            ->addViolation();
                    }
                } elseif (!is_null($sku->getCombo())) {
                    $combo = $sku->getCombo();

                    $comboTotalQuantity = $combo->getQuantity();
                    $protocolComboQuantity = $protocolQuantity * $set->getQuantity();

                    if ($comboTotalQuantity < $protocolComboQuantity) {
                        $this->context->buildViolation($constraint->message)
                            ->setParameter('{{ item }}', $combo->getTitle())
                            ->setParameter('{{ object }}', 'Combo')
                            ->addViolation();
                    }
                }
            }
        }
    }
}