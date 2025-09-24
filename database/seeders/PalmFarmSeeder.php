<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\Block;
use App\Models\PalmStage;
use App\Models\PalmTree;
use App\Models\Worker;

class PalmFarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create palm stages
        $stages = [
            ['name' => 'شتلة', 'min_age_years' => 0, 'max_age_years' => 3, 'expected_yield' => 0],
            ['name' => 'صغيرة', 'min_age_years' => 4, 'max_age_years' => 8, 'expected_yield' => 10],
            ['name' => 'متوسطة', 'min_age_years' => 9, 'max_age_years' => 15, 'expected_yield' => 25],
            ['name' => 'ناضجة', 'min_age_years' => 16, 'max_age_years' => 30, 'expected_yield' => 50],
            ['name' => 'كبيرة', 'min_age_years' => 31, 'max_age_years' => null, 'expected_yield' => 40],
        ];

        foreach ($stages as $stage) {
            PalmStage::create($stage);
        }

        // Create sample farm
        $farm = Farm::create([
            'name' => 'مزرعة النخيل الرئيسية',
            'owner' => 'أحمد محمد',
            'location' => 'الرياض - طريق الخرج كم 25',
            'size' => 50.5,
            'coordinates' => '24.7136, 46.6753',
            'description' => 'مزرعة متخصصة في زراعة نخيل التمر بأحدث التقنيات',
            'is_active' => true,
        ]);

        // Create blocks
        $blocks = [
            ['name' => 'القطعة الشمالية', 'area' => 15.2, 'soil_type' => 'طينية', 'irrigation_type' => 'تنقيط'],
            ['name' => 'القطعة الجنوبية', 'area' => 12.8, 'soil_type' => 'رملية', 'irrigation_type' => 'رذاذ'],
            ['name' => 'القطعة الشرقية', 'area' => 10.5, 'soil_type' => 'طينية', 'irrigation_type' => 'تنقيط'],
            ['name' => 'القطعة الغربية', 'area' => 12.0, 'soil_type' => 'رملية', 'irrigation_type' => 'رذاذ'],
        ];

        foreach ($blocks as $blockData) {
            $block = Block::create([
                'farm_id' => $farm->id,
                'name' => $blockData['name'],
                'area' => $blockData['area'],
                'soil_type' => $blockData['soil_type'],
                'irrigation_type' => $blockData['irrigation_type'],
                'notes' => 'قطعة منتجة ومروية بشكل منتظم',
                'is_active' => true,
            ]);

            // Create palm trees for each block
            $stages = PalmStage::all();
            for ($i = 1; $i <= 25; $i++) {
                $stage = $stages->random();
                $row = ceil($i / 5);
                $col = $i % 5;
                if ($col == 0) $col = 5;

                PalmTree::create([
                    'block_id' => $block->id,
                    'tree_code' => 'T' . $block->id . sprintf('%03d', $i),
                    'row_no' => $row,
                    'col_no' => $col,
                    'stage_id' => $stage->id,
                    'variety' => 'سكري',
                    'planting_date' => now()->subYears(rand(5, 20)),
                    'status' => 'active',
                ]);
            }
        }

        // Create workers
        $workers = [
            ['name' => 'محمد أحمد', 'national_id' => '1234567890', 'phone' => '+966501234567', 'role_in_farm' => 'مشرف عام'],
            ['name' => 'علي حسن', 'national_id' => '1234567891', 'phone' => '+966501234568', 'role_in_farm' => 'عامل حصاد'],
            ['name' => 'فاطمة محمد', 'national_id' => '1234567892', 'phone' => '+966501234569', 'role_in_farm' => 'عاملة ري'],
            ['name' => 'خالد عبدالله', 'national_id' => '1234567893', 'phone' => '+966501234570', 'role_in_farm' => 'عامل صيانة'],
        ];

        foreach ($workers as $workerData) {
            Worker::create([
                'name' => $workerData['name'],
                'national_id' => $workerData['national_id'],
                'phone' => $workerData['phone'],
                'farm_id' => $farm->id,
                'role_in_farm' => $workerData['role_in_farm'],
                'employment_status' => 'active',
                'hire_date' => now()->subMonths(rand(6, 36)),
                'salary' => rand(3000, 8000),
                'notes' => 'عامل مجتهد ومنتج',
            ]);
        }
    }
}
