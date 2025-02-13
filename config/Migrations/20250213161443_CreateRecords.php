<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateRecords extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/4/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('records');
        $table->addColumn('onset_date', 'date', [
            'null' => false,
        ])
            ->addColumn('recovery_date', 'date', [
                'null' => true,
            ])
            ->addColumn('disease_name', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('complications', 'text', [
                'null' => true,
            ])
            ->addColumn('severity', 'integer', [
                'default' => 1,
                'null' => false,
            ])
            ->addColumn('medications', 'text', [
                'null' => true,
            ])
            ->addColumn('recovery_actions', 'text', [
                'null' => true,
            ])
            ->addColumn('action_effectiveness', 'integer', [
                'null' => true,
            ])
            ->addColumn('free_notes', 'text', [
                'null' => true,
            ])
            ->addColumn('reminder_datetime', 'datetime', [
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
                'default' => false,
                'null' => false,
            ])
            ->create();
    }
}
