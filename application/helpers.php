<?php

use App\Core\Application;
use App\Core\View;
use App\Core\RedirectResponse;
use App\Core\Config;

/**
 * 세션 초기화 (저장 경로 설정)
 */
if (!function_exists('init_session')) {
    function init_session() {
        if (session_status() === PHP_SESSION_NONE) {
            // 세션 저장 경로 설정
            // DOCUMENT_ROOT가 /nirvanan835/www 이면
            // 한 단계 위인 /nirvanan835/session 으로 설정
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $parentDir = dirname($docRoot);
            $sessionPath = $parentDir . '/session';
            
            // 디렉토리가 없으면 생성
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0755, true);
            }
            
            session_save_path($sessionPath);
            session_start();
        }
    }
}

/**
 * 애플리케이션 컨테이너에 접근하는 헬퍼 함수
 * 
 * @param string|null $key 가져올 서비스 키
 * @return mixed 서비스 키가 제공되면 해당 서비스, 아니면 애플리케이션 인스턴스
 */
function app($key = null)
{
    $app = Application::getInstance();
    
    if ($key === null) {
        return $app;
    }
    
    return $app->get($key);
} 

/**
 * 디버깅
 * 라라벨 스타일 덤퍼
 * @param mixed ...$vars
 */
if (!function_exists('dump')) {
    function dump(...$vars)
    {
        $isCli = (PHP_SAPI === 'cli');

        // 스타일 wrapper
        if (!$isCli) {
            echo "<pre style='background:#1e1e1e;color:#dcdcdc;padding:12px 14px;border-radius:8px;"
               . "font:13px/1.45 Menlo,Consolas,monospace;white-space:pre-wrap;tab-size:2;'>\n";
        }

        $seen = []; // 순환 참조 방지용(객체)
        $isHtml = !$isCli;
        foreach ($vars as $var) {
            echo _laravel_dump_render($var, 0, $seen, $isCli, $isHtml);
            echo "\n";
        }

        if (!$isCli) {
            echo "</pre>";
        }
    }
}

/**
 * 디버깅 종료
 * @param mixed ...$vars
 */
if (!function_exists('dd')) {
    function dd(...$vars)
    {
        dump(...$vars);
        exit;
    }
}

/**
 * 색상 유틸(웹/CLI 공통)
 */
if (!function_exists('_d_color')) {
    function _d_color($text, $role, $isCli, $isHtml)
    {
        // VS Code 다크 테마 톤에 맞춘 팔레트
        static $htmlColors = [
            'bool'        => '#4FC1FF', // 하늘
            'null'        => '#9CDCFE', // 밝은 시안
            'number'      => '#B5CEA8', // 연녹
            'string_len'  => '#DCDCAA', // 노랑(길이)
            'string'      => '#CE9178', // 주황
            'key'         => '#9CDCFE', // 키/속성명
            'punct'       => '#D4D4D4', // 구두점/괄호
            'class'       => '#4EC9B0', // 민트
            'vis'         => '#C586C0', // 접근자 +/#/-
            'static'      => '#C586C0', // static
            'resource'    => '#C586C0',
            'note'        => '#808080', // 보조, depth limit 등
        ];

        static $cliColors = [
            'bool'        => "\033[36m",   // Cyan
            'null'        => "\033[96m",   // Bright Cyan
            'number'      => "\033[92m",   // Green
            'string_len'  => "\033[93m",   // Yellow
            'string'      => "\033[91m",   // Red
            'key'         => "\033[96m",   // Bright Cyan
            'punct'       => "\033[37m",   // Gray
            'class'       => "\033[36m",   // Cyan
            'vis'         => "\033[95m",   // Magenta
            'static'      => "\033[95m",   // Magenta
            'resource'    => "\033[95m",   // Magenta
            'note'        => "\033[90m",   // Dark Gray
        ];

        if ($isHtml) {
            $color = $htmlColors[$role] ?? '#dcdcdc';
            return "<span style=\"color:{$color}\">{$text}</span>";
        }
        if ($isCli) {
            $start = $cliColors[$role] ?? "\033[0m";
            $end   = "\033[0m";
            return $start . $text . $end;
        }
        return $text; // 폴백
    }
}

