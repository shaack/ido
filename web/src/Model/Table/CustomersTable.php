<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Customers Model
 *
 * @property \App\Model\Table\ContactsTable&\Cake\ORM\Association\HasMany $Contacts
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\HasMany $Projects
 *
 * @method \App\Model\Entity\Customer newEmptyEntity()
 * @method \App\Model\Entity\Customer newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Customer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Customer get($primaryKey, $options = [])
 * @method \App\Model\Entity\Customer findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Customer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Customer[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Customer|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Customer saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Customer[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Customer[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Customer[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Customer[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CustomersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('customers');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Contacts', [
            'foreignKey' => 'customer_id',
        ]);
        $this->hasMany('Projects', [
            'foreignKey' => 'customer_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('shortcut')
            ->maxLength('shortcut', 3)
            ->allowEmptyString('shortcut');

        $validator
            ->numeric('hourly_rate')
            ->allowEmptyString('hourly_rate');

        $validator
            ->scalar('website')
            ->maxLength('website', 256)
            ->allowEmptyString('website');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        $validator
            ->scalar('color')
            ->maxLength('color', 8)
            ->allowEmptyString('color');

        $validator
            ->boolean('current')
            ->notEmptyString('current');

        $validator
            ->scalar('name')
            ->maxLength('name', 128)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('street')
            ->maxLength('street', 128)
            ->allowEmptyString('street');

        $validator
            ->scalar('zip_city')
            ->maxLength('zip_city', 128)
            ->allowEmptyString('zip_city');

        $validator
            ->scalar('country')
            ->maxLength('country', 64)
            ->allowEmptyString('country');

        $validator
            ->scalar('invoice_email')
            ->maxLength('invoice_email', 256)
            ->allowEmptyString('invoice_email');

        return $validator;
    }
}
