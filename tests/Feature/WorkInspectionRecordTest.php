<?php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class WorkInspectionRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_work_inspection_record_successfully()
    {
        // สร้างผู้ใช้จำลองและ login
        $user = User::factory()->create();
        $this->actingAs($user);

        // เตรียมข้อมูลทดสอบ
        $payload = [
            'WorkID' => 1,
            'StationID' => 1,
            'DispenserID' => 1,
            'NozzleID' => 1,
            'NozzleNumber' => '1-000001-2-1234-56',
            'MitterBegin' => 100.0,
            'MitterEnd' => 105.0,
            'MMQ_1L' => 0.01,
            'MMQ_5L' => 0.05,
            'MPE_5L' => 0.02,
            'Repeat5L_1' => 0.01,
            'Repeat5L_2' => 0.02,
            'Repeat5L_3' => 0.01,
            'KFactor' => 123.45,
            'ExpirationDate' => now()->addYear()->toDateString(),
            'KarudaNumber' => 'K123456',
            'SealNumber' => 'S78910',
            'Status' => 1
        ];

        $response = $this->postJson('/api/inspection-records', $payload); // เปลี่ยน URL ให้ตรง

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'บันทึกข้อมูลตรวจสอบสำเร็จ',
                 ]);

        $this->assertDatabaseHas('work_inspection_records', [
            'WorkID' => 1,
            'NozzleNumber' => '1-000001-2-1234-56',
            'created_by' => $user->id
        ]);
    }
}
