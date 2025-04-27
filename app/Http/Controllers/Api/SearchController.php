<?php

namespace App\Http\Controllers\Api;

use App\Search\SearchClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
  public function search(Request $request)
  {
    // dd($request->all());
    try {
      $valid = $request->validate([
        'q' => 'required|string|max:255',
        'source' => 'sometimes|string',
      ]);
    } catch (\Exception $e) {
      return response()->json(['status' => 'success', 'data' => []]);
    }
    
    if (isset($valid['source'])) {
      $data = SearchClient::findIn($valid['q'], $valid['source']);
    } else {
      $data = SearchClient::full($valid['q']);
    }

    return response()->json([
      'status' => 'success',
      'data' => $data,
    ]);
  }
}
