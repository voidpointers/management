<?php

namespace Receipt\Transforms;

class Transformer
{
    private static $instance;

    protected static $transformer;

    public function __construct() {}

    public static function instance($transformer)
    {
        switch ($transformer) {
            case 'consignee':
                self::$instance = new ConsigneeTransformer;
            break;
            case 'transaction':
                self::$instance = new TransactionTransformer;
            break;
            default:
                self::$instance = new ReceiptTransformer;
        }

        return self::$instance;
    }
}
