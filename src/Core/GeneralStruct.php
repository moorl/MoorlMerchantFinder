<?php declare(strict_types=1);

namespace MoorlMerchantFinder\Core;

use Shopware\Core\Framework\Struct\Struct;

class GeneralStruct extends Struct
{
    public function __construct(array $values = [])
    {
        foreach ($values as $name => $value) {
            $this->$name = $value;
        }
    }
}
