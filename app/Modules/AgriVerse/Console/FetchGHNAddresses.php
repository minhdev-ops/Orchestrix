<?php

namespace App\Modules\AgriVerse\Console;

use App\Modules\AgriVerse\Models\District;
use App\Modules\AgriVerse\Models\Province;
use App\Modules\AgriVerse\Models\Ward;
use App\Modules\AgriVerse\Services\GHNService;
use Illuminate\Console\Command;

class FetchGHNAddresses extends Command
{
    protected $signature = 'agriverse:sync-ghn-addresses';

    protected $description = 'Fetch provinces, districts, wards from GHN API and cache in local DB';

    public function handle(GHNService $ghn): int
    {
        $token = config('services.ghn.token', '');
        if (empty($token)) {
            $this->warn('GHN token not configured in config/services.php or .env (services.ghn.token).');
            $this->warn('Set GHN_TOKEN and GHN_SHOP_ID in your .env file, then re-run this command.');
            $this->newLine();
            $this->info('You can get GHN API credentials by registering at https://ghn.vn/');

            return Command::SUCCESS;
        }

        $this->info('Fetching provinces...');
        $provinces = $ghn->getProvinces();
        $bar = $this->output->createProgressBar(count($provinces));
        $bar->start();

        foreach ($provinces as $p) {
            Province::updateOrCreate(
                ['province_id' => $p['ProvinceID']],
                [
                    'province_name' => $p['ProvinceName'],
                    'code' => $p['Code'] ?? null,
                ]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        $this->info('Fetching districts...');
        $districtCount = 0;
        $districtBar = $this->output->createProgressBar(count($provinces));
        $districtBar->start();

        foreach ($provinces as $p) {
            $districts = $ghn->getDistricts($p['ProvinceID']);
            foreach ($districts as $d) {
                District::updateOrCreate(
                    ['district_id' => $d['DistrictID']],
                    [
                        'district_name' => $d['DistrictName'],
                        'province_id' => $d['ProvinceID'],
                        'code' => $d['Code'] ?? null,
                    ]
                );
                $districtCount++;
            }
            $districtBar->advance();
        }
        $districtBar->finish();
        $this->newLine();
        $this->info("Imported {$districtCount} districts.");

        $this->info('Fetching wards...');
        $districtIds = District::pluck('district_id');
        $wardCount = 0;
        $wardBar = $this->output->createProgressBar($districtIds->count());
        $wardBar->start();

        foreach ($districtIds as $did) {
            $wards = $ghn->getWards($did);
            foreach ($wards as $w) {
                Ward::updateOrCreate(
                    ['ward_code' => $w['WardCode']],
                    [
                        'ward_name' => $w['WardName'],
                        'district_id' => $w['DistrictID'],
                    ]
                );
                $wardCount++;
            }
            $wardBar->advance();
        }
        $wardBar->finish();
        $this->newLine();
        $this->info("Imported {$wardCount} wards.");

        $this->newLine();
        $this->info('Done! All GHN addresses synced to database.');

        return Command::SUCCESS;
    }
}
