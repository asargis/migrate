<?php

namespace App\Console\Commands;

use App\Models\Customers;
use App\Rules\Location;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use League\ISO3166\ISO3166;

class migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:data {input} {output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from csv with validation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try {
            $invalid_data = [];
            $file = $this->argument('input');
            $csv = array_map('str_getcsv', file($file));
            array_walk($csv, function (&$a) use ($csv) {
                $a = array_combine($csv[0], $a);
            });
            array_shift($csv);

            foreach ($csv as $item) {
                $data = new Customers();

                $name = explode(' ', $item['name'])[0];
                $surname = explode(' ', $item['name'])[1];

                $validator = Validator::make(
                    [
                        'name' => $item['name'],
                        'email' => $item['email'],
                        'age' => $item['age'],
                        'location' => $item['location'],
                    ],
                    [
                        'name' => ['required', 'string', 'max:255'],
                        'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
                        'age' => ['required', 'numeric', 'min:18', 'max:99'],
                        'location' => ['required', 'string', new Location],
                    ]);


                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $field => $error) {
                        switch ($field) {
                            case 'email':
                                {
                                    $item['error'] = 'email';
                                };
                                break;
                            case 'age':
                                {
                                    $item['error'] = 'age';
                                };
                                break;
                            case 'location':
                                {
                                    $item['error'] = 'location';
                                };
                                break;
                        }
                        $invalid_data[] = [
                            'id' => $item['id'],
                            'name' => $name,
                            'surname' => $surname,
                            'email' => $item['email'],
                            'age' => $item['age'],
                            'location' => $item['location'],
                            'error' => $item['error'],
                        ];
                    }
                } else {
                    Customers::firstOrCreate(
                        [
                            'name' => $name,
                            'surname' => $surname,
                            'email' => $item['email'],
                            'age' => $item['age'],
                            'location' => $item['location'],
                            'country_code' => (new ISO3166())->name($item['location'])['alpha3'],
                        ]
                    );
                }
            }

            $fields = [
                'id',
                'name',
                'surname',
                'email',
                'age',
                'location',
                'error',
            ];

            $fp = fopen($this->argument('output'), 'w');

            array_unshift($invalid_data, $fields);

            foreach ($invalid_data as $fields) {
                fputcsv($fp, $fields);
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }
        return 0;
    }
}
