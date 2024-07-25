<?php

namespace App\Console\Commands\Retrofits;

use App\Models\Company;
use App\Models\GoodsIssue;
use App\Models\GoodsReceipt;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetrofitUnitType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrofit:add-unit-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will add the default unit type of products, stock movements, goods receipts, goods issues and inventories.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('Fetching all companies...');
        $companies = Company::all();
        $this->line($companies->count().' company/s found.');

        $this->line('Retrofitting...');
        $bar = $this->output->createProgressBar($companies->count());
        $bar->start();

        foreach ($companies as $company) {
            $unit = Unit::firstOrCreate([
                'company_id' => $company->id,
                'name' => 'Piece',
                'abbreviation' => 'pcs',
            ]);

            $products = Product::where('company_id', $company->id)->get();
            $this->startExecution($products, $unit);

            $inventories = Inventory::where('company_id', $company->id)->get();
            $this->startExecution($inventories, $unit);

            $stockMovements = StockMovement::where('company_id', $company->id)->get();
            $this->startExecution($stockMovements, $unit, true);

            $goodsIssue = GoodsIssue::where('company_id', $company->id)->get();
            $this->startExecution($goodsIssue, $unit, true);

            $goodsReceipt = GoodsReceipt::where('company_id', $company->id)->get();
            $this->startExecution($goodsReceipt, $unit, true);

            $items = PurchaseOrderItem::where('company_id', $company->id)->get();
            $this->startExecution($items, $unit, true);

            $bar->advance();
        }

        $this->newLine();

        $this->line('Retrofit Finished!');
    }

    /**
     * @param $records
     * @param $unit
     * @param $updateQuantity
     * @return void
     */
    protected function startExecution($records, $unit, $updateQuantity = false): void
    {
        $errorCount = 0;

        foreach ($records as $record) {
            try {
                $record->unit_id = $unit->id;
                if ($updateQuantity) {
                    $record->quantity_base_unit = $record->quantity;
                }

                $record->save();
            } catch (\Exception $exception) {
                Log::error('There was something wrong while retrofitting data.', [
                    'record_id' => $record->id,
                    'class' => get_class($record),
                    'exception' => $exception,
                ]);
                $errorCount++;
            }

            if ($errorCount) {
                $this->error($errorCount.' errors found. Please check logs.');
            }
        }
    }
}
