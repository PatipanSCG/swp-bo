<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WamVerifyRequest;
use App\Services\WamAuth;            // <-- เพิ่ม
use Carbon\Carbon;     
use App\Services\MailService;              // <-- ใช้ parse วันที่

class FetchWamData extends Command
{
    protected $signature = 'wam:fetch';
    protected $description = 'ดึงข้อมูลจาก WAM API ทุกๆ 1 ชั่วโมง';

    public function __construct(private WamAuth $auth) // <-- ฉีด service ผ่าน constructor
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // 1) ล็อกอินเอา accessToken
            $token = $this->auth->login();
            print_r($token);
            if (empty($token)) {
                $this->error('Login to WAM failed: ไม่ได้ accessToken');
                return Command::FAILURE;
            }

            // 2) เรียก verify-request โดยใช้ token ข้างบน
       
            $base = 'https://wam.dit.go.th';

            $response = Http::withHeaders([
                'accept'          => 'application/json',
                'accept-language' => 'en-US,en;q=0.9,th;q=0.8,en-GB;q=0.7',
                'authorization'   => 'Bearer ' . $token,
                'referer'         => $base . '/verify-request/list',
                'user-agent'      => 'Mozilla/5.0',
            ])
                ->withCookies([
                    'CBWM-language'   => 'en',
                    // ใส่คุกกี้อื่นเท่าที่จำเป็นพอ
                    'accessToken'     => $token,
                    'firstLogin'      => '0',
                    // ถ้าปลายทางเคร่งเรื่องสิทธิ์ ค่อยเติม userAbilityRules / userData
                ], 'wam.dit.go.th')
                ->withOptions([
                    'version' => CURL_HTTP_VERSION_1_1,
                    'curl'    => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4],
                    'http_errors' => false,
                ])
                ->timeout(60)->connectTimeout(10)
                ->get($base . '/api/v1/verify-request', [
                    'page'    => 1,
                    'limit'   => 10,
                    'orderBy' => 'desc',
                    // อย่าส่งคีย์ว่าง เช่น sortBy, SheetNo ฯลฯ ถ้าไม่มีค่า
                ]);

            if (!$response->successful()) {
                dd('HTTP ' . $response->status(), $response->headers(), $response->body());
            }

            $json = $response->json();
            print_r($json);

            $items = data_get($json, 'data', []);
            print_r($items);
            $inserted = 0;
            $updated  = 0;

            foreach ($items as $it) {
                $sheetNo = (string) data_get($it, 'SheetNo', '');
                $rowNum  = (int) data_get($it, 'RowNum', 0);
                if ($sheetNo === '') {
                    continue;
                }

                $payload = [
                    'sheet_no'              => $sheetNo,
                    'total_request'         => data_get($it, 'TotalRequest'),
                    'have_inspector'        => (bool) data_get($it, 'haveInspector', false),
                    'merchan_name'          => data_get($it, 'MerchanName'),
                    'receive_business_type' => data_get($it, 'ReceiveBusinessType'),
                    'status'                => data_get($it, 'Status', ''),
                    'create_time'           => $this->toTs(data_get($it, 'CreateTime')),
                    'vb_first_name'         => data_get($it, 'vbFirstName'),
                    'vb_last_name'          => data_get($it, 'vbLastName'),
                    'vb_merchant_name'      => data_get($it, 'vbMerchantName'),
                    'vb_id_card'            => data_get($it, 'vbIDCard'),
                    'prov_id'               => data_get($it, 'ProvId'),
                    'vb_branch_name'        => data_get($it, 'vbBranchName'),
                    'date_of_request_start' => $this->toTs(data_get($it, 'dateOfRequestStart')),
                    'vb_prov_name'          => data_get($it, 'vbProvName'),
                    'customer_name'         => data_get($it, 'CustomerName'),
                    'row_num'               => $rowNum,
                ];

                $existing = WamVerifyRequest::where('sheet_no', $sheetNo)
                    ->where('row_num', $rowNum)
                    ->first();

                if (!$existing) {
                    WamVerifyRequest::create($payload);
                    $inserted++;
                     MailService::sendRaw(
                            'kasmanee@scggroup.com', 
                            '[SWP]มีราการตรวจ สุวรรณภูมิใหม่ ', 
                            "มีรายการใหม่ เลขที่ใบคำขอ: {$sheetNo}, ลูกค้า: {$payload['customer_name']}"
                        );

                } else {
                    $newStatus = $payload['status'] ?? '';
                    if ($existing->status !== $newStatus) {
                        $existing->fill($payload)->save();
                        $updated++;
                    }
                }
            }

            $msg = "WAM sync OK. inserted={$inserted}, updated={$updated}";
            $this->info($msg);
            Log::info($msg);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('เกิดข้อผิดพลาด WAM API', ['message' => $e->getMessage()]);
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function toTs($val)
    {
        if (empty($val)) return null;
        try {
            return Carbon::parse($val);
        } catch (\Throwable) {
            return null;
        }
    }
}
