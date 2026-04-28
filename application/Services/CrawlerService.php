<?php

namespace App\Services;

use App\Core\BaseClass;

class CrawlerService extends BaseClass
{
    
    private const DOMAIN = "https://www.bestoypn.co.kr";
    private const LOGIN_PAGE_URL = self::DOMAIN . "/intro/member.php";
    private const LOGIN_URL = self::DOMAIN . "/member/login_ps.php";
    private const GOODS_VIEW_URL = self::DOMAIN . "/goods/goods_view.php";
    
    private const LOGIN_ID = "dnfix2024";
    private const LOGIN_PASSWORD = "xkdlfpshf112*";
    
    private $cookieJar;
    private $ch;
    
    public function __construct()
    {
        parent::__construct();
        $this->cookieJar = tempnam(sys_get_temp_dir(), 'bestoypn_cookies');
        $this->initCurl();
    }
    
     /**
      * cURL 초기화
      */
     private function initCurl()
     {
         $this->ch = curl_init();
         
         // PHP 7.1 호환 cURL 설정
         curl_setopt_array($this->ch, [
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_FOLLOWLOCATION => true,
             CURLOPT_MAXREDIRS => 5,
             CURLOPT_TIMEOUT => 30,
             CURLOPT_CONNECTTIMEOUT => 10,
             CURLOPT_COOKIEJAR => $this->cookieJar,
             CURLOPT_COOKIEFILE => $this->cookieJar,
             CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
             CURLOPT_SSL_VERIFYPEER => false,
             CURLOPT_SSL_VERIFYHOST => false,
             CURLOPT_ENCODING => '', // 자동 압축 해제 (gzip, deflate 등)
             // PHP 7.1에서 안정적인 SSL 설정
             CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // HTTP/1.1 강제 사용
             // 쿠키 처리 강화
             CURLOPT_COOKIESESSION => false,
             // 헤더 처리 개선
             CURLOPT_HEADER => false,
             CURLOPT_NOBODY => false,
             CURLOPT_HTTPHEADER => [
                 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                 'Accept-Language: ko-KR,ko;q=0.9,en;q=0.8',
                 'Accept-Encoding: gzip, deflate', // PHP 7.1에서는 br(brotli) 제외
                 'Connection: keep-alive',
                 'Upgrade-Insecure-Requests: 1',
                 'Cache-Control: no-cache'
             ]
         ]);
         
         // PHP 7.1에서 cURL 버전 확인
         if (function_exists('curl_version')) {
             $curlVersion = curl_version();
             if (isset($curlVersion['version'])) {
                 // cURL 버전이 너무 낮으면 경고
                 $version = $curlVersion['version'];
                 if (version_compare($version, '7.40.0', '<')) {
                     error_log("경고: cURL 버전이 낮습니다 ($version). 일부 기능이 제한될 수 있습니다.");
                 }
             }
         }
     }
    
