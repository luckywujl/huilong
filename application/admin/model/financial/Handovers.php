<?php

namespace app\admin\model\financial;

use think\Model;


class Handovers extends Model
{

    

    

    // 表名
    protected $name = 'finanical_handovers';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'handovers_begintime_text',
        'handovers_endtime_text'
    ];
    

    



    public function getHandoversBegintimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['handovers_begintime']) ? $data['handovers_begintime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getHandoversEndtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['handovers_endtime']) ? $data['handovers_endtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setHandoversBegintimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setHandoversEndtimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
