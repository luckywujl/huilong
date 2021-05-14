<?php

namespace app\admin\model\work;

use think\Model;


class Outdetail extends Model
{

    

    

    // 表名
    protected $name = 'work_iodetail';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'iodetail_iotype_text',
        'iodetail_iotime_text',
        'iodetail_status_text'
    ];
    

    
    public function getIodetailIotypeList()
    {
        return ['0' => __('Iodetail_iotype 0'), '1' => __('Iodetail_iotype 1')];
    }

    public function getIodetailStatusList()
    {
        return ['0' => __('Iodetail_status 0'), '1' => __('Iodetail_status 1'), '2' => __('Iodetail_status 2')];
    }


    public function getIodetailIotypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['iodetail_iotype']) ? $data['iodetail_iotype'] : '');
        $list = $this->getIodetailIotypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIodetailIotimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['iodetail_iotime']) ? $data['iodetail_iotime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getIodetailStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['iodetail_status']) ? $data['iodetail_status'] : '');
        $list = $this->getIodetailStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setIodetailIotimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function customcustom()
    {
        return $this->belongsTo('app\admin\model\custom\Custom', 'iodetail_custom_id', 'custom_id', [], 'LEFT')->setEagerlyType(0);
    }


    public function baseproduct()
    {
        return $this->belongsTo('app\admin\model\base\Product', 'iodetail_product_id', 'product_ID', [], 'LEFT')->setEagerlyType(0);
    }
}
