<?php

namespace App\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Association;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Utility\Hash;

class PreserveNullBehavior extends Behavior
{
    /**
     * @param \Cake\Event\Event $event
     * @param \ArrayObject $data
     * @param \ArrayObject $options
     * @return void
     */
    public function beforeMarshal(Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        $data = $this->_process($data, $this->_table);
    }

    /**
     * @param \ArrayObject|array $data
     * @param \Cake\ORM\Table $table
     * @return \ArrayObject|array
     */
    protected function _process($data, Table $table)
    {
        $associations = [];
        foreach ($table->associations() as $association) { /* @var $association \Cake\ORM\Association */
            $associations[$association->getProperty()] = $association->getName();
        }

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $associations)) {
                $data[$key] = $this->_process($data[$key], $table->getAssociation($associations[$key])->getTarget());
                continue;
            }
            $nullable = Hash::get((array)$table->getSchema()->getColumn($key), 'null');
            if ($nullable !== true) {
                continue;
            }
            if ($value === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }
}
