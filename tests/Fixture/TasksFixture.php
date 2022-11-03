<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TasksFixture
 */
class TasksFixture extends TestFixture
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
                'marked' => 1,
                'prio' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'start_est' => '2022-11-03',
                'deadline' => '2022-11-03',
                'duration_est' => 1,
                'link' => 'Lorem ipsum dolor sit amet',
                'service_id' => 1,
                'created' => 1667513338,
                'notes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'done' => 1,
                'done_at' => '2022-11-03 22:08:58',
                'duration' => 1,
            ],
        ];
        parent::init();
    }
}
