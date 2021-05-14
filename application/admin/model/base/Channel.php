<?php

namespace app\admin\model\base;

use think\Model;


class Channel extends Model
{

    

    

    // 表名
    protected $name = 'base_channel';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'channel_iotype_text'
    ];
    

    
    public function getChannelIotypeList()
    {
        return ['0' => __('Channel_iotype 0'), '1' => __('Channel_iotype 1')];
    }


    public function getChannelIotypeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['channel_iotype']) ? $data['channel_iotype'] : '');
        $list = $this->getChannelIotypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
