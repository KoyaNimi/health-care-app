<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\RecordsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\RecordsController Test Case
 *
 * @uses \App\Controller\RecordsController
 */
class RecordsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Records' => ['truncate' => true],
        'app.HospitalVisits' => ['truncate' => true],
    ];

    /**
     * Test index method
     *
     * @return void
     * @uses \App\Controller\RecordsController::index()
     */
    public function testIndex(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

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
        $this->assertResponseOk();

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
        $this->assertResponseOk();

        // 5. ビュー変数のテスト
        $records = $this->viewVariable('records');
        $this->assertNotEmpty($records);                                  // レコードが存在することを確認
        $this->assertEquals(1, count($records));                         // レコード数が1件であることを確認
        $this->assertEquals('Test Disease', $records->first()->disease_name); // 病名が正しいことを確認
    }
}
