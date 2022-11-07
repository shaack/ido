<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Projects Model
 *
 * @property \App\Model\Table\CustomersTable&\Cake\ORM\Association\BelongsTo $Customers
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $ParentProjects
 * @property \App\Model\Table\ProjectStatusesTable&\Cake\ORM\Association\BelongsTo $ProjectStatuses
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\HasMany $ChildProjects
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\HasMany $Services
 *
 * @method \App\Model\Entity\Project newEmptyEntity()
 * @method \App\Model\Entity\Project newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Project[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Project get($primaryKey, $options = [])
 * @method \App\Model\Entity\Project findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Project patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Project[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Project|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Project saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProjectsTable extends Table
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

        $this->setTable('projects');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
        ]);
        $this->belongsTo('ParentProjects', [
            'className' => 'Projects',
            'foreignKey' => 'parent_id',
        ]);
        $this->belongsTo('ProjectStatuses', [
            'foreignKey' => 'project_status_id',
        ]);
        $this->hasMany('ChildProjects', [
            'className' => 'Projects',
            'foreignKey' => 'parent_id',
        ]);
        $this->hasMany('Services', [
            'foreignKey' => 'project_id',
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
            ->scalar('name')
            ->maxLength('name', 256)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('customer_id')
            ->allowEmptyString('customer_id');

        $validator
            ->date('start')
            ->allowEmptyDate('start');

        $validator
            ->date('end_est')
            ->allowEmptyDate('end_est');

        $validator
            ->date('end')
            ->allowEmptyDate('end');

        $validator
            ->boolean('fixed_price')
            ->allowEmptyString('fixed_price');

        $validator
            ->numeric('hourly_rate')
            ->requirePresence('hourly_rate', 'create')
            ->notEmptyString('hourly_rate');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('invoice_number')
            ->maxLength('invoice_number', 16)
            ->allowEmptyString('invoice_number');

        $validator
            ->date('invoice_date')
            ->allowEmptyDate('invoice_date');

        $validator
            ->date('paid_at')
            ->allowEmptyDate('paid_at');

        $validator
            ->integer('parent_id')
            ->allowEmptyString('parent_id');

        $validator
            ->integer('project_status_id');
            // ->allowEmptyString('project_status_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('customer_id', 'Customers'), ['errorField' => 'customer_id']);
        $rules->add($rules->existsIn('parent_id', 'ParentProjects'), ['errorField' => 'parent_id']);
        $rules->add($rules->existsIn('project_status_id', 'ProjectStatuses'), ['errorField' => 'project_status_id']);

        return $rules;
    }
}
