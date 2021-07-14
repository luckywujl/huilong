<?php

namespace app\admin\model\financial;

use think\Model;


class Charge extends Model
{

    

    

    // 表名
    protected $name = 'financial_charge';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'charge_type_text'
    ];
    

    
    public function getChargeTypeList()
    {
        return ['0' => __('Charge_type 0'), '1' => __('Charge_type 1')];
    }


    public function getChargeTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['charge_type']) ? $data['charge_type'] : '');
        $list = $this->getChargeTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }

	 public function customcustom()
    {
        return $this->belongsTo('app\admin\model\custom\Custom', 'charge_custom_id', 'custom_id', [], 'LEFT')->setEagerlyType(0);
    }


}
