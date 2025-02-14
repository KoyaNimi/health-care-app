<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RecordsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RecordsTable Test Case
 */
class RecordsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\RecordsTable
     */
    protected $Records;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Records',
        'app.HospitalVisits',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Records') ? [] : ['className' => RecordsTable::class];
        $this->Records = $this->getTableLocator()->get('Records', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Records);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\RecordsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
