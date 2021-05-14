<?php

namespace app\admin\model\base;

use think\Model;


class Productprice extends Model
{

    

    

    // 表名
    protected $name = 'base_productprice';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'productprice_begin_time_text',
        'productprice_end_time_text'
    ];
    

    



    public function getProductpriceBeginTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['productprice_begin_time']) ? $data['productprice_begin_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getProductpriceEndTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['productprice_end_time']) ? $data['productprice_end_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setProductpriceBeginTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setProductpriceEndTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function baseproduct()
    {
        return $this->belongsTo('app\admin\model\base\Product', 'productprice_product_id', 'product_ID', [], 'LEFT')->setEagerlyType(0);
    }


    public function baseproducttype()
    {
        return $this->belongsTo('app\admin\model\base\Producttype', 'productprice_producttype_id', 'producttype_id', [], 'LEFT')->setEagerlyType(0);
    }
    
    public function customtype()
    {
        return $this->belongsTo('app\admin\model\custom\Customtype', 'productprice_customtype_id', 'customtype_ID', [], 'LEFT')->setEagerlyType(0);
    }
}
