<?php
namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\PaymentRequestService;
use App\Utils\Pagination;
use App\Services\OrderGroupService;

class PaymentRequestController extends BaseClass
{

    /**
     * 결제요청 목록 조회
     * 
     * @param Request $request
     * @return view
     */
    public function paymentRequestList(Request $request)
    {
        try{

            $requestData = $request->all();

            $page = (int)($requestData['page'] ?? ($requestData['pn'] ?? 1));
            if ($page < 1) {
                $page = 1;
            }

            $status = $requestData['s_status'] ?? '요청';
            $keyword = $requestData['s_keyword'] ?? '';

            $payload = [
                'page' => $page,
                'per_page' => 100,
                'status' => $status,
                'keyword' => $keyword,
            ];
            $paymentRequestService = new PaymentRequestService();
            $paymentRequestList = $paymentRequestService->getPaymentRequestList($payload);

            $pagination = new Pagination(
                $paymentRequestList['total'],
                $paymentRequestList['per_page'],
                $paymentRequestList['current_page'],
                10
            );
            $paginationHtml = $pagination->renderLinks();

            $data = [
                's_status' => $status,
                's_keyword' => $keyword,
                'paymentRequestList' => $paymentRequestList['data'],
                'pagination' => $pagination->toArray(),
                'paginationHtml' => $paginationHtml,
            ];

            return view('admin.payment.payment_request_list', $data)
                ->extends('admin.layout.layout',[
                    'pageGroup2' => 'work',
                    'pageNameCode' => 'payment_request_list'
                ]);


        } catch (Exception $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }

    /**
     * 결제요청 생성페이지
     * 
     * @param Request $request
     * @return view
     */
    public function paymentRequestCreate(Request $request)
    {
        try{

            $requestData = $request->all();

            $kind = $requestData['kind'] ?? '';
            $category = $requestData['category'] ?? '';
            $amount = $requestData['amount'] ?? 0;
            $kind_idx = null;
            $currency = 'KRW';

            if( $kind === 'order_sheet' ){
                $orderSheetIdx = $requestData['orderSheetIdx'] ?? null;
                if( $orderSheetIdx <= 0 ){
                    throw new Exception("주문서 번호가 없습니다.");
                }

                $kind_idx = $orderSheetIdx;

                //$orderSheetInfo = $OrderSheetService->getOrderSheetInfo($orderSheetIdx);

                $orderGroupIdx = $requestData['orderGroupIdx'] ?? null;
                if( $orderGroupIdx <= 0 ){
                    throw new Exception("주문서 그룹 번호가 없습니다.");
                }

                $OrderGroupService = new OrderGroupService();
                $orderGroupInfo = $OrderGroupService->getOrderGroupInfo($orderGroupIdx);

                $bank = $orderGroupInfo['bank']['domestic']['bank'] ?? '';
                $bank_account = $orderGroupInfo['bank']['domestic']['account'] ?? '';
                $depositor = $orderGroupInfo['bank']['domestic']['depositor'] ?? '';
                $importAccount = $orderGroupInfo['bank']['import_account'] ?? '';
                $oogGroup = $orderGroupInfo['oog_group'] ?? '';
                $currencyMap = [
                    'ko' => 'KRW',
                    'jp' => 'JPY',
                    'cn' => 'CNY',
                    'dol' => 'USD',
                    'etc' => 'USD',
                ];
                $currency = $currencyMap[$oogGroup] ?? 'KRW';


            }

            $data = [
                'mode' => 'create',
                'category' => $category,
                'kind' => $kind,
                'kind_idx' => $kind_idx,
                'currency' => $currency,
                'amount' => $amount,
                'bank' => $bank ?? '',
                'bank_account' => $bank_account ?? '',
                'depositor' => $depositor ?? '',
                'importAccount' => $importAccount ?? '',
            ];

            return view('admin.payment.payment_request_detail', $data);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);

        }
    }


    /**
     * 결제요청 상세 조회
     * 
     * @param Request $request
     * @return view
     */
    public function paymentRequestDetail(Request $request)
    {
        try{
            
            $requestData = $request->all();
            $idx = $requestData['idx'] ?? null;
            if( $idx <= 0 ){
                throw new Exception("결제요청 번호가 없습니다.");
            }

            $paymentRequestService = new PaymentRequestService();
            $paymentRequest = $paymentRequestService->getPaymentRequestInfo($idx);

            $category = $paymentRequest['category'] ?? '';
            $kind = $paymentRequest['kind'] ?? '';
            $kind_idx = $paymentRequest['kind_idx'] ?? '';
            $currency = $paymentRequest['currency'] ?? '';
            $amount = $paymentRequest['amount'] ?? 0;
            $is_vat = $paymentRequest['is_vat'] ?? 'Y';
            $bank = $paymentRequest['bank'] ?? '';
            $bank_account = $paymentRequest['bank_account'] ?? '';
            $depositor = $paymentRequest['depositor'] ?? '';
            $importAccount = $paymentRequest['importAccount'] ?? '';

            $data = [
                'mode' => 'modify',
                'paymentRequest' => $paymentRequest ?? [],
                'idx' => $idx,
                'category' => $category,
                'kind' => $kind,
                'kind_idx' => $kind_idx,
                'currency' => $currency,
                'amount' => $amount,
                'bank' => $bank ?? '',
                'bank_account' => $bank_account ?? '',
                'depositor' => $depositor ?? '',
                'importAccount' => $importAccount ?? '',
            ];

            return view('admin.payment.payment_request_detail', $data);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    /**
     * 결제요청 저장
     * 
     * @param Request $request
     * @return json
     */
    public function paymentRequestSave(Request $request)
    {
        try{

            $requestData = $request->all();

            $mode = $requestData['mode'] ?? '';
            $paymentRequestService = new PaymentRequestService();
            if( $mode == 'create' ){
                $paymentRequest = $paymentRequestService->createPaymentRequest($requestData);
            }else{
                $paymentRequest = $paymentRequestService->updatePaymentRequest($requestData);
            }

            return response()->json([
                'success' => true,
                'message' => '결제요청 저장 완료',
                'data' => $paymentRequest,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

}