/**
 * 내부 렌더러
 */
if (!function_exists('_laravel_dump_render')) {
    function _laravel_dump_render($var, $level, array &$seen, $isCli=false, $isHtml=false)
    {
        $indent = str_repeat('  ', $level);
        $next   = str_repeat('  ', $level + 1);
        $maxDepth = 10;

        if ($level >= $maxDepth) {
            return $indent . _d_color("…(depth limit)", 'note', $isCli, $isHtml);
        }

        // === 스칼라/기본형 ===
        if (is_bool($var)) {
            return $indent . _d_color($var ? 'true' : 'false', 'bool', $isCli, $isHtml);
        }
        if (is_null($var)) {
            return $indent . _d_color('null', 'null', $isCli, $isHtml);
        }
        if (is_int($var) || is_float($var)) {
            return $indent . _d_color((string)$var, 'number', $isCli, $isHtml);
        }
        if (is_string($var)) {
            $len = mb_strlen($var, 'UTF-8');
            $esc = $isHtml ? htmlspecialchars($var, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $var;
            $lenPart = _d_color("string($len)", 'string_len', $isCli, $isHtml);
            $quote   = _d_color('"', 'punct', $isCli, $isHtml);
            $body    = _d_color($esc, 'string', $isCli, $isHtml);
            return $indent . "{$lenPart} {$quote}{$body}{$quote}";
        }

        // === 배열 ===
        if (is_array($var)) {
            $count = count($var);
            if ($count === 0) {
                $arr = _d_color('array:0', 'string_len', $isCli, $isHtml)
                     . ' ' . _d_color('[]', 'punct', $isCli, $isHtml);
                return $indent . $arr;
            }

            $hdr = _d_color("array:$count", 'string_len', $isCli, $isHtml)
                 . ' ' . _d_color('[', 'punct', $isCli, $isHtml);
            $out = $indent . $hdr . "\n";
            foreach ($var as $k => $v) {
                $keyStr = is_int($k) ? (string)$k
                                     : '"' . ($isHtml ? htmlspecialchars($k, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $k) . '"';
                $kPart  = _d_color($keyStr, 'key', $isCli, $isHtml);
                $arrow  = _d_color(' => ', 'punct', $isCli, $isHtml);

                $rendered = _laravel_dump_render($v, $level + 1, $seen, $isCli, $isHtml);
                $rendered = preg_replace('/^' . preg_quote($next, '/') . '/m', '', $rendered, 1);

                $out .= $next . $kPart . $arrow . $rendered . "\n";
            }
            $out .= $indent . _d_color(']', 'punct', $isCli, $isHtml);
            return $out;
        }

        // === 객체 ===
        if (is_object($var)) {
            $oid  = function_exists('spl_object_id') ? spl_object_id($var) : null;
            $hash = $oid !== null ? $oid : spl_object_hash($var);

            if (isset($seen[$hash])) {
                $cls = _d_color(get_class($var), 'class', $isCli, $isHtml);
                return $indent . $cls . ' ' . _d_color("{#{$hash} …}", 'punct', $isCli, $isHtml);
            }
            $seen[$hash] = true;

            $ref   = new ReflectionClass($var);
            $class = _d_color($ref->getName(), 'class', $isCli, $isHtml);
            $hdrL  = _d_color('{', 'punct', $isCli, $isHtml);
            $hdrR  = _d_color('}', 'punct', $isCli, $isHtml);
            $hashTag = _d_color('#'.$hash, 'note', $isCli, $isHtml);

            $out = $indent . $class . " " . _d_color("{", 'punct', $isCli, $isHtml) . $hashTag . "\n";

            foreach ($ref->getProperties() as $prop) {
                $prop->setAccessible(true);
                $vis = $prop->isPublic() ? '+' : ($prop->isProtected() ? '#' : '-');
                $visC = _d_color($vis, 'vis', $isCli, $isHtml);
                $stat = $prop->isStatic() ? _d_color(' static', 'static', $isCli, $isHtml) : '';
                $name = _d_color($prop->getName(), 'key', $isCli, $isHtml);

                try {
                    $val = $prop->getValue($var);
                    $rendered = _laravel_dump_render($val, $level + 1, $seen, $isCli, $isHtml);
                    $rendered = preg_replace('/^' . preg_quote($next, '/') . '/m', '', $rendered, 1);
                } catch (Throwable $e) {
                    $rendered = _d_color('…(inaccessible)', 'note', $isCli, $isHtml);
                }

                $colon = _d_color(': ', 'punct', $isCli, $isHtml);
                $out .= $next . "{$visC}{$stat} {$name}{$colon}" . $rendered . "\n";
            }

            // 동적 프로퍼티
            foreach (get_object_vars($var) as $k => $v) {
                if ($ref->hasProperty($k) && $ref->getProperty($k)->isPublic()) continue;
                $visC = _d_color('+', 'vis', $isCli, $isHtml);
                $name = _d_color($k, 'key', $isCli, $isHtml);
                $colon = _d_color(': ', 'punct', $isCli, $isHtml);

                $rendered = _laravel_dump_render($v, $level + 1, $seen, $isCli, $isHtml);
                $rendered = preg_replace('/^' . preg_quote($next, '/') . '/m', '', $rendered, 1);

                $out .= $next . "{$visC} {$name}{$colon}" . $rendered . "\n";
            }

            $out .= $indent . $hdrR;
            return $out;
        }

        // === 리소스 ===
        if (is_resource($var)) {
            $type = get_resource_type($var);
            $res  = _d_color("resource($type)", 'resource', $isCli, $isHtml);
            return $indent . $res;
        }

        // 폴백
        $txt = var_export($var, true);
        if ($isHtml) $txt = htmlspecialchars($txt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return $indent . $txt;
    }
}

/**
 * 뷰 생성
 * 
 * @param string $template
 * @param array $data
 * @return View
 */
if (!function_exists('view')) {
    function view(string $template, array $data = []): View {
        return View::make($template, $data);
    }
}

/**
 * 레이아웃 지정 (Blade의 @extends 대체)
 * 
 * @param string $layout
 * @param array $data
 * @return void
 * 
 * @TODO 메서드명을 블레이드 처럼 extends로 변경할지 고민중
 */
if (!function_exists('extends_layout')) {
    function extends_layout(string $layout, array $data = []): void {
        // 뷰 인스턴스가 뷰 파일 내부에서 $this 로 전달되므로,
        // 여기서는 전역적으로 현재 View 객체를 접근하도록 설계 필요
        if ($GLOBALS['__current_view__'] instanceof View) {
            $GLOBALS['__current_view__']->extends($layout, $data);
        }
    }
}

/**
 * 리다이렉트 팩토리 클래스
 */
class RedirectFactory {
    public function back(int $status = 302, array $headers = []): RedirectResponse
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return new RedirectResponse($referer, $status, $headers);
    }
    
    public function to(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return new RedirectResponse($url, $status, $headers);
    }
}

/**
 * 리다이렉트
 *
 * @param string|null $url 이동할 경로 (null이면 RedirectFactory 반환)
 * @param int $status HTTP 상태 코드 (기본 302)
 * @param array $headers 추가 헤더
 * @return RedirectResponse|RedirectFactory
 */
if (!function_exists('redirect')) {
    function redirect(?string $url = null, int $status = 302, array $headers = []) {
        if ($url === null) {
            return new RedirectFactory();
        }
        return new RedirectResponse($url, $status, $headers);
    }
}


/**
 * config 파일 설정 값 가져오기
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('config')) {
    function config(string $key, $default = null) {
        return Config::get($key, $default);
    }
}