         /**
      * 로그인 수행
      * 
      * @param bool $debug 디버그 모드
      * @param string $targetGoodsNo 로그인 후 이동할 상품번호 (선택사항)
      * @return bool 로그인 성공 여부
      */
     public function login($debug = false, $targetGoodsNo = '1000001528', $customReturnUrl = null)
     {
         try {
             if ($debug) {
                 echo "=== 로그인 시작 ===\n";
             }
             
             // 1. 로그인 페이지 접속하여 필요한 정보 수집
             curl_setopt($this->ch, CURLOPT_URL, self::LOGIN_PAGE_URL);
             curl_setopt($this->ch, CURLOPT_POST, false);
             
             if ($debug) {
                 echo "로그인 페이지 URL: " . self::LOGIN_PAGE_URL . "\n";
             }
             
             $loginPageContent = curl_exec($this->ch);
             
             if (curl_error($this->ch)) {
                 throw new \Exception('로그인 페이지 접속 실패: ' . curl_error($this->ch));
             }
             
             $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
             if ($debug) {
                 echo "로그인 페이지 HTTP 코드: " . $httpCode . "\n";
                 echo "로그인 페이지 HTML 길이: " . strlen($loginPageContent) . "\n";
             }
             
             // 2. 로그인 폼에서 필요한 hidden 필드 추출 (있다면)
             $hiddenFields = $this->extractHiddenFields($loginPageContent);
             
             if ($debug && !empty($hiddenFields)) {
                 echo "발견된 hidden 필드: " . json_encode($hiddenFields) . "\n";
             }
             
             // 로그인 폼 구조 분석
             if ($debug) {
                 echo "=== 로그인 폼 분석 ===\n";
                 // 로그인 폼 찾기
                 if (preg_match('/<form[^>]*>(.*?)<\/form>/is', $loginPageContent, $formMatch)) {
                     echo "로그인 폼 발견됨\n";
                     $formHtml = $formMatch[1];
                     
                     // input 필드들 찾기
                     if (preg_match_all('/<input[^>]*>/i', $formHtml, $inputMatches)) {
                         echo "발견된 input 필드들:\n";
                         foreach ($inputMatches[0] as $input) {
                             echo "  " . $input . "\n";
                         }
                     }
                 } else {
                     echo "로그인 폼을 찾을 수 없음\n";
                 }
                 echo "==================\n";
             }
             
                         if ($debug) {
                // 원본 returnUrl 확인
                if (preg_match('/<input[^>]*name="returnUrl"[^>]*value="([^"]*)"/', $loginPageContent, $returnMatch)) {
                    echo "HTML의 원본 returnUrl: " . $returnMatch[1] . "\n";
                    echo "⚠️ 외부 도메인 returnUrl 발견 - 이것이 로그인 실패 원인일 수 있음\n";
                }
            }
             
                         // 3. 바이담 사이트 정확한 로그인 데이터 구성
            // 사용자 제공 HTML에서 확인된 정확한 필드명: loginId, loginPwd
            $loginData = [
                'loginId' => self::LOGIN_ID,
                'loginPwd' => self::LOGIN_PASSWORD,
            ];
            
            // hidden 필드들과 병합 (returnUrl은 바이담 도메인으로 수정)
            $loginData = array_merge($loginData, $hiddenFields);
            
            // returnUrl 처리: 사이트에서 강제하는 경우 그대로 사용, 아니면 메인 페이지
            if ($customReturnUrl !== null) {
                $loginData['returnUrl'] = $customReturnUrl;
                if ($debug) echo "사용자 지정 returnUrl 사용: {$customReturnUrl}\n";
            } else {
                // 기본적으로 메인 페이지로 설정 (사이트에서 강제 리다이렉트할 수 있음)
                $loginData['returnUrl'] = '/';
                if ($debug) echo "메인 페이지 returnUrl 사용: /\n";
            }
            
            if ($debug) {
                echo "⚠️ 참고: 바이담 사이트는 로그인 후 무조건 메인으로 리다이렉트될 수 있음\n";
                echo "이 경우 로그인 후 별도로 상품 페이지에 접근해야 함\n";
            }
            
            if ($debug) {
                echo "=== 로그인 폼 분석 ===\n";
                echo "감지된 hidden 필드들:\n";
                foreach ($hiddenFields as $name => $value) {
                    echo "  {$name} = {$value}\n";
                }
                echo "최종 로그인 데이터 필드:\n";
                foreach ($loginData as $name => $value) {
                    if ($name === 'loginPwd') {
                        echo "  {$name} = ****\n";
                    } else {
                        echo "  {$name} = {$value}\n";
                    }
                }
                echo "==================\n";
            }
             
             if ($debug) {
                 $debugData = $loginData;
                 $debugData['loginPwd'] = '****'; // 비밀번호 숨김
                 echo "로그인 데이터: " . json_encode($debugData) . "\n";
             }
             
             // 4. 로그인 요청 수행 (Python 방식과 동일하게)
             curl_setopt($this->ch, CURLOPT_URL, self::LOGIN_URL);
             curl_setopt($this->ch, CURLOPT_POST, true);
             curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($loginData));
             curl_setopt($this->ch, CURLOPT_REFERER, self::LOGIN_PAGE_URL);
             curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, false); // Python의 allow_redirects=False와 동일
             
