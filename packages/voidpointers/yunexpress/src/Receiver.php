<?php

namespace Voidpointers\Yunexpress;

class Receiver
{
    /**
     * 企业税号
     * 
     * @var string
     */
    public $TaxId;

    /**
     * 国家，填写国际通用标准2位简码，可通过国家查询服务查询 必填
     * 
     * @var string
     */
    public $CountryCode;

    /**
     * 姓 必填
     * 
     * @var string
     */
    public $FirtstName;

    /**
     * 名
     * 
     * @var string
     */
    public $LastName;

    /**
     * 公司
     * 
     * @var string
     */
    public $Company;

    /**
     * 详细地址 必填
     * 
     * @var string
     */
    public $Street;

    /**
     * 详细地址1
     * 
     * @var string
     */
    public $StreetAddress1;

    /**
     * 详细地址2
     * 
     * @var string
     */
    public $StreetAddress2;

    /**
     * 市 必填
     * 
     * @var string
     */
    public $City;

    /**
     * 州/省
     * 
     * @var string
     */
    public $State;

    /**
     * 邮编
     * 
     * @var string
     */
    public $Zip;

    /**
     * 电话
     * 
     * @var string
     */
    public $Phone;
}
