<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TimeTrackingsFixture
 */
class TimeTrackingsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'task_id' => 1,
                'start' => '2022-11-03 16:10:22',
                'pause' => 1,
                'duration' => 1,
            ],
        ];
        parent::init();
    }
}
