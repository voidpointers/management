<?php

namespace Package\Services;

use Package\Entities\Package;

class StateMachine
{
    protected const OPERATION = [
        'create' => 1,
        'track' => 2, // 获取物流单号，待打单
        'print' => 3, // 打单，待发货
        'dispatch' => 8, // 发货
        'close' => 7,
        'complete' => 8,
    ];

    protected $data;

    /**
     * 操作
     */
    public function operation($action, $where = [])
    {
        if (!in_array($action, array_keys(self::OPERATION))) {
            return false;
        }
        $this->build($action);

        return $this->update($where);
    }

    protected function build($action)
    {
        $data = [
            'status' => self::OPERATION[$action],
            $action . '_time' => time()
        ];
        if ('dispatch' == $action) {
            $data['complete_time'] = time();
        }

        $this->data = $data;
    }

    protected function update($where)
    {
        return Package::whereIn(
            key($where), current($where)
        )->update($this->data);
    }
}
