<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CustomersFixture
 */
class CustomersFixture extends TestFixture
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
                'shortcut' => 'L',
                'hourly_rate' => 1,
                'website' => 'Lorem ipsum dolor sit amet',
                'notes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'color' => 'Lorem ',
                'current' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'street' => 'Lorem ipsum dolor sit amet',
                'zip_city' => 'Lorem ipsum dolor sit amet',
                'country' => 'Lorem ipsum dolor sit amet',
                'invoice_email' => 'Lorem ipsum dolor sit amet',
                'created' => 1667491821,
            ],
        ];
        parent::init();
    }
}
