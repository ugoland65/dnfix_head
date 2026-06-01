<?php

namespace App\Controllers\Admin;

use Throwable;
use App\Core\BaseClass;
use App\Classes\Request;
use App\Services\CalendarService;

class CalendarController extends BaseClass
{
    private $calendarService;

    public function __construct()
    {
        parent::__construct();
        $this->calendarService = new CalendarService();
    }

    /**
     * 메인 달력
     */
    public function calendar(Request $request)
    {
        try {
            $requestData = $request->all();
            $data = $this->calendarService->buildCalendarViewData($requestData);

            return view('admin.calendar.calendar', $data);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 캘린더 등록/상세 폼
     */
    public function calendarReg(Request $request)
    {
        try {
            $requestData = $request->all();
            $data = $this->calendarService->getCalendarRegFormData($requestData);

            return view('admin.calendar.calendar_reg', $data);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 캘린더 신규 생성
     */
    public function createCalendar(Request $request)
    {
        try {
            $requestData = $request->all();
            $result = $this->calendarService->createCalendar($requestData);
            return response()->json($result);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 캘린더 수정 저장
     */
    public function saveCalendar(Request $request)
    {
        try {
            $requestData = $request->all();
            $result = $this->calendarService->saveCalendar($requestData);
            return response()->json($result);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * 캘린더 삭제
     */
    public function deleteCalendar(Request $request)
    {
        try {
            $requestData = $request->all();
            $result = $this->calendarService->deleteCalendar($requestData);
            return response()->json($result);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage(),
            ], 400);
        }
    }
}
