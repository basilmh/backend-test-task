<?php
declare(strict_types=1);

namespace App\Requests;

use App\Requests\BaseRequest;
use App\Validator\Constraint\EntityExist;
use Symfony\Component\Validator\Constraints\AtLeastOneOf;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CalculatePriceRequest extends BaseRequest
{
    #[Type('integer')]
    #[NotBlank()]
    #[EntityExist("App\Entity\Product", 'id', 'Product "%property%" is wrong')]
    protected $product;

    #[Type('string')]
    #[NotBlank([])]
    protected $taxNumber;

    #[Type('string')]
    #[AtLeastOneOf([
        new EntityExist("App\Entity\Coupon", 'code', 'Coupon "%property%" is wrong'),
        new Blank(),
    ])]
    protected $couponCode;

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function getProduct(): int
    {
        return $this->product;
    }

    /**
     * @return mixed
     */
    public function getTaxNumber(): string
    {
        return $this->taxNumber;
    }
}
