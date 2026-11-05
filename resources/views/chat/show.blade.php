@extends('layouts.main')

@section('content')
<div class="container py-4">
    
    <!-- Tombol Kembali -->
    <div class="mb-3">
       
        <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm">
                &larr; Kembali ke Pesan
        </a>
    </div>

    

    <!-- Kotak Obrolan -->
    <div class="card border-custom shadow-sm d-flex flex-column" style="border-radius: 12px; height: 75vh;">
        
        <!-- Header Chat -->
        <div class="card-header bg-light border-bottom border-custom py-3 d-flex align-items-center gap-3">
            <!-- Inisial Lawan Bicara -->
            <div class="bg-primary-custom text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 45px; height: 45px; font-weight: bold; font-size: 1.2rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h6 class="fw-bold text-dark m-0">{{ $user->name }}</h6>
                <small class="text-muted">{{ ucfirst($user->role) }}</small>
            </div>
        </div>

        <!-- Area Pesan (Scrollable) -->
        <div class="card-body p-4 overflow-auto bg-white" id="chat-container" style="flex-grow: 1;">
            @forelse($messages as $message)
                @if($message->sender_id === auth()->id())
                    <!-- Gelembung Pesan Sendiri (Kanan) -->
                    <div class="d-flex justify-content-end mb-3">
                        <div style="max-width: 75%;">
                            <div class="bg-primary-custom text-white p-3 rounded-3 shadow-sm" style="border-bottom-right-radius: 0 !important;">
                                {{ $message->message }}
                            </div>
                            <div class="text-end text-muted small mt-1" style="font-size: 0.7rem;">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Gelembung Pesan Lawan Bicara (Kiri) -->
                    <div class="d-flex justify-content-start mb-3">
                        <div style="max-width: 75%;">
                            <div class="bg-light border text-dark p-3 rounded-3 shadow-sm" style="border-bottom-left-radius: 0 !important;">
                                {{ $message->message }}
                            </div>
                            <div class="text-start text-muted small mt-1" style="font-size: 0.7rem;">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center text-muted py-5 d-flex flex-column align-items-center justify-content-center h-100">
                    <i class="bi bi-chat-square-quote mb-2" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="m-0">Belum ada pesan. Mulailah menyapa <strong>{{ $user->name }}</strong>!</p>
                </div>
            @endforelse
        </div>

        <!-- Form Kirim Pesan -->
        <div class="card-footer bg-light border-top border-custom p-3">
            <form action="{{ route('chat.store', $user->id) }}" method="POST" class="d-flex gap-2 m-0">
                @csrf
                <input type="text" name="message" class="form-control form-control-lg border-custom  fs-6" placeholder="Ketik pesan Anda di sini..." required autocomplete="off" autofocus>
                <button type="submit" class="btn bg-accent text-white  px-4 fw-bold">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>

    </div>
</div>

<!-- Script untuk Auto-Scroll ke Bawah -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatContainer = document.getElementById('chat-container');
        // Gulung kotak obrolan hingga ke titik paling bawah
        chatContainer.scrollTop = chatContainer.scrollHeight;
    });
</script>
@endsection