<?php

namespace App\Http\Controllers;

use App\Models\Parser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MainController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function getJson(Request $request): JsonResponse
    {
        // getting data from <textarea>
        $text = trim($request->input('text'));

        $data = explode("\n", trim($text));
        $parser = new Parser($data);
        $parsedData = $parser->getData();
        return response()->json($parsedData);
    }
}
