<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class RecordsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * 削除済みの記録の詳細画面にアクセスした場合のテスト
     */
    public function testViewDeletedRecord(): void
    {
        // 削除済みの記録を作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Deleted Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => true,  // 削除済み
        ]);
        $records->save($record);

        // 削除済みの記録にアクセス
        $this->get('/records/view/' . $record->id);

        // 404エラーが返されることを確認
        $this->assertResponseCode(404);
    }
}