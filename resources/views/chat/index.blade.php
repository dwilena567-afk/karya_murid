@extends('layouts.main')

@section('content')

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary-custom m-0">Pesan</h4>
    </div>

<div class="container py-4">
    
    <div class="card border-custom shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <!--
        <div class="card-header bg-light border-bottom border-custom py-3">
            <h6 class="fw-bold text-primary-custom m-0">Daftar Percakapan</h6>
        </div>
        -->
        <div class="list-group list-group-flush">
            @forelse($conversations as $user)
                <a href="{{ route('chat.show', $user->id) }}" class="list-group-item list-group-item-action p-3 d-flex align-items-center gap-3 border-bottom">
                    <!-- Avatar / Inisial User -->
                    <div class="bg-primary-custom text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-weight: bold; font-size: 1.2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <!-- Informasi Lawan Bicara & Pesan Terakhir -->
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold text-dark m-0 text-truncate">{{ $user->name }}</h6>
                            @if($user->last_message)
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ $user->last_message->created_at->diffForHumans() }}
                                </small>
                            @endif
                        </div>
                        <p class="text-muted small m-0 text-truncate" style="max-width: 500px;">
                            @if($user->last_message)
                                @if($user->last_message->sender_id === auth()->id())
                                    <span class="fw-semibold">Anda: </span>
                                @endif
                                {{ $user->last_message->message }}
                            @else
                                <span class="fst-italic">Belum ada pesan.</span>
                            @endif
                        </p>
                    </div>

                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-dots text-muted" style="font-size: 3.5rem;"></i>
                    <h5 class="text-muted mt-3">Belum ada obrolan.</h5>
                    
                    
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection