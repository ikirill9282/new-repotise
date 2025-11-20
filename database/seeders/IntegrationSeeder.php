<?php

namespace Database\Seeders;

use App\Models\Integration;
use Illuminate\Database\Seeder;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = [
            [
                'name' => 'stripe',
                'type' => Integration::TYPE_PAYMENT,
                'status' => Integration::STATUS_NOT_CONFIGURED,
                'config' => null,
            ],
            [
                'name' => 'mailgun',
                'type' => Integration::TYPE_EMAIL,
                'status' => Integration::STATUS_NOT_CONFIGURED,
                'config' => null,
            ],
            [
                'name' => 'ga4',
                'type' => Integration::TYPE_ANALYTICS,
                'status' => Integration::STATUS_NOT_CONFIGURED,
                'config' => null,
            ],
        ];

        foreach ($integrations as $integration) {
            Integration::updateOrCreate(
                ['name' => $integration['name']],
                $integration
            );
        }
    }
}
