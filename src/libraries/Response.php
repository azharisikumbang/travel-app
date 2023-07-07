<?php

class Response 
{
    public function notFound() : void
    {
        http_response_code(404);
        html_not_found();
        exit();
    }

    public function redirectTo(string $to, mixed $message = null, bool $permanent = true) : void
    {
        if ($message) session()->add('temp', $message);
        header('Location: ' . $to, true, $permanent ? 301 : 302);
        exit();
    }

    public function toJson(mixed $data, int $code = 200) : void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($data);
        exit();
    }

    public function jsonNotFound(string $message = 'Not Found.') : void
    {
        $this->toJson([
            'message' => $message, 
            'code' => 404
        ], 404);
    }

    public function badRequest(mixed $errors): void
    {
        $this->toJson(data: [
            'message' => 'BAD_REQUEST',
            'code' => 400,
            'errors' => is_array($errors) ? $errors : [$errors]
        ], code: 400);
    }

    public function unauthorized(mixed $errors): void
    {
        $this->toJson(data: [
            'message' => 'UNAUTHORIZED',
            'code' => 403,
            'errors' => !is_array($errors) ? [$errors] : $errors
        ], code: 403);
    }

    public function serverError(mixed $errors): void
    {
        $this->toJson(data: [
            'message' => 'SERVER_ERROR',
            'code' => 500,
            'errors' => !is_array($errors) ? [$errors] : $errors
        ], code: 500);
    }

    public function jsonOk(mixed $data, int $code = 200, string $message = 'Resources Found.'): void
    {
        $this->toJson([
            'message' => $message,
            'code' => $code,
            'data' => $data
        ]);
    }
}