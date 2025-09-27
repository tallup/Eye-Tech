<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ResetDataSeeder;

class ResetWebsiteData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'website:reset-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset website data with fresh sample products and services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting website data...');
        
        $seeder = new ResetDataSeeder();
        $seeder->run();
        
        $this->info('✅ Website data reset successfully!');
        $this->info('📊 Data summary:');
        $this->info('   - Categories: ' . \App\Models\Category::count());
        $this->info('   - Suppliers: ' . \App\Models\Supplier::count());
        $this->info('   - Services: ' . \App\Models\Service::count());
        $this->info('   - Products: ' . \App\Models\Product::count());
    }
}