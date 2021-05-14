<?php

namespace app\admin\model\custom;

use think\Model;


class Card extends Model
{

    

    

    // 表名
    protected $name = 'custom_card';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'card_status_text'
    ];
    

    
    public function getCardStatusList()
    {
        return ['0' => __('Card_status 0'), '1' => __('Card_status 1'), '2' => __('Card_status 2')];
    }
    
    public function getCustomStatusList()
    {
        return ['0' => __('Custom_status 0'), '1' => __('Custom_status 1'), '2' => __('Custom_status 2')];
    }


    public function getCardStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['card_status']) ? $data['card_status'] : '');
        $list = $this->getCardStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }
    
    public function getCustomStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['custom_status']) ? $data['custom_status'] : '');
        $list = $this->getCustomStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function customcustom()
    {
        return $this->belongsTo('app\admin\model\custom\Custom', 'card_custom_id', 'custom_id', [], 'LEFT')->setEagerlyType(0);
    }
}
