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
     * テストの前処理
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfToken();
        $this->enableSecurityToken();
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

    /**
     * 記録の詳細表示テスト
     * 
     * @return void
     */
    public function testView(): void
    {
        // 1. テストデータの作成
        $records = $this->getTableLocator()->get('Records');
        $record = $records->newEntity([
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
            'is_deleted' => false,
        ]);
        $records->save($record);

        // 2. 関連する通院記録の作成
        $hospitalVisits = $this->getTableLocator()->get('HospitalVisits');
        $visit = $hospitalVisits->newEntity([
            'record_id' => $record->id,
            'hospital_name' => 'Test Hospital',
            'visit_datetime' => '2024-02-14 15:00:00',
            'created_at' => '2024-02-14 10:00:00',
            'modified_at' => '2024-02-14 10:00:00',
        ]);
        $hospitalVisits->save($visit);

        // 3. /records/view/1 へのGETリクエストを実行
        $this->get('/records/view/' . $record->id);

        // 4. レスポンスのテスト
        $this->assertResponseSuccess();

        // 5. ビュー変数のテスト
        $viewRecord = $this->viewVariable('record');
        $this->assertNotEmpty($viewRecord);
        $this->assertEquals('Test Disease', $viewRecord->disease_name);

        // 6. 関連する通院記録のテスト
        $this->assertNotEmpty($viewRecord->hospital_visits);
        $this->assertEquals(1, count($viewRecord->hospital_visits));
        $this->assertEquals('Test Hospital', $viewRecord->hospital_visits[0]->hospital_name);
    }

    /**
     * 存在しない記録のIDでviewアクションを実行した場合のテスト
     * 
     * @return void
     */
    public function testViewNotFound(): void
    {
        // 1. 存在しないIDでGETリクエストを実行
        $this->get('/records/view/999999');

        // 2. レスポンスのテスト
        $this->assertResponseError();  // 4xx系エラー
        $this->assertResponseCode(404);  // Not Found
    }

    /**
     * GETリクエストでaddアクションを実行した場合のテスト（フォーム表示）
     */
    public function testAddGet(): void
    {
        // GETリクエストを実行
        $this->get('/records/add');

        // レスポンスのテスト
        $this->assertResponseSuccess();
        $this->assertResponseCode(200);

        // ビュー変数のテスト
        $this->assertInstanceOf('App\Model\Entity\Record', $this->viewVariable('record'));
    }

    /**
     * 正常なPOSTリクエストでaddアクションを実行した場合のテスト
     */
    public function testAddPost(): void
    {
        $data = [
            'onset_date' => '2024-02-14',
            'disease_name' => 'Test Disease',
            'severity' => 1,
            'complications' => 'Test Complications',
            'medications' => 'Test Medications',
        ];

        // POSTリクエストを実行
        $this->post('/records/add', $data);

        // リダイレクトのテスト
        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Records', 'action' => 'index']);

        // フラッシュメッセージのテスト
        $this->assertFlashMessage('記録を保存しました。');

        // データベースに保存されたことを確認
        $records = $this->getTableLocator()->get('Records');
        $query = $records->find()->where(['disease_name' => 'Test Disease']);
        $result = $query->first();

        $this->assertNotEmpty($result);
        $this->assertEquals('Test Disease', $result->disease_name);
        $this->assertEquals('2024-02-14', $result->onset_date->format('Y-m-d'));
    }

    /**
     * バリデーションエラーとなるPOSTリクエストでaddアクションを実行した場合のテスト
     */
    public function testAddPostValidationError(): void
    {
        $data = [
            'onset_date' => '',  // 必須項目を空に
            'disease_name' => '', // 必須項目を空に
            'severity' => 'invalid',  // 不正な値
        ];

        // POSTリクエストを実行
        $this->post('/records/add', $data);

        // レスポンスのテスト
        $this->assertResponseSuccess();
        $this->assertResponseCode(200);  // バリデーションエラー時は200 OK

        // エラーの検証
        $record = $this->viewVariable('record');
        $this->assertGreaterThan(0, $record->getErrors());
    }
}
