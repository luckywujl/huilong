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
      
    ];
    

    



    


    


    public function baseproduct()
    {
        return $this->belongsTo('app\admin\model\base\Product', 'productprice_product_id', 'product_ID', [], 'LEFT')->setEagerlyType(0);
    }


    public function baseproducttype()
    {
        return $this->belongsTo('app\admin\model\base\Producttype', 'productprice_producttype_id', 'producttype_id', [], 'LEFT')->setEagerlyType(0);
    }
    
    
}
