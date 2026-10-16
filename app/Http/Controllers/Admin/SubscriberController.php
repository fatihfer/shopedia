<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function index(): View
    {
        $subscribers = Subscriber::latest()->paginate(20);

        return view('admin.subscribers.index', compact('subscribers'));
    }
}
