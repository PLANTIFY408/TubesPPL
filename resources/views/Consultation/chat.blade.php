@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Chat Header -->
    <div class="bg-gradient-to-r from-white to-gray-50 rounded-t-2xl shadow-lg p-6 border-b border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-user text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Chat dengan {{ $chatPartnerName }}</h1>
                <div class="flex items-center space-x-2 mt-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600">Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Box -->
    <div id="chat-box" class="bg-gradient-to-b from-white to-gray-50 rounded-b-2xl shadow-lg p-6 h-96 overflow-y-auto mb-6 border-l border-r border-gray-100">
        @foreach($messages as $message)
            <div class="mb-4 flex @if($message->sender_id === Auth::id()) justify-end @endif transform transition-all duration-300 ease-out">
                <div class="flex flex-col max-w-xs group">
                    <div class="@if($message->sender_id === Auth::id()) text-right @else text-left @endif">
                        @if($message->image)
                            <a href="{{ asset('storage/' . $message->image) }}" target="_blank" class="block mb-2">
                                <img src="{{ asset('storage/' . $message->image) }}" alt="Gambar" class="rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 w-full">
                            </a>
                        @endif
                        @if($message->message)
                             <p class="inline-block px-6 py-3 rounded-2xl font-medium shadow-md transform transition-all duration-300 hover:shadow-lg @if($message->sender_id === Auth::id()) bg-gradient-to-br from-green-500 to-green-600 text-white @else bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800 @endif @if($message->image) mt-2 @endif">
                                {{ $message->message }}
                            </p>
                        @endif
                    </div>
                     <small class="text-gray-500 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 @if($message->sender_id === Auth::id()) self-end @else self-start @endif">{{ $message->created_at->format('H:i') }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Chat Input -->
    <div class="bg-white rounded-2xl shadow-lg p-4 border border-gray-100">
        <form id="chat-form" action="{{ route('consultation.sendMessage', $otherUser->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-3">
            @csrf
            <div class="flex-grow relative">
                <input type="text" name="message" id="message-input" placeholder="Ketik pesan Anda..." class="w-full px-6 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 transition-all duration-300 focus:transform focus:-translate-y-1 focus:shadow-lg">
            </div>
            
            <label for="image-upload" class="bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 p-4 rounded-2xl cursor-pointer transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-md">
                <i class="fas fa-paperclip text-lg"></i>
                <input type="file" name="image" id="image-upload" accept="image/*" class="hidden">
            </label>

            <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-lg">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatPartnerId = {{ $otherUser->id }};
    let lastMessageId = {{ $messages->last() ? $messages->last()->id : 0 }};

    // Function to fetch new messages
    function fetchMessages() {
        fetch(`{{ route('consultation.chat', $otherUser->id) }}?last_message_id=${lastMessageId}`, {
            headers: {
                 'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                const currentScrollTop = chatBox.scrollTop;
                const isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= currentScrollTop + 1;

                data.messages.forEach(message => {
                    const messageElement = `
                        <div class="mb-4 flex ${message.sender_id === {{ Auth::id() }} ? 'justify-end' : ''} transform transition-all duration-300 ease-out animate-slide-in">
                            <div class="flex flex-col max-w-xs group">
                                <div class="${message.sender_id === {{ Auth::id() }} ? 'text-right' : 'text-left'}">
                                    ${message.image ? `<a href="/storage/${message.image}" target="_blank" class="block mb-2"><img src="/storage/${message.image}" alt="Gambar" class="rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 w-full"></a>` : ''}
                                    ${message.message ? `<p class="inline-block px-6 py-3 rounded-2xl font-medium shadow-md transform transition-all duration-300 hover:shadow-lg ${message.sender_id === {{ Auth::id() }} ? 'bg-gradient-to-br from-green-500 to-green-600 text-white' : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800'} ${message.image ? 'mt-2' : ''}">${message.message}</p>` : ''}
                                </div>
                                <small class="text-gray-500 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ${message.sender_id === {{ Auth::id() }} ? 'self-end' : 'self-start'}">${message.created_at}</small>
                            </div>
                        </div>
                    `;
                    chatBox.innerHTML += messageElement;
                });

                lastMessageId = data.messages[data.messages.length - 1].id;

                if (isScrolledToBottom) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            }
        })
        .catch(error => console.error('Error fetching messages:', error));
    }

    // Auto-refresh every 3 seconds
    setInterval(fetchMessages, 3000);

    // Handle form submission with AJAX
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.action, {
            method: this.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.message) {
                messageInput.value = '';
                document.getElementById('image-upload').value = '';

                const message = data.message;
                 const messageElementHTML = `
                    <div class="mb-4 flex ${message.sender_id === {{ Auth::id() }} ? 'justify-end' : ''} transform transition-all duration-300 ease-out animate-slide-in">
                        <div class="flex flex-col max-w-xs group">
                            <div class="${message.sender_id === {{ Auth::id() }} ? 'text-right' : 'text-left'}">
                                 ${message.image ? `<a href="/storage/${message.image}" target="_blank" class="block mb-2"><img src="/storage/${message.image}" alt="Gambar" class="rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 w-full"></a>` : ''}
                                 ${message.message ? `<p class="inline-block px-6 py-3 rounded-2xl font-medium shadow-md transform transition-all duration-300 hover:shadow-lg ${message.sender_id === {{ Auth::id() }} ? 'bg-gradient-to-br from-green-500 to-green-600 text-white' : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-800'} ${message.image ? 'mt-2' : ''}">${message.message}</p>` : ''}
                            </div>
                             <small class="text-gray-500 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ${message.sender_id === {{ Auth::id() }} ? 'self-end' : 'self-start'}">${message.formatted_created_at}</small>
                        </div>
                    </div>
                `;

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = messageElementHTML.trim();
                const newMessageElement = tempDiv.firstChild;

                chatBox.appendChild(newMessageElement);

                setTimeout(() => {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }, 50);

                lastMessageId = message.id;
            }
        })
        .catch(error => console.error('Error sending message:', error));
    });

    // Initial scroll to bottom on load
    setTimeout(() => {
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 100);

    // Handle Enter key
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    // Auto focus on input
    messageInput.focus();
</script>

<style>
    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endsection