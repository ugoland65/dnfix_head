<?php

namespace App\Utils;

class Pagination
{
    public $total;        // 총 데이터 개수
    public $perPage;      // 페이지당 항목 수
    public $currentPage;  // 현재 페이지 번호
    public $lastPage;     // 마지막 페이지 번호
	public $visiblePages;  // 화면에 보여질 페이지 번호 개수

	public function __construct($total, $perPage, $currentPage, $visiblePages = 10)
    {
        $this->total = $total;
        $this->perPage = max(1, $perPage); // 최소 1개 이상
        $this->currentPage = max(1, $currentPage); // 최소 페이지는 1
        $this->lastPage = (int) ceil($total / $this->perPage); // 마지막 페이지 계산
		$this->visiblePages = max(1, $visiblePages); // 최소 1개 이상
    }

    public function toArray()
    {
        return [
            'total' => $this->total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
			'visible_pages' => $this->visiblePages,
        ];
    }

	public function links($baseUrl = '')
	{
		$links = [];

		// 기존 URL 파라미터 분석
		$parsedUrl = parse_url($baseUrl);
		$queryParams = [];
		if (isset($parsedUrl['query'])) {
			parse_str($parsedUrl['query'], $queryParams);
		}

		// 페이지 링크 생성
		for ($i = 1; $i <= $this->lastPage; $i++) {
			$queryParams['page'] = $i; // 현재 페이지 번호 설정
			$url = $this->buildUrl($parsedUrl, $queryParams);

			$links[] = [
				'page' => $i,
				'url' => $url,
				'active' => $i == $this->currentPage, // 현재 페이지 여부
			];
		}

		return $links;
	}

	public function renderLinks($baseUrl = '')
    {
        $html = '<nav class="pagination"><ul>';

        $queryParams = [];

        // baseUrl이 비어있다면 현재 URL 사용
        if (empty($baseUrl)) {
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
            $urlComponents = parse_url($currentUrl);
            $baseUrl = $urlComponents['path'] ?? '';

            if (isset($urlComponents['query'])) {
                parse_str($urlComponents['query'], $queryParams);

                // page 파라미터 제거
                unset($queryParams['page']);

                if (!empty($queryParams)) {
                    $baseUrl .= '?' . http_build_query($queryParams);
                }
            }
        }

        // 기존 URL 파라미터 분석
        $parsedUrl = parse_url($baseUrl);
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);
        }

        // 시작 및 끝 페이지 계산
        $startPage = max(1, $this->currentPage - (int) floor($this->visiblePages / 2));
        $endPage   = min($this->lastPage, $startPage + $this->visiblePages - 1);

        // 맨 처음 버튼
        if ($this->currentPage > 1) {
            $queryParams['page'] = 1;
            $firstUrl = $this->buildUrl($parsedUrl, $queryParams);
            $html .= '<li class="first-page"><a href="' . $firstUrl . '">맨 처음</a></li>';
        }

        // 이전 버튼
        if ($this->currentPage > 1) {
            $queryParams['page'] = $this->currentPage - 1;
            $prevUrl = $this->buildUrl($parsedUrl, $queryParams);
            $html .= '<li class="prev-page"><a href="' . $prevUrl . '">이전</a></li>';
        }

        // 페이지 번호 버튼
        for ($i = $startPage; $i <= $endPage; $i++) {
            $queryParams['page'] = $i;
            $pageUrl = $this->buildUrl($parsedUrl, $queryParams);
            $activeClass = $i == $this->currentPage ? ' class="active"' : '';
            $html .= '<li' . $activeClass . '><a href="' . $pageUrl . '">' . $i . '</a></li>';
        }

        // 다음 버튼
        if ($this->currentPage < $this->lastPage) {
            $queryParams['page'] = $this->currentPage + 1;
            $nextUrl = $this->buildUrl($parsedUrl, $queryParams);
            $html .= '<li class="next-page"><a href="' . $nextUrl . '">다음</a></li>';
        }

        // 맨 끝 버튼
        if ($this->currentPage < $this->lastPage) {
            $queryParams['page'] = $this->lastPage;
            $lastUrl = $this->buildUrl($parsedUrl, $queryParams);
            $html .= '<li class="last-page"><a href="' . $lastUrl . '">맨 끝</a></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }


	private function buildUrl($parsedUrl, $queryParams)
	{
		// URL 재구성
		$scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
		$host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
		$path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
		$query = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';

		return $scheme . $host . $path . $query;
	}



}
