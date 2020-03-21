<?php

namespace App\Console\Commands;

use Etsy\Requests\ReceiptRequest;
use Illuminate\Console\Command;
use Receipt\Services\ReceiptService;

class ReceiptPull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipt:pull {method} {--page=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取订单';

    protected $receiptService;

    protected $receiptRequest;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ReceiptService $receiptService,
        ReceiptRequest $receiptRequest)
    {
        $this->receiptService = $receiptService;
        $this->receiptRequest = $receiptRequest;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $shop_id = $this->option('shop_id');
        $page = $this->option('page') ?? 1;
        $limit = $this->option('limit') ?? 5;

        if ('page' == $this->argument('method')) {
            for ($i = $page; $i > 0; $i--) {
                $this->pull([
                    'shop_id' => $shop_id,
                    'page' => $i,
                    'limit' => $limit,
                ]);
            }
        } else {
            // 获取一小时内订单
            $cur = mktime(date("H"), 0, 0);
            $this->pull([
                'shop_id' => $shop_id,
                'min_created' => $cur - 3600,
                'max_created' => $cur
            ]);
        }
    }

    protected function pull($params)
    {
        $data = $this->receiptRequest->filters($params);
        if (empty($data)) {
            echo "订单列表为空" . PHP_EOL;
            return;
        }

        // 入库
        $this->receiptService->create($data);

        echo json_encode($params) . " 执行完毕" . PHP_EOL;
        usleep(100);
    }
}
