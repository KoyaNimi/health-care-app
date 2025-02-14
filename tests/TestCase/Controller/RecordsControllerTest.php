<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\RecordsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use App\Test\Fixture\RecordsFixture;
use App\Test\Fixture\HospitalVisitsFixture;

/**
 * 記録コントローラーのテストクラス
 */
class RecordsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    // フィクスチャクラスを直接指定
    protected array $fixtures = [
        RecordsFixture::class,
        HospitalVisitsFixture::class
    ];

    /**
     * 記録が0件の場合のindexアクションのテスト
     * 
     * @return void
     */
    public function testIndexWithNoRecords(): void
    {
        // 1. テーブルは自動的に空の状態（truncateによる）

        // 2. /records へのGETリクエストを実行
        $this->get('/records');

        // 3. レスポンスが成功（200 OK）することを確認
        $this->assertResponseSuccess();

        // 4. ビュー変数に渡されるrecordsが空であることを確認
        $records = $this->viewVariable('records');
        $this->assertEmpty($records);
    }

    /**
     * 記録が存在する場合のindexアクションのテスト
     * 
     * @return void
     */
    public function testIndexWithRecords(): void
    {
        // 1. テストデータの作成
        $record = $this->getTableLocator()->get('Records')->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);

        // 2. テストデータをデータベースに保存
        $this->getTableLocator()->get('Records')->save($record);

        // 3. /records へのGETリクエストを実行
        $this->get('/records');

        // 4. レスポンスが成功（200 OK）することを確認
        $this->assertResponseSuccess();

        // 5. ビュー変数のテスト
        $records = $this->viewVariable('records');
        $this->assertNotEmpty($records);                                  // レコードが存在することを確認
        $this->assertEquals(1, count($records));                         // レコード数が1件であることを確認
        $this->assertEquals('Test Disease', $records->first()->disease_name); // 病名が正しいことを確認
    }

    /**
     * 記録が正しい順序（発症日の降順）で表示されることのテスト
     */
    public function testIndexOrderByOnsetDate(): void
    {
        // 1. テストデータの作成
        $records = $this->getTableLocator()->get('Records');

        // 1つ目の記録（新しい発症日）
        $record1 = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'New Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);

        // 2つ目の記録（古い発症日）
        $record2 = $records->newEntity([
            'onset_date' => '2024-01-01',
            'disease_name' => 'Old Disease',
            'severity' => 1,
            'created_at' => '2024-01-01 10:00:00',
            'modified_at' => '2024-01-01 10:00:00',
            'is_deleted' => false,
        ]);

        // 2. データを保存
        $records->saveMany([$record1, $record2]);

        // 3. /records へのGETリクエストを実行
        $this->get('/records');
        $this->assertResponseSuccess();

        // 4. ビュー変数のテスト
        $viewRecords = $this->viewVariable('records');
        $this->assertNotEmpty($viewRecords);
        $this->assertEquals(2, count($viewRecords));

        // 5. 順序のテスト
        $firstRecord = $viewRecords->first();
        $lastRecord = $viewRecords->last();

        // 新しい発症日の記録が最初に表示される
        $this->assertEquals('New Disease', $firstRecord->disease_name);
        $this->assertEquals('2024-02-14', $firstRecord->onset_date->format('Y-m-d'));

        // 古い発症日の記録が後に表示される
        $this->assertEquals('Old Disease', $lastRecord->disease_name);
        $this->assertEquals('2024-01-01', $lastRecord->onset_date->format('Y-m-d'));
    }
}
