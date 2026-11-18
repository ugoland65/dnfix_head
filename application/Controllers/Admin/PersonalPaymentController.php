<?php
namespace App\Controllers\Admin;

use Exception;
use App\Core\AuthAdmin;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\PersonalPaymentSettlementService;
use App\Utils\Pagination;

class PersonalPaymentController extends BaseClass
{
    public function create(Request $request)
    {
        try {
            $service = new PersonalPaymentSettlementService();
            $actorIdx = (int)(AuthAdmin::getSession('sess_idx') ?? 0);
            $account = $service->getSavedAccount($actorIdx);

            return view('admin.payment.personal_payment_form', [
                'mode' => 'create',
                'settlement' => [
                    'applicant_ad_pk' => $actorIdx,
                    'use_date' => date('Y-m-d'),
                    'pay_method' => '개인 신용카드',
                    'bank' => (string)($account['bank'] ?? ''),
                    'bank_account' => (string)($account['bank_account'] ?? ''),
                    'depositor' => (string)($account['depositor'] ?? ''),
                ],
                'admins' => $service->getAdminOptions(),
                'payMethods' => PersonalPaymentSettlementService::PAY_METHODS,
                'statuses' => PersonalPaymentSettlementService::STATUSES,
                'editable' => true,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function detail(Request $request)
    {
        try {
            $idx = (int)($request->all()['idx'] ?? 0);
            if ($idx <= 0) {
                throw new Exception('정산요청 번호가 없습니다.');
            }

            $service = new PersonalPaymentSettlementService();
            $settlement = $service->getInfo($idx);

            return view('admin.payment.personal_payment_form', [
                'mode' => 'modify',
                'settlement' => $settlement,
                'admins' => $service->getAdminOptions(),
                'payMethods' => PersonalPaymentSettlementService::PAY_METHODS,
                'statuses' => PersonalPaymentSettlementService::STATUSES,
                'editable' => in_array((string)($settlement['status'] ?? ''), PersonalPaymentSettlementService::EDITABLE_STATUSES, true),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function save(Request $request)
    {
        try {
            $requestData = $request->all();
            $service = new PersonalPaymentSettlementService();
            if (($requestData['mode'] ?? '') === 'create') {
                $idx = $service->create($requestData);
            } else {
                $idx = $service->update($requestData);
            }

            return response()->json([
                'success' => true,
                'message' => '개인결제 정산요청을 저장했습니다.',
                'idx' => $idx,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function listData(Request $request): array
    {
        $requestData = $request->all();
        $page = max(1, (int)($requestData['page'] ?? 1));
        $service = new PersonalPaymentSettlementService();
        $list = $service->getList([
            'page' => $page,
            'per_page' => 100,
            'status' => $requestData['s_status'] ?? '',
            'keyword' => $requestData['s_keyword'] ?? '',
            'use_date_start' => $requestData['s_use_date_start'] ?? '',
            'use_date_end' => $requestData['s_use_date_end'] ?? '',
        ]);
        $pagination = new Pagination($list['total'], $list['per_page'], $list['current_page'], 10);

        return [
            'personalList' => $list['data'],
            'personalSummary' => $list['summary'],
            'personalPagination' => $pagination->toArray(),
            'personalPaginationHtml' => $pagination->renderLinks(),
            's_status' => (string)($requestData['s_status'] ?? ''),
            's_keyword' => (string)($requestData['s_keyword'] ?? ''),
            's_use_date_start' => (string)($requestData['s_use_date_start'] ?? ''),
            's_use_date_end' => (string)($requestData['s_use_date_end'] ?? ''),
        ];
    }
}
