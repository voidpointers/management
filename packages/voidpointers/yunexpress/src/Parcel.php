<?php

namespace Voidpointers\Yunexpress;

class Parcel
{
    /**
     * 包裹申报名称(英文) 必填
     *
     * @var string
     */
    public $eName;

    /**
     * 包裹申报名称(中文) 非必填
     *
     * @var string
     */
    public $cName;

    /**
     * 海关编码
     *
     * @var string
     */
    public $hsCode;

    /**
     * 申报数量 必填
     *
     * @var int
     */
    public $quantity;

    /**
     * 申报价格，单位USD 必填
     *
     * @var Decimal(18,2)
     */
    public $unitPrice;

    /**
     * 申报重量，单位kg 必填
     *
     * @var Decimal(18,3)
     */
    public $unitWeight;

    /**
     * 订单备注，用于打印配货单 非必填
     *
     * @var string
     */
    public $remark;

    /**
     * 产品链接地址 非必填
     *
     * @var string
     */
    public $productUrl;

    /**
     * 商品SKU，非必填
     *
     * @var string
     */
    public $SKU;

    /**
     * 配货信息
     * 
     * @var string
     */
    public $invoiceRemark;

    /**
     * 申报币种，默认：USD 必填
     * 
     * @var string
     */
    public $currencyCode;
}
