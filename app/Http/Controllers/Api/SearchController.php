<?php

namespace App\Http\Controllers\Api;

use App\Search\SearchClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Country;
use App\Models\UserOptions;
use Illuminate\Support\Facades\Crypt;

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
          'data' => Country::query()
            ->where('name', 'like', "%{$valid['q']}%")
            ->get()
            ->map(function($hit) {
              return [
                'id' => Crypt::encrypt($hit->id),
                'label' => $hit->name,
                'slug' => $hit->name,
                'source' => $hit->name,
              ];
            })
        ]);
      }

      if ($source[0] == 'language') {
        return response()->json([
          'status' => 'success',
          'data' => Language::query()
            ->where('name', 'like', "%{$valid['q']}%")
            ->get()
            ->map(fn($hit) => [
              'id' => Crypt::encrypt($hit->id),
              'label' => $hit->name,
              'slug' => $hit->name,
              'source' => $hit->name,
            ])
        ]);
      }

      if ($source[0] == 'creators') $source[0] = 'users';

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
