<div class="comments-wrapper">
    <div class="comments-section">
        <h3 style="font-family: 'Barlow Condensed', sans-serif; font-size: 20px; color: var(--navy); margin-bottom: 16px;">
        Discussion ({{ $order->comments->count() }})
    </h3>

    <div class="chat-container">
        @foreach($order->comments->sortBy('created_at') as $comment)
            <div class="chat-message {{ $comment->user_id == auth()->id() ? 'chat-mine' : 'chat-theirs' }}">
                <div class="comment-card">
                    <div class="comment-header">
                        <div>
                            <span class="comment-author">{{ $comment->user->name }}</span>
                            @if($comment->user->hasRole('engineer'))
                                <span class="comment-role" style="background:#e3f2fd; color:#1565c0;">Engineer</span>
                            @elseif($comment->user->hasRole('manager'))
                                <span class="comment-role" style="background:#fce4ec; color:#c2185b;">Manager</span>
                            @elseif($comment->user->hasRole('workshop'))
                                <span class="comment-role" style="background:#fff3e0; color:#e65100;">Workshop</span>
                            @endif
                        </div>
                        <span class="comment-time" title="{{ $comment->created_at->diffForHumans() }}">
                            {{ $comment->created_at->format('M d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="comment-body">
                        {!! nl2br(e($comment->body)) !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="comment-form">
        <form action="{{ route('orders.comments.store', $order) }}" method="POST">
            @csrf
            <textarea name="body" placeholder="Write a comment..." required></textarea>
            <div class="comment-form-actions">
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </div>
        </form>
    </div>
    </div>
</div>
