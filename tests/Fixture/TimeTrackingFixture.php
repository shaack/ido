<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TimeTrackingFixture
 */
class TimeTrackingFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'time_tracking';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'Id' => 1,
                'task' => 1,
                'start' => '2022-11-02 22:36:01',
                'pause' => 1,
                'duration' => 1,
            ],
        ];
        parent::init();
    }
}
