<?php

class Response 
{
    public function toJson(mixed $data, int $code = 200) : void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode($data);
        exit();
    }

    public function jsonNotFound(string $message) : void
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
            'errors' => !is_array($errors) ? [$errors] : $errors
        ], code: 400);
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