<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $anomaliesDestinationEmail = new Configuration();
        $anomaliesDestinationEmail->config_name = 'anomalies_destination_email';
        $anomaliesDestinationEmail->config_value = null;
        $anomaliesDestinationEmail->save();

        $requisitionsDestinationEmail = new Configuration();
        $requisitionsDestinationEmail->config_name = 'requisitions_destination_email';
        $requisitionsDestinationEmail->config_value = null;
        $requisitionsDestinationEmail->save();
    }
}
