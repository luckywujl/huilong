<?php

namespace app\admin\model\financial;

use think\Model;


class Statementcount extends Model
{

    

    

    // 表名
    protected $name = 'financial_statement';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'statement_intime_text',
        'statement_outtime_text',
        'statement_status_text'
    ];
    

    
    public function getStatementStatusList()
    {
        return ['0' => __('Statement_status 0'), '1' => __('Statement_status 1')];
    }


    public function getStatementIntimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['statement_intime']) ? $data['statement_intime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatementOuttimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['statement_outtime']) ? $data['statement_outtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatementStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['statement_status']) ? $data['statement_status'] : '');
        $list = $this->getStatementStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setStatementIntimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setStatementOuttimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


    public function customcustom()
    {
        return $this->belongsTo('app\admin\model\custom\Custom', 'statement_custom_id', 'custom_id', [], 'LEFT')->setEagerlyType(0);
    }
}
