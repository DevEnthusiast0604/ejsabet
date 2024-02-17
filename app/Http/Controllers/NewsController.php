<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function search(Request $request) {
        $mod = new Post();
        if ($request->get('keyword') != '') {
            $keyword = $request->get('keyword');
            $mod = $mod->where(function ($query) use ($keyword) {
                return $query->where('title', 'like', "%$keyword%")
                            ->orWhere('text', 'like', "%$keyword%");
            });
        }
        $mod = $mod->where('image_url', 'not like', "%preview-image%");
        $data = $mod->orderBy('published_at', 'desc')->paginate(12);
        return $this->sendResponse($data);
    }
}
