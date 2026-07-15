@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Create New Order Draft</span>
        </div>
        <div class="card-body">
            <form action="{{ route('engineer.orders.store') }}" method="POST">
                @csrf
                
                <div class="field-group full" style="margin-bottom: 20px;">
                    <label class="field-label">Select Site</label>
                    <select name="site_id" class="field-input" required>
                        <option value="">-- Choose a site --</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="field-group full" style="margin-bottom: 20px;">
                    <label class="field-label">Priority</label>
                    <select name="priority" class="field-input" required>
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                
                <div class="field-group full" style="margin-bottom: 20px;">
                    <label class="field-label">Requested Delivery Date</label>
                    <input type="date" name="requested_delivery_date" class="field-input">
                </div>
                
                <div class="field-group full" style="margin-bottom: 20px;">
                    <label class="field-label">Notes</label>
                    <textarea name="notes" class="field-input" rows="4" placeholder="Any additional notes..."></textarea>
                </div>
                
                <div style="display:flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Create Draft</button>
                    <a href="{{ route('engineer.orders.index') }}" class="btn btn-secondary" style="text-decoration:none; padding:10px 16px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
