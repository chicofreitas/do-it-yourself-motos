<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Lists all users.
     */
    public function index()
    {
        $users = User::all();

        return response()->json($users, 201);
    }
}
