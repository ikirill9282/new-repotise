<?php

namespace App\Http\Controllers\Api;

use App\Search\SearchClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserOptions;

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
      $source = explode(',', $valid['source']);
      if ($source[0] == 'country') {
        return response()->json([
          'status' => 'success',
          'data' => UserOptions::select('country')
              ->distinct()
              ->where('country', 'like', "%{$valid['q']}%")
              ->get()
              ->map(function($hit) {
                return [
                  'id' => $hit['country'],
                  'label' => $hit['country'],
                  'slug' => $hit['country'],
                  'source' => $hit['country'],
                ];
              })
        ]);
      }

      $data = SearchClient::findIn($valid['q'], $source);
    } else {
      $data = SearchClient::full($valid['q']);
    }

    return response()->json([
      'status' => 'success',
      'data' => $data,
    ]);
  }
}
