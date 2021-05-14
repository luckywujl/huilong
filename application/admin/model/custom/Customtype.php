<?php

namespace app\admin\model\custom;

use think\Model;


class Customtype extends Model
{

    

    

    // 表名
    protected $name = 'custom_customtype';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'customtype_attribute_text'
    ];
    

    
    public function getCustomtypeAttributeList()
    {
        return ['0' => __('Customtype_attribute 0'), '1' => __('Customtype_attribute 1')];
    }


    public function getCustomtypeAttributeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['customtype_attribute']) ? $data['customtype_attribute'] : '');
        $list = $this->getCustomtypeAttributeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
