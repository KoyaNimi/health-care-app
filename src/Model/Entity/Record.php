<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Record Entity
 *
 * @property int $id
 * @property \Cake\I18n\Date $onset_date
 * @property \Cake\I18n\Date|null $recovery_date
 * @property string $disease_name
 * @property string|null $complications
 * @property int $severity
 * @property string|null $medications
 * @property string|null $recovery_actions
 * @property int|null $action_effectiveness
 * @property string|null $free_notes
 * @property \Cake\I18n\DateTime|null $reminder_datetime
 * @property \Cake\I18n\DateTime $created_at
 * @property \Cake\I18n\DateTime $modified_at
 * @property bool $is_deleted
 *
 * @property \App\Model\Entity\HospitalVisit[] $hospital_visits
 */
class Record extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'onset_date' => true,
        'recovery_date' => true,
        'disease_name' => true,
        'complications' => true,
        'severity' => true,
        'medications' => true,
        'recovery_actions' => true,
        'action_effectiveness' => true,
        'free_notes' => true,
        'reminder_datetime' => true,
        'created_at' => true,
        'modified_at' => true,
        'is_deleted' => true,
        'hospital_visits' => true,
    ];
}
