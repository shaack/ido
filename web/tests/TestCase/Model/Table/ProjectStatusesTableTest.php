<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProjectStatusesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProjectStatusesTable Test Case
 */
class ProjectStatusesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProjectStatusesTable
     */
    protected $ProjectStatuses;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.ProjectStatuses',
        'app.Projects',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ProjectStatuses') ? [] : ['className' => ProjectStatusesTable::class];
        $this->ProjectStatuses = $this->getTableLocator()->get('ProjectStatuses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ProjectStatuses);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ProjectStatusesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\ProjectStatusesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
