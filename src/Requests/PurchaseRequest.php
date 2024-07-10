<?php

declare(strict_types=1);

namespace App\Requests;

use App\Validator\Constraint\EntityExist;
use App\Validator\Constraint\TaxExist;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class PurchaseRequest extends BaseRequest
{
    #[Type('integer')]
    #[NotBlank()]
    #[EntityExist("App\Entity\Product", 'id', 'Product %property% is wrong')]
    protected $product;

    #[Type('string')]
    #[NotBlank([])]
    #[TaxExist()]
    protected $taxNumber;

    #[Type('string')]
    #[AtLeastOneOf([
        new Blank([]),
        new EntityExist("App\Entity\Coupon", 'code', 'Coupon %property% is wrong'),
    ])]
    protected $couponCode;

    #[Type('string')]
    #[Choice(['paypal', 'stripe'])]
    #[NotBlank([])]
    protected $paymentProcessor;

    public function getProduct(): int
    {
        return $this->product;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }

    public function getPaymentProcessor(): string
    {
        return $this->paymentProcessor;
    }
}
