<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TimeTrackingTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TimeTrackingTable Test Case
 */
class TimeTrackingTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TimeTrackingTable
     */
    protected $TimeTracking;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.TimeTracking',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('TimeTracking') ? [] : ['className' => TimeTrackingTable::class];
        $this->TimeTracking = $this->getTableLocator()->get('TimeTracking', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TimeTracking);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TimeTrackingTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
