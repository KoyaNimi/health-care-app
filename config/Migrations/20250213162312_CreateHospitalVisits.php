<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateHospitalVisits extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('hospital_visits');
        $table->addColumn('record_id', 'integer', [
            'null' => false,
        ])
            ->addColumn('hospital_name', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('visit_datetime', 'datetime', [
                'null' => false,
            ])
            ->addColumn('treatment_details', 'text', [
                'null' => true,
            ])
            ->addColumn('impressions', 'text', [
                'null' => true,
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('modified_at', 'datetime', [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'update' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('is_deleted', 'boolean', [
                'null' => false,
                'default' => false,
            ])
            ->addForeignKey('record_id', 'records', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
