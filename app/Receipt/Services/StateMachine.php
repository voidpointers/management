<?php

namespace Receipt\Services;

use Receipt\Entities\Receipt;

class StateMachine
{
    protected const OPERATION = [
        'create' => 1,
        'packup' => 2,
        'dispatch' => 8,
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
        $query = Receipt::whereIn(key($where), current($where));

        // 查询当前状态
        foreach ($query->get() as $receipt) {
            if ($receipt->status == $this->data['status']) {
                throw new \Exception('不允许重复操作');
            }
        }

        return $query->update($this->data);
    }
}
