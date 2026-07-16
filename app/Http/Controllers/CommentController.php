<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $order->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            $comment->load('user.roles');
            $role = '';
            $roleBg = '';
            $roleColor = '';
            
            if ($comment->user->hasRole('engineer')) {
                $role = 'Engineer'; $roleBg = '#e3f2fd'; $roleColor = '#1565c0';
            } elseif ($comment->user->hasRole('manager')) {
                $role = 'Manager'; $roleBg = '#fce4ec'; $roleColor = '#c2185b';
            } elseif ($comment->user->hasRole('workshop')) {
                $role = 'Workshop'; $roleBg = '#fff3e0'; $roleColor = '#e65100';
            }

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'author' => $comment->user->name,
                    'role' => $role,
                    'roleBg' => $roleBg,
                    'roleColor' => $roleColor,
                    'time_diff' => $comment->created_at->diffForHumans(),
                    'time_exact' => $comment->created_at->format('M d, Y h:i A'),
                    'body' => nl2br(e($comment->body))
                ]
            ]);
        }

        return back()->with('success', 'Comment added successfully.');
    }
}
