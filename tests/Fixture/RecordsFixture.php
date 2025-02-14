<?php

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * 記録テーブルのフィクスチャ
 * 
 * このフィクスチャは、記録（Records）テーブルのテストデータを定義します。
 * テストの際に一時的なテストデータベースにこの構造でテーブルが作成されます。
 */
class RecordsFixture extends TestFixture
{
    /**
     * テーブルのフィールド定義
     * 
     * @var array テーブルのカラム定義の配列
     */
    public array $fields = [
        // 主キー（ID）
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true],

        // 基本情報
        'onset_date' => ['type' => 'date', 'length' => null, 'null' => false],  // 発症日（必須）
        'recovery_date' => ['type' => 'date', 'length' => null, 'null' => true],  // 回復日（任意）
        'disease_name' => ['type' => 'string', 'length' => 255, 'null' => false],  // 病名（必須）

        // 詳細情報
        'complications' => ['type' => 'text', 'length' => null, 'null' => true],  // 合併症（任意）
        'severity' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '1'],  // 重症度（必須、デフォルト1）
        'medications' => ['type' => 'text', 'length' => null, 'null' => true],  // 服薬情報（任意）
        'recovery_actions' => ['type' => 'text', 'length' => null, 'null' => true],  // 回復のための行動（任意）
        'action_effectiveness' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true],  // 行動の効果（任意）
        'free_notes' => ['type' => 'text', 'length' => null, 'null' => true],  // 自由メモ（任意）

        // リマインダー
        'reminder_datetime' => ['type' => 'datetime', 'length' => null, 'null' => true],  // リマインド日時（任意）

        // 管理用カラム
        'created_at' => ['type' => 'datetime', 'length' => null, 'null' => false],  // 作成日時（必須）
        'modified_at' => ['type' => 'datetime', 'length' => null, 'null' => false],  // 更新日時（必須）
        'is_deleted' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => false],  // 論理削除フラグ（必須、デフォルトfalse）

        // テーブルの制約
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id']],  // 主キー制約
        ],
    ];

    /**
     * テストデータ
     * 
     * 各テストメソッドで必要なデータを個別に作成するため、
     * ここでは空の配列を定義します。
     * これにより、テスト間でのデータの影響を防ぎます。
     * 
     * @var array テストデータの配列
     */
    public array $records = [];
}
