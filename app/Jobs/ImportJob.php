<?php

namespace App\Jobs;

use App\Models\Categories;
use App\Models\ImportReport;
use App\Models\Manufacturer;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $items;

    protected $categories;

    protected $report_id;

    protected $counterItems = 0;
    protected $counterDoubles = 0;
    protected $counterNullField = 0;

    public $tries = 50;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->items = $data['items'];
        $this->report_id = $data['report_id'];
        $this->categories = Categories::all()->toArray();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->items as $item) {
            if ($this->validate($item)) {
                $this->counterNullField++;
                continue;
            }
            $category = $this->category($item);
            $manufacturer = $this->manufacturer($item);
            $product = $this->addProduct($item, $category->id, $manufacturer->id);
            if ($product->updated_at != $product->created_at) {
                $this->counterDoubles++;
            } else {
                $this->counterItems++;
            }
        }
        $this->report();
    }

    public function report()
    {
        $reportArr = [
            'report_id' => $this->report_id,
            'duplicate_fields' => $this->counterDoubles,
            'duplicate_null' => $this->counterNullField,
            'sum' => $this->counterItems
        ];
        ImportReport::create($reportArr);
    }

    public function category($item)
    {
        $firstCategory = $this->addCategory($item[0], null);
        $secondCategory = $this->addCategory($item[1], $firstCategory->id);
        return $this->addCategory($item[2], $secondCategory->id);
    }

    public function manufacturer($item)
    {
        return Manufacturer::updateOrCreate([
            'name' => $item[3]
        ]);
    }

    public function addProduct($item, $categoryId, $manufacturerId)
    {
        return Product::updateOrCreate([
            'category_id' => $categoryId,
            'manufacturer_id' => $manufacturerId,
            'name' => $item[4],
            'code' => $item[5],
            'description' => $item[6],
            'amount' => $item[7],
            'garancy' => $item[8],
            'in_stock' =>  $item[9]
        ]);
    }

    public function addCategory($name, $parentId)
    {
        return Categories::updateOrCreate([
            'name' => $name,
            'parent_id' => $parentId
        ]);
    }

    public function validate($product)
    {
        $response = false;
        $requireFields = [0,1,2,3,4,5,6,7,8,9];
        foreach ($product as $k => $field) {
            if (is_null($field) && in_array($k, $requireFields)) {
                $response = true;
            }
        }

        return $response;
    }

    public function getResponse()
    {
        return [
            'counter_items' => $this->counterItems,
            'counter_doubles' => $this->counterDoubles,
            'counterNull' => $this->counterNullField
        ];
    }
}
