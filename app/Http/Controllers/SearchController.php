<?php

namespace App\Http\Controllers;

use App\Search\SearchClient;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  public function search(Request $request)
  {
    $valid = $request->validate([
      'q' => 'required|string|max:255',
    ]);
    $data = SearchClient::full($valid['q']);
    return response()->json([
      'status' => 'success',
      'data' => $data,
    ]);
  }
}
