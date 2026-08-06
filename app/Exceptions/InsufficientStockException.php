<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(
        public readonly int $productId,
        public readonly int $available,
        public readonly int $requested,
    ) {
        parent::__construct("Insufficient stock for product {$productId}: requested {$requested}, available {$available}.");
    }
}
