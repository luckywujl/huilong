<?php

namespace app\admin\model\report;

use think\Model;


class Accountpaymode extends Model
{

    

    

    // 表名
    protected $name = 'financial_account';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'account_type_text'
    ];
    

    
    public function getAccountTypeList()
    {
        return ['0' => __('Account_type 0'), '1' => __('Account_type 1')];
    }


    public function getAccountTypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['account_type']) ? $data['account_type'] : '');
        $list = $this->getAccountTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
