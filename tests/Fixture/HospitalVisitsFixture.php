<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * HospitalVisitsFixture
 */
class HospitalVisitsFixture extends TestFixture
{
    public array $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true],
        'record_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false],
        'hospital_name' => ['type' => 'string', 'length' => 255, 'null' => false],
        'visit_datetime' => ['type' => 'datetime', 'length' => null, 'null' => false],
        'treatment_details' => ['type' => 'text', 'length' => null, 'null' => true],
        'impressions' => ['type' => 'text', 'length' => null, 'null' => true],
        'created_at' => ['type' => 'datetime', 'length' => null, 'null' => false],
        'modified_at' => ['type' => 'datetime', 'length' => null, 'null' => false],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],
            'hospital_visits_ibfk_1' => [
                'type' => 'foreign',
                'columns' => ['record_id'],
                'references' => ['records', 'id'],
                'update' => 'CASCADE',
                'delete' => 'CASCADE',
            ],
        ],
    ];

    public array $records = [];

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        parent::init();
    }
}
