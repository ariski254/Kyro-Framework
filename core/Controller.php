<?php

namespace Core;

class Controller {
    protected function request(): Request {
        return Request::capture();
    }

    protected function view(string $view, array $data = [], int $status = 200): Response {
        return Response::view($view, $data, $status);
    }

    protected function json($data, int $status = 200): Response {
        return Response::json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): Response {
        return Response::redirect($url, $status);
    }

    protected function validate(array $rules): array {
        return $this->request()->validate($rules);
    }
}