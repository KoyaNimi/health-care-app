<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HospitalVisitsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HospitalVisitsTable Test Case
 */
class HospitalVisitsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HospitalVisitsTable
     */
    protected $HospitalVisits;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.HospitalVisits',
        'app.Records',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('HospitalVisits') ? [] : ['className' => HospitalVisitsTable::class];
        $this->HospitalVisits = $this->getTableLocator()->get('HospitalVisits', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->HospitalVisits);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\HospitalVisitsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\HospitalVisitsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
