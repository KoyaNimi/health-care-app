<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Records Model
 *
 * @property \App\Model\Table\HospitalVisitsTable&\Cake\ORM\Association\HasMany $HospitalVisits
 *
 * @method \App\Model\Entity\Record newEmptyEntity()
 * @method \App\Model\Entity\Record newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Record> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Record get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Record findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Record patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Record> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Record|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Record saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Record>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Record>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Record>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Record> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Record>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Record>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Record>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Record> deleteManyOrFail(iterable $entities, array $options = [])
 */
class RecordsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('records');
        $this->setDisplayField('disease_name');
        $this->setPrimaryKey('id');

        $this->hasMany('HospitalVisits', [
            'foreignKey' => 'record_id',
        ]);

        // created_atとmodified_atを自動設定
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created_at' => 'new',    // 新規作成時のみ
                    'modified_at' => 'always'  // 常に更新
                ]
            ]
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
            ->date('onset_date')
            ->requirePresence('onset_date', 'create')
            ->notEmptyDate('onset_date');

        $validator
            ->date('recovery_date')
            ->allowEmptyDate('recovery_date');

        $validator
            ->scalar('disease_name')
            ->maxLength('disease_name', 255)
            ->requirePresence('disease_name', 'create')
            ->notEmptyString('disease_name');

        $validator
            ->scalar('complications')
            ->allowEmptyString('complications');

        $validator
            ->integer('severity')
            ->notEmptyString('severity');

        $validator
            ->scalar('medications')
            ->allowEmptyString('medications');

        $validator
            ->scalar('recovery_actions')
            ->allowEmptyString('recovery_actions');

        $validator
            ->integer('action_effectiveness')
            ->allowEmptyString('action_effectiveness');

        $validator
            ->scalar('free_notes')
            ->allowEmptyString('free_notes');

        $validator
            ->dateTime('reminder_datetime')
            ->allowEmptyDateTime('reminder_datetime');

        $validator
            ->dateTime('created_at')
            ->notEmptyDateTime('created_at');

        $validator
            ->dateTime('modified_at')
            ->notEmptyDateTime('modified_at');

        $validator
            ->boolean('is_deleted')
            ->notEmptyString('is_deleted');

        return $validator;
    }
}
