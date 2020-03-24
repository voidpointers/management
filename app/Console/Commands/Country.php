<?php

namespace App\Console\Commands;

use Common\Entities\Country as EntitiesCountry;
use Illuminate\Console\Command;
use Express\Requests\Request as ExpressRequest;

class Country extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'country:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取国家列表';

    protected $request;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ExpressRequest $request) 
    {
        $this->request = $request;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $countries = $this->request->instance()->country();

        $data = [];
        foreach ($countries as $country) {
            $data[] = [
                'code' => $country['CountryCode'],
                'name' => $country['CName'],
                'en' => $country['EName'],
            ];
        }

        EntitiesCountry::insert($data);
    }
}
