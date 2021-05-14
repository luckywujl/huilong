<?php

namespace app\admin\model\custom;

use think\Model;


class Custom extends Model
{

    

    

    // 表名
    protected $name = 'custom_custom';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'custom_status_text'
    ];
    

    
    public function getCustomStatusList()
    {
        return ['0' => __('Custom_status 0'), '1' => __('Custom_status 1'), '2' => __('Custom_status 2')];
    }


    public function getCustomStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['custom_status']) ? $data['custom_status'] : '');
        $list = $this->getCustomStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
