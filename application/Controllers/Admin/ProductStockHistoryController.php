<?php

namespace App\Controllers\Admin;

use Exception;
use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\ProductStockHistoryService;

class ProductStockHistoryController extends BaseClass
{

    /**
     * 상품 재고 이력 조회
     * 
     * @param int $idx
     * @return array
     */
    public function productStockHistoryListApi(Request $request)
    {

        try{

            $requestData = $request->all();

            $payload = [
                'start_date' => $requestData['s_date'] ?? date('Y-m-d'),
                'end_date' => $requestData['e_date'] ?? date('Y-m-d'),
            ];

            $productStockHistoryService = new ProductStockHistoryService();
            $productStockHistoryList = $productStockHistoryService->getProductStockHistoryList($payload);

            $data = [
                'productStockHistoryList' => $productStockHistoryList,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

    }


    /**
     * 일일재고 임시저장
     * 
     * @param Request $request
     * @return array
     */
    public function saveDailyStockTemp(Request $request)
    {
        try {
            $requestData = $request->all();

            $payload = [
                'file_name' => $requestData['file_name'] ?? '',
                'mode' => $requestData['mode'] ?? 'p',
                'start_date' => $requestData['start_date'] ?? date('Y-m-d'),
                'end_date' => $requestData['end_date'] ?? date('Y-m-d'),
            ];

            $productStockHistoryService = new ProductStockHistoryService();
            $result = $productStockHistoryService->saveDailyStockTemp($payload);

            return response()->json([
                'status' => 'success',
                'message' => '일일재고 임시저장이 완료되었습니다.',
                'data' => [
                    'uid' => (int)($result['uid'] ?? 0),
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * 일일 재고관리 (엑셀) 페이지
     *
     * @param Request $request
     * @return mixed
     */
    public function stockExcelPage(Request $request)
    {
        try {
            $data = [
                'title' => '일일 재고관리 (엑셀)',
                'idx' => $request->input('idx', ''),
            ];

            return view('admin.stock.stock_excel', $data)
                ->extends('admin.layout.layout', [
                    'pageGroup2' => 'order',
                    'pageNameCode' => 'stock_excel',
                ]);
        } catch (Throwable $e) {
            return view('admin.errors.404', [
                'message' => $e->getMessage(),
            ])->response(404);
        }
    }


    /**
     * 일일 재고관리 상세 HTML (우측 패널)
     *
     * @param Request $request
     * @return mixed
     */
    public function stockExcelView(Request $request)
    {
        try {
            $idx = $request->input('idx', '');
            $sort = $request->input('sort', 'qty');

            $productStockHistoryService = new ProductStockHistoryService();
            $data = $productStockHistoryService->getStockExcelView($idx, $sort);

            return view('admin.stock.stock_excel_view', $data);
        } catch (Throwable $e) {
            return '<div class="p-10">' . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</div>';
        }
    }


    /**
     * 일일재고 엑셀 업로드
     *
     * @param Request $request
     * @return mixed
     */
    public function uploadStockExcel(Request $request)
    {
        try {
            if (!$request->hasFile('userfile')) {
                throw new Exception('파일을 첨부해 주세요');
            }

            $productStockHistoryService = new ProductStockHistoryService();
            $result = $productStockHistoryService->uploadStockExcel($request->file('userfile'));

            return redirect('/admin/stock/stock_excel?idx=' . (int)($result['uid'] ?? 0));
        } catch (Throwable $e) {
            echo "<script>alert(" . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . ");history.back();</script>";
            exit;
        }
    }


    /**
     * 일일 재고 입출고 등록
     *
     * @param Request $request
     * @return array
     */
    public function registerDayStock(Request $request)
    {
        try {
            $requestData = $request->all();

            $productStockHistoryService = new ProductStockHistoryService();
            $result = $productStockHistoryService->registerDayStock([
                'stock_key' => $requestData['stock_key'] ?? [],
                'stock_mode' => $requestData['stock_mode'] ?? [],
                'stock_qry' => $requestData['stock_qry'] ?? [],
                'stock_kind' => $requestData['stock_kind'] ?? [],
                'stock_memo' => $requestData['stock_memo'] ?? [],
                'stock_day' => $requestData['stock_day'] ?? '',
                'stock_history_idx' => $requestData['stock_history_idx'] ?? '',
            ]);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'idx' => $result['idx'] ?? '',
                'msg' => '완료',
                'message' => '완료',
            ]);
        } catch (Throwable $e) {
            $statusCode = (int)$e->getCode();
            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            return response()->json([
                'status' => 'error',
                'success' => false,
                'msg' => $e->getMessage(),
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }


    /**
     * 일일 재고 입출고 되돌리기
     *
     * @param Request $request
     * @return array
     */
    public function revertDayStock(Request $request)
    {
        try {
            $requestData = $request->all();

            $productStockHistoryService = new ProductStockHistoryService();
            $result = $productStockHistoryService->revertDayStock([
                'stock_history_idx' => $requestData['stock_history_idx'] ?? ($requestData['idx'] ?? ''),
            ]);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'idx' => $result['idx'] ?? '',
                'msg' => '재고 수량을 되돌렸습니다.',
                'message' => '재고 수량을 되돌렸습니다.',
            ]);
        } catch (Throwable $e) {
            $statusCode = (int)$e->getCode();
            if ($statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            return response()->json([
                'status' => 'error',
                'success' => false,
                'msg' => $e->getMessage(),
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }


    /**
     * 일일재고 이력 삭제
     *
     * @param Request $request
     * @return array
     */
    public function deleteDayStock(Request $request)
    {
        try {
            $idx = $request->input('idx', '');

            $productStockHistoryService = new ProductStockHistoryService();
            $productStockHistoryService->deleteDayStock($idx);

            return response()->json([
                'status' => 'success',
                'success' => true,
                'msg' => '완료',
                'message' => '완료',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'success' => false,
                'msg' => $e->getMessage(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}