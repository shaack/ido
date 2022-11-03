<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TimeTrackingsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TimeTrackingsTable Test Case
 */
class TimeTrackingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TimeTrackingsTable
     */
    protected $TimeTrackings;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.TimeTrackings',
        'app.Tasks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('TimeTrackings') ? [] : ['className' => TimeTrackingsTable::class];
        $this->TimeTrackings = $this->getTableLocator()->get('TimeTrackings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TimeTrackings);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\TimeTrackingsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\TimeTrackingsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
