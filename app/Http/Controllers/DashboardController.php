<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Event;
use App\Models\Report;
use App\Models\Sector;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        $userCount = User::count();
        $eventCount = Event::count();
        $postCount = Post::count();
        $reportCount = Report::count();

        $postWithMostInteractions = Post::postWithMostInteractions();
        $sectorsWithMostPosts = Sector::sectorsWithMostPosts();

        return view('dashboard', compact('userCount', 'eventCount', 'postCount', 'reportCount','postWithMostInteractions','sectorsWithMostPosts'));
    }
}
