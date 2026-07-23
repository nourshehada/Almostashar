@props(['context' => 'home', 'attemptId' => null, 'alternativeIndex' => null])

<!-- Chatbot Floating Button -->
<button id="chatbot-toggle" class="fixed bottom-6 right-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center z-50 transition-transform">
    <i class="fas fa-comments text-2xl"></i>
    <span id="chatbot-badge"  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center animate-pulse">
        1
    </span>
</button>

<!-- Chatbot Window -->
<div id="chatbot-window" class="fixed bottom-24 right-6 w-96 max-w-[calc(100vw-3rem)] h-[500px] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 hidden flex-col">

    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4 rounded-t-2xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/50 rounded-full flex justify-center">
                <img src="{{ asset('images/advisor-chat.png') }}"
                     alt="المستشار"
                     class="w-12 h-12 rounded-full object-cover">
            </div>
            <div>
                <h3 class="font-bold text-sm">{{ app()->getLocale() === 'ar' ? 'المستشار الذكي' : 'Almostashar' }}</h3>
                <p class="text-xs text-blue-100">{{ app()->getLocale() === 'ar' ? 'متصل الآن' : 'Online now' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button id="chatbot-clear" class="text-white/80 hover:text-white text-sm" title="{{ app()->getLocale() === 'ar' ? 'مسح المحادثة' : 'Clear chat' }}">
                <i class="fas fa-trash-alt"></i>
            </button>
            <button id="chatbot-close" class="text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Messages Container -->
    <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-3 chat-scroll">
        <!-- Welcome Message -->
        <div class="flex gap-2">
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-robot text-blue-600 text-sm"></i>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl rounded-tl-sm p-3 max-w-[80%]">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    @if($context === 'home')
                        {{ app()->getLocale() === 'ar' ? 'مرحباً! 👋 أنا المستشار الذكي. يمكنني مساعدتك في فهم التطبيق أو الإجابة عن أسئلتك. كيف يمكنني مساعدتك؟' : 'Hello! 👋 I am the Smart Advisor. I can help you understand the app or answer your questions. How can I help you?' }}
                    @elseif($context === 'results')
                        {{ app()->getLocale() === 'ar' ? 'مرحباً!  تهانينا على إكمال الاختبار. يمكنني مساعدتك في فهم نتيجتك والتخصص المقترح. ما الذي تريد معرفته؟' : 'Hello! 👋 Congratulations on completing the quiz. I can help you understand your results and the recommended major. What would you like to know?' }}
                    @elseif($context === 'alternative')
                        {{ app()->getLocale() === 'ar' ? 'مرحباً! 👋 يمكنني مساعدتك في فهم هذا التخصص البديل ومقارنته بالتخصص الأفضل. ما سؤالك؟' : 'Hello! 👋 I can help you understand this alternative major and compare it with the best one. What is your question?' }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Suggestions -->
    <div id="chatbot-suggestions" class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap gap-2">
            @if($context === 'home')
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'ما هذا التطبيق؟' : 'What is this app?' }}
                </button>
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'كم عدد الأسئلة؟' : 'How many questions?' }}
                </button>
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'هل هو مجاني؟' : 'Is it free?' }}
                </button>
            @elseif($context === 'results')
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'لماذا هذا التخصص؟' : 'Why this major?' }}
                </button>
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'ما نقاط قوتي؟' : 'What are my strengths?' }}
                </button>
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'كيف أبدأ؟' : 'How to start?' }}
                </button>
            @elseif($context === 'alternative')
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'الفرق بين التخصصين؟' : 'Difference between majors?' }}
                </button>
                <button class="suggestion-btn text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/40 transition">
                    {{ app()->getLocale() === 'ar' ? 'المسارات المهنية؟' : 'Career paths?' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Input Area -->
    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex gap-2">
            <input
                type="text"
                id="chatbot-input"
                placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب سؤالك...' : 'Type your question...' }}"
                class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                maxlength="500"
            >
            <button
                id="chatbot-send"
                class="w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition"
                disabled
            >
                <i class="fas fa-paper-plane text-sm"></i>
            </button>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
    .chat-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .chat-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .chat-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
    .chat-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .message-enter {
        animation: fadeIn 0.3s ease-out;
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 8px 12px;
    }
    .typing-indicator span {
        width: 6px;
        height: 6px;
        background: #94a3b8;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-4px); }
    }
</style>

<!-- JavaScript -->
<script>
(function() {
    const context = @json($context);
    const attemptId = @json($attemptId);
    const alternativeIndex = @json($alternativeIndex);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const toggleBtn = document.getElementById('chatbot-toggle');
    const chatWindow = document.getElementById('chatbot-window');
    const closeBtn = document.getElementById('chatbot-close');
    const clearBtn = document.getElementById('chatbot-clear');
    const messagesContainer = document.getElementById('chatbot-messages');
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');

    let isOpen = false;

    // Toggle chat window
    toggleBtn.addEventListener('click', () => {
        isOpen = !isOpen;
        chatWindow.classList.toggle('hidden', !isOpen);
        chatWindow.classList.toggle('flex', isOpen);
        document.getElementById('chatbot-badge').classList.add('hidden');

        if (isOpen) {
            input.focus();
            scrollToBottom();
        }
    });

    closeBtn.addEventListener('click', () => {
        isOpen = false;
        chatWindow.classList.add('hidden');
        chatWindow.classList.remove('flex');
    });

    // Clear chat
    clearBtn.addEventListener('click', () => {
        if (confirm(@json(app()->getLocale() === 'ar' ? 'هل تريد مسح المحادثة؟' : 'Clear conversation?'))) {
            fetch('/chat/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ attempt_id: attemptId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    messagesContainer.innerHTML = '';
                    location.reload();
                }
            });
        }
    });

    // Send message
    function sendMessage(message) {
        if (!message.trim()) return;

        // Add user message
        addMessage(message, 'user');
        input.value = '';
        sendBtn.disabled = true;

        // Show typing indicator
        showTyping();

        // Send to server
        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                message: message,
                context: context,
                attempt_id: attemptId,
                alternative_index: alternativeIndex
            })
        })
        .then(res => res.json())
        .then(data => {
            removeTyping();

            if (data.success) {
                addMessage(data.reply, 'assistant', data.timestamp);
            } else {
                addMessage(data.message || @json(app()->getLocale() === 'ar' ? 'حدث خطأ. حاول مرة أخرى.' : 'Error occurred. Try again.'), 'assistant');
            }
        })
        .catch(err => {
            removeTyping();
            addMessage(@json(app()->getLocale() === 'ar' ? 'خطأ في الاتصال. حاول مرة أخرى.' : 'Connection error. Try again.'), 'assistant');
        });
    }

    // Add message to chat
    function addMessage(text, role, timestamp = null) {
        const time = timestamp || new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
        const isUser = role === 'user';

        const messageHTML = `
            <div class="flex gap-2 message-enter ${isUser ? 'flex-row-reverse' : ''}">
                <div class="w-8 h-8 ${isUser ? 'bg-blue-600' : 'bg-blue-100 dark:bg-blue-900/30'} rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas ${isUser ? 'fa-user text-white' : 'fa-robot text-blue-600'} text-sm"></i>
                </div>
                <div class="${isUser ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm' : 'bg-gray-100 dark:bg-gray-700 rounded-2xl rounded-tl-sm'} p-3 max-w-[80%]">
                    <p class="text-sm ${isUser ? 'text-white' : 'text-gray-700 dark:text-gray-300'}">${text}</p>
                    <span class="text-xs ${isUser ? 'text-blue-100' : 'text-gray-400'} mt-1 block">${time}</span>
                </div>
            </div>
        `;

        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        scrollToBottom();
    }

    // Show typing indicator
    function showTyping() {
        const typingHTML = `
            <div id="typing-indicator" class="flex gap-2 message-enter">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-robot text-blue-600 text-sm"></i>
                </div>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl rounded-tl-sm p-3">
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        messagesContainer.insertAdjacentHTML('beforeend', typingHTML);
        scrollToBottom();
    }

    // Remove typing indicator
    function removeTyping() {
        const typing = document.getElementById('typing-indicator');
        if (typing) typing.remove();
    }

    // Scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Input handling
    input.addEventListener('input', () => {
        sendBtn.disabled = !input.value.trim();
    });

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && input.value.trim()) {
            sendMessage(input.value);
        }
    });

    sendBtn.addEventListener('click', () => {
        if (input.value.trim()) {
            sendMessage(input.value);
        }
    });

    // Suggestion buttons
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            sendMessage(btn.textContent.trim());
        });
    });

    // Load chat history
    if (attemptId) {
        fetch('/chat/history?attempt_id=' + attemptId)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.history.length > 0) {
                    // Clear welcome message
                    messagesContainer.innerHTML = '';

                    data.history.forEach(msg => {
                        addMessage(msg.content, msg.role, msg.timestamp);
                    });
                }
            });
    }
})();
</script>
