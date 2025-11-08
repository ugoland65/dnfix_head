<?php
namespace App\Core;

class RedirectResponse
{
    protected string $url;
    protected int $status;
    protected array $headers = [];
    protected bool $deferSend = false;
    protected bool $sent = false;

    public function __construct(string $url, int $status = 302, array $headers = [])
    {
        $this->url = $url;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function send()
    {
        if ($this->sent) return;
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        header("Location: {$this->url}");
        $this->sent = true;
        exit;
    }

    public function with(string $key, $value): self
    {
        init_session(); // 세션 저장 경로 설정 포함
        $_SESSION['_flash'][$key] = $value;
        $this->deferSend = true;
        return $this;
    }

    /** 
     * 뒤로 가기 (HTTP_REFERER 기반)
     */
    public static function back(int $status = 302, array $headers = []): self
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return new self($referer, $status, $headers);
    }

    public function __destruct()
    {
        if (!$this->sent) {
            $this->send();
        }
    }
}