             // Python과 동일한 헤더 설정
             curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
                 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                 'Referer: ' . self::LOGIN_PAGE_URL,
                 'Content-Type: application/x-www-form-urlencoded',
                 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                 'Accept-Language: ko-KR,ko;q=0.9,en;q=0.8',
                 'Accept-Encoding: gzip, deflate, br',
                 'Connection: keep-alive'
             ]);
             
             if ($debug) {
                 echo "로그인 요청 URL: " . self::LOGIN_URL . "\n";
                 echo "POST 데이터: " . http_build_query($loginData) . "\n";
             }
             
             $loginResponse = curl_exec($this->ch);
             
             if (curl_error($this->ch)) {
                 throw new \Exception('로그인 요청 실패: ' . curl_error($this->ch));
             }
             
             // 5. 로그인 성공 여부 확인
             $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
             $effectiveUrl = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
             
             if ($debug) {
                 echo "로그인 응답 HTTP 코드: " . $httpCode . "\n";
                 echo "최종 URL: " . $effectiveUrl . "\n";
                 echo "응답 길이: " . strlen($loginResponse) . "\n";
                 echo "응답 내용 일부: " . substr($loginResponse, 0, 500) . "\n";
                 
                 // 로그인 실패 메시지 확인
                 if (strpos($loginResponse, '일치하지 않습니다') !== false) {
                     echo "❌ 로그인 실패 메시지 발견: '아이디, 비밀번호가 일치하지 않습니다'\n";
                 }
                 if (strpos($loginResponse, 'member.php') !== false) {
                     echo "❌ 응답에서 member.php 발견 - 로그인 페이지로 리다이렉트됨\n";
                 }
                 
                 // JSON 응답 확인
                 $jsonData = json_decode($loginResponse, true);
                 if (json_last_error() === JSON_ERROR_NONE) {
                     echo "JSON 응답 분석:\n";
                     echo json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                 }
             }
             
                         // 로그인 성공 판단 (실패 메시지 우선 확인)
            // 1. 먼저 로그인 실패 메시지 확인
            if (strpos($loginResponse, '일치하지 않습니다') !== false || 
                strpos($loginResponse, '로그인') !== false ||
                strpos($effectiveUrl, 'member.php') !== false) {
                if ($debug) echo "❌ 로그인 실패 감지 - 실패 메시지 또는 로그인 페이지 리다이렉트\n";
                return false;
            }
            
            // 2. HTTP 302/303 리다이렉트 확인 (로그인 실패가 아닌 경우에만)
            if ($httpCode == 302 || $httpCode == 303) {
                if ($debug) echo "✅ 302/303 리다이렉트 감지 - 로그인 성공 가능성\n";
                return true;
            }
             
                         // 2. 쿠키 존재 확인 (Python: self.session.cookies.get_dict())
            $cookieFile = $this->cookieJar;
            if (file_exists($cookieFile) && filesize($cookieFile) > 0) {
                if ($debug) echo "✅ 쿠키 파일 존재 확인 - Python과 동일한 로그인 성공 조건\n";
                return true; // Python과 동일: 쿠키가 있으면 즉시 성공 반환
            }
             
             // 응답 내용으로 로그인 성공 여부 확인
             if (strpos($loginResponse, '로그인') === false && 
                 strpos($loginResponse, 'login') === false &&
                 (strpos($loginResponse, 'mypage') !== false || 
                  strpos($loginResponse, 'main') !== false ||
                  strpos($loginResponse, 'index') !== false)) {
                 if ($debug) echo "응답 내용으로 로그인 성공 확인\n";
                 return true;
             }
             
             // 로그인 실패 메시지 확인 (더 구체적으로)
             if (strpos($loginResponse, '일치하지 않습니다') !== false || 
                 strpos($loginResponse, '아이디') !== false || 
                 strpos($loginResponse, '비밀번호') !== false ||
                 strpos($loginResponse, 'member.php') !== false) {
                 if ($debug) {
                     echo "로그인 실패 메시지 발견\n";
                     echo "응답에서 발견된 오류 관련 텍스트:\n";
                     if (preg_match_all('/.*(?:일치하지|아이디|비밀번호|오류|실패).*/', $loginResponse, $errorMatches)) {
                         foreach ($errorMatches[0] as $errorLine) {
                             echo "  " . trim($errorLine) . "\n";
                         }
                     }
                 }
                 throw new \Exception('로그인 실패: 아이디 또는 비밀번호가 일치하지 않습니다.');
             }
             
             if ($debug) echo "로그인 상태 불명확\n";
             return false;
             
         } catch (\Exception $e) {
             error_log("로그인 오류: " . $e->getMessage());
             if ($debug) {
                 echo "로그인 오류: " . $e->getMessage() . "\n";
             }
             return false;
         }
     }
    
    /**
     * HTML에서 hidden 필드 추출
     * 
     * @param string $html
     * @return array
     */
    private function extractHiddenFields($html)
    {
        $hiddenFields = [];
        
        // hidden input 필드 찾기
        if (preg_match_all('/<input[^>]*type=["\']hidden["\'][^>]*>/i', $html, $matches)) {
            foreach ($matches[0] as $hiddenInput) {
                // name과 value 속성 추출
                if (preg_match('/name=["\']([^"\']+)["\']/', $hiddenInput, $nameMatch) &&
                    preg_match('/value=["\']([^"\']*)["\']/', $hiddenInput, $valueMatch)) {
                    $hiddenFields[$nameMatch[1]] = $valueMatch[1];
                }
            }
        }
        
        return $hiddenFields;
    }
    
    /**
      * 상품 정보 크롤링
      * 
      * @param string $goodsNo 상품코드
      * @param bool $debug 디버그 모드
      * @return array|false 상품 정보 배열 또는 실패 시 false
      */
     public function crawlProduct($goodsNo, $debug = false)
     {
         try {
             if ($debug) {
                 echo "=== 크롤링 시작 ===\n";
                 echo "상품번호: {$goodsNo}\n";
             }
             
             // 쿠키 기반 로그인 상태 확인 (더 안정적)
             if (!file_exists($this->cookieJar) || filesize($this->cookieJar) == 0) {
                 if ($debug) {
                     echo "쿠키 파일이 없음. 로그인 필요\n";
                 }
                 
                 // 로그인 시 returnUrl은 메인 페이지로 설정 (사이트 정책에 따라)
                 if (!$this->login($debug, $goodsNo, '/')) {
                     throw new \Exception('로그인에 실패했습니다.');
                 }
                 
                 if ($debug) {
                     echo "로그인 완료. 이제 상품 페이지에 별도 접근\n";
                 }
             } else {
                 if ($debug) {
                     echo "쿠키 파일 존재 (" . filesize($this->cookieJar) . " bytes). 기존 세션 사용\n";
                 }
             }
             
             // 상품 페이지 URL 생성
             $productUrl = self::GOODS_VIEW_URL . "?goodsNo=" . urlencode($goodsNo);
             
             if ($debug) {
                 echo "요청 URL: " . $productUrl . "\n";
             }
             
            // 상품 페이지 요청 (메인 페이지를 Referer로 설정)
            curl_setopt($this->ch, CURLOPT_URL, $productUrl);
            curl_setopt($this->ch, CURLOPT_POST, false);
            curl_setopt($this->ch, CURLOPT_REFERER, self::DOMAIN . "/main/index.php");
            
            // 추가 헤더 설정 (일반 브라우저 동작 모방)
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: ko-KR,ko;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
                'Cache-Control: no-cache'
            ]);
             
             $productPageContent = curl_exec($this->ch);
             
             if (curl_error($this->ch)) {
                 throw new \Exception('상품 페이지 접속 실패: ' . curl_error($this->ch));
             }
             
             $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
             if ($httpCode !== 200) {
                 throw new \Exception("상품 페이지 접속 실패: HTTP {$httpCode}");
             }
             
            if ($debug) {
                echo "HTTP 응답 코드: " . $httpCode . "\n";
                echo "최종 URL: " . curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL) . "\n";
                echo "HTML 길이: " . strlen($productPageContent) . "\n";
                
                // 리다이렉트 상태 확인
                $finalUrl = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
                if (strpos($productPageContent, 'member.php') !== false || 
                    strpos($productPageContent, '로그인') !== false) {
                    echo "❌ 로그인 페이지로 리다이렉트됨\n";
                    return false;
                } elseif (strpos($finalUrl, 'main/index.php') !== false) {
                    echo "⚠️ 메인 페이지로 리다이렉트됨 - 상품 페이지 직접 접근 재시도\n";
                    
                    // 메인 페이지에서 상품 페이지로 다시 접근 시도
                    curl_setopt($this->ch, CURLOPT_URL, $productUrl);
                    curl_setopt($this->ch, CURLOPT_REFERER, self::DOMAIN . "/main/index.php");
                    
                    $productPageContent = curl_exec($this->ch);
                    $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
                    $finalUrl = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
                    
                    echo "재시도 결과 - HTTP: {$httpCode}, URL: {$finalUrl}\n";
                    
                    if (strpos($finalUrl, 'goods_view.php') !== false) {
                        echo "✅ 상품 페이지 접근 성공 (재시도)\n";
                    } else {
                        echo "❌ 상품 페이지 접근 실패 (재시도 후에도)\n";
                        return false;
                    }
                } else {
                    echo "✅ 상품 페이지 접근 성공\n";
                }

                
                // title 태그 확인
                if (preg_match('/<title>([^<]+)<\/title>/i', $productPageContent, $matches)) {
                    echo "페이지 제목: " . trim($matches[1]) . "\n";
                }
                
                // HTML 일부 출력 (처음 1000자)
                echo "\nHTML 시작 부분:\n";
                echo substr($productPageContent, 0, 1000) . "\n";
                echo "=================\n";
                
                // 상품명 검색 테스트 (다양한 패턴)
                echo "\n=== 상품명 검색 테스트 ===\n";
                $namePatterns = [
                    '/<h1[^>]*>([^<]+)<\/h1>/i' => 'H1',
                    '/<h2[^>]*>([^<]+)<\/h2>/i' => 'H2', 
                    '/<h3[^>]*>([^<]+)<\/h3>/i' => 'H3',
                    '/<div[^>]*class="[^"]*item_detail_tit[^"]*"[^>]*>.*?<h3[^>]*>([^<]+)<\/h3>/is' => '상품명 DIV'
                ];
                
                foreach ($namePatterns as $pattern => $name) {
                    if (preg_match($pattern, $productPageContent, $matches)) {
                        echo "{$name} 태그 발견: " . trim($matches[1]) . "\n";
                    } else {
                        echo "{$name} 태그 없음\n";
                    }
                }
                
                // 최저 판매가 검색 테스트
                echo "\n=== 가격 정보 검색 ===\n";
                if (preg_match('/<div[^>]*id="lowestSellingPrice"/i', $productPageContent)) {
                    echo "✅ lowestSellingPrice div 발견\n";
                } else {
                    echo "❌ lowestSellingPrice div 없음\n";
                }
                
                // 옵션 박스 검색 테스트
                echo "\n=== 옵션 정보 검색 ===\n";
                if (preg_match('/<div[^>]*class="[^"]*item_add_option_box/i', $productPageContent)) {
                    echo "✅ item_add_option_box div 발견\n";
                } else {
                    echo "❌ item_add_option_box div 없음\n";
                }
            }
             
             // 상품 정보 파싱
             return $this->parseProductInfo($productPageContent, $goodsNo, $debug);
             
         } catch (\Exception $e) {
             error_log("상품 크롤링 오류 (상품코드: {$goodsNo}): " . $e->getMessage());
             if ($debug) {
                 echo "오류 발생: " . $e->getMessage() . "\n";
             }
             return false;
         }
     }
    
         /**
      * 로그인 상태 확인
      * 
      * @return bool
      */
     private function isLoggedIn()
     {
         // 메인 페이지에 접속해서 로그인 상태 확인
         curl_setopt($this->ch, CURLOPT_URL, self::DOMAIN . "/");
         curl_setopt($this->ch, CURLOPT_POST, false);
         curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
             'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
             'Accept-Language: ko-KR,ko;q=0.9,en;q=0.8',
             'Accept-Encoding: gzip, deflate, br',
             'Connection: keep-alive',
             'Cache-Control: no-cache'
         ]);
         
         $response = curl_exec($this->ch);
         $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
         $effectiveUrl = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
         
         // 로그인 페이지로 리다이렉트되거나 로그인 폼이 있으면 로그인되지 않은 상태
         if (strpos($effectiveUrl, 'member.php') !== false || 
             strpos($response, '로그인') !== false || 
             strpos($response, 'loginId') !== false) {
             return false;
         }
         
         return true;
     }
    
         /**
      * 상품 정보 파싱 (바이담 구조에 맞춤)
      * 
      * @param string $html
      * @param string $goodsNo
      * @param bool $debug 디버그 모드
      * @return array
      */
     private function parseProductInfo($html, $goodsNo, $debug = false)
     {
         $productInfo = [
             'goods_no' => $goodsNo,
             'name' => '',
             'price' => '',
             'sale_price' => '',
             'lowest_price' => '', // 최저 판매가 추가
             'brand' => '',
             'model' => '',
             'description' => '',
             'images' => [],
             'options' => [],
             'stock' => '',
             'crawled_at' => date('Y-m-d H:i:s')
         ];
         
         // 상품명 추출 (바이담 구조: item_detail_tit > h3)
         if (preg_match('/<div[^>]*class="[^"]*item_detail_tit[^"]*"[^>]*>.*?<h3[^>]*>([^<]+)<\/h3>/is', $html, $matches)) {
             $productInfo['name'] = trim($matches[1]);
         } elseif (preg_match('/<h3[^>]*>([^<]+)<\/h3>/i', $html, $matches)) {
             $productInfo['name'] = trim($matches[1]);
         } elseif (preg_match('/<title>([^<]+)<\/title>/i', $html, $matches)) {
             $productInfo['name'] = trim(str_replace([' - bestoypn', 'bestoypn'], '', $matches[1]));
         }
         
         // 판매가 추출 (바이담 구조: item_price > dd > strong)
         if (preg_match('/<dl[^>]*class="[^"]*item_price[^"]*"[^>]*>.*?<dd[^>]*>.*?<strong[^>]*>.*?<strong[^>]*>([0-9,]+)<\/strong>/is', $html, $matches)) {
             $productInfo['sale_price'] = str_replace(',', '', $matches[1]);
         }
         
         // 최저 판매가 추출 (바이담 특별 구조: #lowestSellingPrice .button span)
         if (preg_match('/<div[^>]*id="lowestSellingPrice"[^>]*>.*?<div[^>]*class="[^"]*button[^"]*"[^>]*>.*?<span[^>]*>([0-9,]+)<\/span>/is', $html, $matches)) {
             $productInfo['lowest_price'] = str_replace(',', '', $matches[1]);
         }
         
         // 상품코드 추출
         if (preg_match('/<dt[^>]*>상품코드<\/dt>.*?<dd[^>]*>([^<]+)<\/dd>/is', $html, $matches)) {
             $productInfo['product_code'] = trim($matches[1]);
         }
         
         // 브랜드 추출 (일반적인 구조)
         if (preg_match('/브랜드[^>]*>([^<]+)</i', $html, $matches)) {
             $productInfo['brand'] = trim($matches[1]);
         }
         
         // 모델명 추출
         if (preg_match('/모델[^>]*>([^<]+)</i', $html, $matches)) {
             $productInfo['model'] = trim($matches[1]);
         }
         
         // 옵션 정보 추출 (바이담 구조: item_add_option_box 안의 select[name="optionSnoInput"])
         if (preg_match('/<div[^>]*class="[^"]*item_add_option_box[^"]*"[^>]*>(.*?)<\/div>/is', $html, $optionBoxMatch)) {
             $optionBoxHtml = $optionBoxMatch[1];
             
             // 옵션명 추출 (dt 태그)
             $optionName = '';
             if (preg_match('/<dt[^>]*>([^<]+)<\/dt>/i', $optionBoxHtml, $dtMatch)) {
                 $optionName = trim($dtMatch[1]);
             }
             
             // 옵션 값들 추출 (select > option)
             if (preg_match('/<select[^>]*name="optionSnoInput"[^>]*>(.*?)<\/select>/is', $optionBoxHtml, $selectMatch)) {
                 $selectHtml = $selectMatch[1];
                 
                 if (preg_match_all('/<option[^>]*value="([^"]*)"[^>]*(?:alt="([^"]*)")?[^>]*>([^<]+)<\/option>/i', $selectHtml, $optionMatches)) {
                     for ($i = 0; $i < count($optionMatches[1]); $i++) {
                         $value = trim($optionMatches[1][$i]);
                         $alt = trim($optionMatches[2][$i]);
                         $text = trim($optionMatches[3][$i]);
                         
                         // 빈 값이나 기본 옵션 텍스트 제외
                         if (!empty($value) && !strpos($text, '옵션') && !strpos($text, '가격') && !strpos($text, '재고')) {
                             // value에서 옵션 정보 파싱 (예: "3437||0||||0^|^OBSIDIAN BLACK")
                             $optionParts = explode('^|^', $value);
                             $optionCode = $optionParts[0] ?? '';
                             $optionName = $optionParts[1] ?? $alt;
                             
                             if (empty($optionName)) {
                                 $optionName = $text;
                             }
                             
                             // 이미지 URL 추출
                             $imgSrc = '';
                             if (preg_match('/data-img-src="([^"]*)"/', $optionMatches[0][$i], $imgMatch)) {
                                 $imgSrc = $imgMatch[1];
                             }
                             
                             $productInfo['options'][] = [
                                 'option_type' => $optionName ?: '색상', // dt에서 추출한 옵션명 사용
                                 'option_code' => $optionCode,
                                 'option_name' => $optionName,
                                 'option_value' => $value,
                                 'option_text' => $text,
                                 'option_image' => $imgSrc,
                                 'alt' => $alt
                             ];
                         }
                     }
                 }
             }
         }
         
         // 상품 이미지 추출 (data-img-src 포함)
         if (preg_match_all('/<img[^>]+(?:src|data-img-src)=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
             foreach ($matches[1] as $imgSrc) {
                 if (strpos($imgSrc, 'goods') !== false || strpos($imgSrc, 'product') !== false || strpos($imgSrc, 'addGoods') !== false) {
                     // 상대 경로면 절대 경로로 변환
                     if (strpos($imgSrc, 'http') !== 0) {
                         $imgSrc = self::DOMAIN . '/' . ltrim($imgSrc, '/');
                     }
                     $productInfo['images'][] = $imgSrc;
                 }
             }
         }
         
         // 상품 설명 추출
         if (preg_match('/<div[^>]*class="[^"]*goods[^"]*desc[^"]*"[^>]*>(.*?)<\/div>/is', $html, $matches)) {
             $productInfo['description'] = trim(strip_tags($matches[1]));
         }
         
         // 재고 정보 추출
         if (preg_match('/재고[^0-9]*([0-9,]+)/i', $html, $matches)) {
             $productInfo['stock'] = str_replace(',', '', $matches[1]);
         }
         
         // 배송비 정보 추출
         if (preg_match('/<dl[^>]*class="[^"]*item_delivery[^"]*"[^>]*>.*?<strong[^>]*>([^<]+)<\/strong>/is', $html, $matches)) {
             $productInfo['delivery_fee'] = trim($matches[1]);
         }
         
         // 할인율 추출
         if (preg_match('/<span[^>]*class="[^"]*sale-rate[^"]*"[^>]*>([0-9]+)<em>%<\/em><\/span>/i', $html, $matches)) {
             $productInfo['discount_rate'] = $matches[1];
         }
         
         return $productInfo;
     }
    
    /**
     * 로그인만 수행 (returnUrl 무관)
     * 
     * @param bool $debug 디버그 모드
     * @return bool 로그인 성공 여부
     */
    public function loginOnly($debug = false)
    {
        if ($debug) {
            echo "=== 로그인 전용 메서드 실행 ===\n";
            echo "returnUrl에 관계없이 로그인만 수행\n";
        }
        
        // 메인 페이지로 returnUrl 설정 (사이트에서 강제할 수 있음)
        $result = $this->login($debug, '1000001528', '/');
        
        if ($debug) {
            echo "로그인 전용 결과: " . ($result ? "성공" : "실패") . "\n";
            echo "이후 별도로 원하는 페이지에 접근 가능\n";
        }
        
        return $result;
    }
    
    /**
     * 여러 상품 일괄 크롤링
     * 
     * @param array $goodsNos 상품코드 배열
     * @return array 크롤링 결과 배열
     */
    public function crawlMultipleProducts($goodsNos)
    {
        $results = [];
        
        foreach ($goodsNos as $goodsNo) {
            $result = $this->crawlProduct($goodsNo);
            $results[$goodsNo] = $result;
            
            // 요청 간격 조절 (서버 부하 방지)
            usleep(500000); // 0.5초 대기
        }
        
        return $results;
    }
    
    /**
     * 리소스 정리
     */
    public function __destruct()
    {
        if ($this->ch) {
            curl_close($this->ch);
        }
        
        if ($this->cookieJar && file_exists($this->cookieJar)) {
            unlink($this->cookieJar);
        }
    }
    
         /**
      * 로그인 테스트 메서드
      * 
      * @return array 로그인 테스트 결과
      */
     public function testLogin()
     {
         echo "<h3>🔍 Python vs PHP 상세 비교 분석 (PHP " . PHP_VERSION . ")</h3>";
         echo "<pre>";
         
         // PHP 7.1 환경 정보 출력
         echo "=== 환경 정보 ===\n";
         echo "PHP 버전: " . PHP_VERSION . "\n";
         if (function_exists('curl_version')) {
             $curlVersion = curl_version();
             echo "cURL 버전: " . $curlVersion['version'] . "\n";
             echo "SSL 버전: " . $curlVersion['ssl_version'] . "\n";
         }
         echo "쿠키 파일 경로: " . $this->cookieJar . "\n\n";
         
         // 1. 초기 상태 정리
         if (file_exists($this->cookieJar)) {
             unlink($this->cookieJar);
             echo "🗑️ 기존 쿠키 파일 삭제\n\n";
         }
         
         // 2. 로그인 전 상품 페이지 접근 (베이스라인)
         echo "=== STEP 1: 로그인 전 상품 페이지 접근 ===\n";
         curl_setopt($this->ch, CURLOPT_URL, self::GOODS_VIEW_URL . "?goodsNo=1000001528");
         curl_setopt($this->ch, CURLOPT_POST, false);
         curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
         
         $beforeResponse = curl_exec($this->ch);
         $beforeCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
         $beforeUrl = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
         
         echo "로그인 전 - HTTP: {$beforeCode}, URL: {$beforeUrl}\n";
         echo "로그인 전 - HTML 길이: " . strlen($beforeResponse) . "\n\n";
         
         // 3. 로그인 시도 (returnUrl 무관하게 로그인만 수행)
         echo "=== STEP 2: 로그인 시도 ===\n";
         $loginResult = $this->login(true);
         echo "로그인 결과: " . ($loginResult ? "✅ 성공" : "❌ 실패") . "\n";
         
         if ($loginResult) {
             echo "💡 로그인 성공 후 어디로 리다이렉트되었는지 확인\n";
         }
         echo "\n";
         
         // 4. 로그인 후 즉시 상품 페이지 접근
         echo "=== STEP 3: 로그인 후 상품 페이지 접근 ===\n";
         curl_setopt($this->ch, CURLOPT_URL, self::GOODS_VIEW_URL . "?goodsNo=1000001528");
         curl_setopt($this->ch, CURLOPT_POST, false);
         curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
         
         $afterResponse = curl_exec($this->ch);
         $afterCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
         $afterUrl = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
         
         echo "로그인 후 - HTTP: {$afterCode}, URL: {$afterUrl}\n";
         echo "로그인 후 - HTML 길이: " . strlen($afterResponse) . "\n";
         
         // 5. 쿠키 분석
         echo "\n=== STEP 4: 쿠키 분석 ===\n";
         if (file_exists($this->cookieJar)) {
             $cookieSize = filesize($this->cookieJar);
             echo "쿠키 파일 크기: {$cookieSize} bytes\n";
             if ($cookieSize > 0) {
                 $cookieContent = file_get_contents($this->cookieJar);
                 $cookieLines = explode("\n", trim($cookieContent));
                 echo "쿠키 라인 수: " . count($cookieLines) . "\n";
                 echo "쿠키 내용 샘플:\n";
                 foreach (array_slice($cookieLines, 0, 3) as $line) {
                     if (!empty(trim($line)) && strpos($line, '#') !== 0) {
                         echo "  " . substr($line, 0, 100) . "...\n";
                     }
                 }
             }
         } else {
             echo "❌ 쿠키 파일 없음\n";
         }
         
         // 6. 최종 판정
         echo "\n=== STEP 5: 최종 분석 ===\n";
         $actualSuccess = (strpos($afterUrl, 'member.php') === false) && 
                         (strpos($afterResponse, '로그인') === false) &&
                         (strlen($afterResponse) > 1000);
         
         echo "PHP 로그인 판정: " . ($loginResult ? "성공" : "실패") . "\n";
         echo "실제 접근 가능: " . ($actualSuccess ? "✅ YES" : "❌ NO") . "\n";
         
         if ($loginResult && !$actualSuccess) {
             echo "\n🔍 Python vs PHP 차이점 분석:\n";
             echo "1. Python requests.Session(): 완전 자동 세션 관리\n";
             echo "2. PHP cURL: 수동 쿠키 관리, 세션 타이밍 이슈 가능\n";
             echo "3. 리다이렉트 처리 방식 차이\n";
             echo "4. 헤더 순서 및 형식 차이\n";
             echo "\n💡 해결 방안:\n";
             echo "- 로그인 후 세션 안정화 대기\n";
             echo "- 쿠키 검증 강화\n";
             echo "- Python requests 방식 완전 모방\n";
         }
         
         echo "</pre>";
         
         return $actualSuccess; // 실제 접근 가능 여부 반환
     }

     /**
      * 크롤링 테스트 메서드
      * 
      * @return array 테스트 결과
      */
     public function test()
    {
        $testResults = [
            'login_test' => false,
            'crawl_test' => false,
            'test_product' => '1000001519',
            'messages' => []
        ];
        
        // 로그인 테스트
        try {
            $testResults['login_test'] = $this->login();
            $testResults['messages'][] = $testResults['login_test'] ? '로그인 성공' : '로그인 실패';
        } catch (\Exception $e) {
            $testResults['messages'][] = '로그인 오류: ' . $e->getMessage();
        }
        
        // 크롤링 테스트 (로그인 성공 시에만)
        if ($testResults['login_test']) {
            try {
                $productInfo = $this->crawlProduct($testResults['test_product']);
                $testResults['crawl_test'] = $productInfo !== false;
                $testResults['product_info'] = $productInfo;
                $testResults['messages'][] = $testResults['crawl_test'] ? '크롤링 성공' : '크롤링 실패';
            } catch (\Exception $e) {
                $testResults['messages'][] = '크롤링 오류: ' . $e->getMessage();
            }
        }
        
        return $testResults;
    }

}
