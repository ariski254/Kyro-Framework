<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;

class ProductController extends Controller {
    public function index(): Response {
        return $this->json(['message' => 'Daftar resource ProductController']);
    }

    public function show($id): Response {
        return $this->json(['message' => 'Detail resource ID: ' . $id]);
    }

    public function store(): Response {
        $data = $this->validate([
            // 'title' => 'required|min:3',
        ]);
        return $this->json(['message' => 'Resource berhasil dibuat', 'data' => $data], 201);
    }

    public function update($id): Response {
        return $this->json(['message' => 'Resource ID ' . $id . ' diperbarui']);
    }

    public function destroy($id): Response {
        return $this->json(['message' => 'Resource ID ' . $id . ' dihapus']);
    }
}
