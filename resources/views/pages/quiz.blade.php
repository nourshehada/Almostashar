<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('quiz.title') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['{{ app()->getLocale() === "ar" ? "Tajawal" : "Inter" }}', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563EB',
                        secondary: '#1E40AF',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: '{{ app()->getLocale() === "ar" ? "Tajawal" : "Inter" }}', sans-serif; }

        .chat-scroll::-webkit-scrollbar { width: 0; height: 0; display: none; }
        .chat-scroll { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message-enter { animation: fadeInUp 0.3s ease-out forwards; }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-4px); }
        }
        .typing-dot { animation: typing 1.4s infinite; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 1; }
            100% { transform: scale(1.2); opacity: 0; }
        }
        .analyzing-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid #2563EB;
            animation: pulse-ring 1.5s infinite;
        }

        .chip { transition: all 0.2s ease; }
        .chip:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }
        .chip:active { transform: translateY(0); }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes modalSlideIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-backdrop { animation: modalFadeIn 0.2s ease-out forwards; }
        .modal-content { animation: modalSlideIn 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-gradient-to-b from-blue-50/50 to-white min-h-screen">

    {{-- ================= PROGRESS SECTION ================= --}}
    <div class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ __('quiz.progress.title') }}</h2>
                    <p class="text-sm text-blue-600 font-medium" id="progress-text">
                        {{ __('quiz.progress.complete', ['percent' => 0]) }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400" id="question-counter"></span>

                    <button onclick="openExitModal()"
                       class="flex items-center gap-2 text-gray-500 hover:text-red-500 transition-all duration-200">
                        <span class="text-sm font-medium">{{ __('quiz.progress.exit') }}</span>
                        <i class="fas fa-sign-out-alt text-sm {{ app()->getLocale() === 'en' ? '' : 'rotate-180' }}"></i>
                    </button>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div id="progress-bar"
                     class="bg-gradient-to-{{ app()->getLocale() === 'ar' ? 'l' : 'r' }} from-blue-500 to-blue-600 h-full rounded-full transition-all duration-700 ease-out"
                     style="width: 0%"></div>
            </div>
        </div>
    </div>

    {{-- ================= CHAT AREA ================= --}}
    <div class="max-w-4xl mx-auto px-4 pt-6 pb-32">
        <div id="chat-container" class="chat-scroll overflow-y-auto max-h-[calc(100vh-320px)] space-y-4">
            <div class="message-enter flex gap-3 items-end">
                <div class="w-10 h-10 bg-blue-300 rounded-full flex justify-center flex-shrink-0 shadow-lg">
                    <img src="{{ asset('images/advisor.png') }}"
                         alt="{{ __('quiz.welcome.greeting') }}"
                         class="w-12 h-12 rounded-full object-cover">
                </div>
                <div class="bg-white rounded-2xl {{ app()->getLocale() === 'ar' ? 'rounded-bl-sm' : 'rounded-br-sm' }} p-5 shadow-md border border-gray-100 max-w-[85%]">
                    <p class="text-gray-700 leading-relaxed">
                        {!! __('quiz.welcome.greeting') !!}
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-3">
                        {{ __('quiz.welcome.duration') }}
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-3 font-bold">
                        {{ __('quiz.welcome.start') }}
                    </p>
                </div>
                <span class="text-xs text-gray-400 mb-1">{{ __('quiz.now') }}</span>
            </div>
        </div>
    </div>

    {{-- ================= SUGGESTED ANSWERS ================= --}}
    <div id="suggestions-container" class="fixed bottom-28 left-0 right-0 z-40 hidden pointer-events-none">
        <div class="max-w-5xl mx-auto px-4">
            <div id="suggestions-list" class="flex flex-wrap gap-2 justify-center pointer-events-auto"></div>
        </div>
    </div>

    {{-- ================= INPUT AREA ================= --}}
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="flex-1 relative">
                    <input
                        type="text"
                        id="user-input"
                        placeholder="{{ __('quiz.input.placeholder') }}"
                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-200 rounded-full {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition text-gray-700 placeholder-gray-400"
                        onkeypress="handleKeyPress(event)"
                    >
                    <button
                        onclick="sendAnswer()"
                        id="send-btn"
                        class="absolute {{ app()->getLocale() === 'ar' ? 'left-2' : 'right-2' }} top-1/2 -translate-y-1/2 w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="fas fa-arrow-up text-l"></i>
                    </button>
                </div>
            </div>
            <div class="text-center mt-2">
                <span class="text-xs text-gray-400 flex items-center justify-center gap-1">
                    <i class="fas fa-lock text-[10px]"></i>
                    {{ __('quiz.input.privacy') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ================= EXIT MODAL ================= --}}
    <div id="exit-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm modal-backdrop" onclick="closeExitModal()"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 modal-content">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-2xl"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white text-center mb-2">
                {{ __('quiz.exit_modal.title') }}
            </h3>
            <p class="text-gray-600 dark:text-gray-400 text-center mb-6 text-sm leading-relaxed">
                {{ __('quiz.exit_modal.message') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="closeExitModal()"
                    class="flex-1 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition shadow-lg shadow-blue-600/30">
                    <i class="fas fa-undo {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('quiz.exit_modal.continue') }}
                </button>
                <a href="{{ route('home') }}"
                    class="flex-1 px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition text-center">
                    <i class="fas fa-sign-out-alt {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('quiz.exit_modal.leave') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ================= VALIDATION MODAL ================= --}}
    <div id="validation-modal" class="fixed inset-0 z-[70] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm modal-backdrop"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 modal-content">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">
                {{ __('quiz.validation_modal.title') }}
            </h3>
            <p class="text-gray-600 text-center mb-6 text-sm leading-relaxed">
                {{ __('quiz.validation_modal.message') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="continueWithoutOpenAnswers()"
                    class="flex-1 px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
                    <i class="fas fa-forward {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('quiz.validation_modal.continue_without') }}
                </button>
                <button onclick="editOpenAnswers()"
                    class="flex-1 px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition">
                    <i class="fas fa-edit {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('quiz.validation_modal.edit') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ================= ANALYZING OVERLAY ================= --}}
    <div id="analyzing-overlay" class="fixed inset-0 bg-white z-[60] hidden flex-col items-center justify-center">
        <div class="relative mb-12">
            <div class="w-40 h-40 border-4 border-blue-100 rounded-full relative">
                <div class="absolute inset-0 border-4 border-transparent border-t-blue-500 border-r-blue-400 rounded-full animate-spin" style="animation-duration: 3s;"></div>
                <div class="absolute inset-2 border-4 border-transparent border-b-cyan-400 border-l-cyan-300 rounded-full animate-spin" style="animation-duration: 2s; animation-direction: reverse;"></div>
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                    <i class="fas fa-brain text-blue-600 text-3xl"></i>
                </div>
            </div>
        </div>

        <h3 id="analyzing-title" class="text-3xl font-bold text-gray-900 mb-3"></h3>
        <p id="analyzing-subtitle" class="text-lg text-gray-500 mb-10"></p>

        <div class="w-96 max-w-md bg-gray-200 rounded-full h-2 overflow-hidden">
            <div id="analyzing-progress" class="h-full bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full transition-all duration-1000" style="width: 0%"></div>
        </div>
    </div>

    {{-- ================= JAVASCRIPT ================= --}}
    <script>
        const questions = {!! $questionsJson !!};

        @php
            // ✅ بناء مصفوفة الترجمات في PHP أولاً
            $translationsArray = [
                'now' => __('quiz.now'),
                'analyzing' => __('quiz.analyzing.phases'),
                'completed_title' => __('quiz.analyzing.completed_title'),
                'completed_subtitle' => __('quiz.analyzing.completed_subtitle'),
                'comments' => __('quiz.comments'),
                'feedbacks' => __('quiz.feedbacks'),
                'validation' => __('quiz.validation'),
                'progress' => [
                    'complete' => __('quiz.progress.complete'),
                    'question_counter' => __('quiz.progress.question_counter'),
                ],
                'advisor_image' => asset('images/advisor.png'),
                'is_rtl' => app()->getLocale() === 'ar',
            ];
        @endphp

        // ✅ تمرير الترجمات من Laravel إلى JavaScript
        const translations = @json($translationsArray);

        // ✅ بناء feedbacks من الترجمات
        const feedbacks = translations.feedbacks;

        let currentQuestionIndex = 0;
        const FIRST_OPEN_QUESTION_INDEX = 24;
        let isSubmitting = false;
        let userAnswers = [];
        let isWaitingForAnswer = false;
        let ignoreOpenAnswers = false;

        let profile = {
            analysis: 0, creativity: 0, leadership: 0, communication: 0,
            research: 0, business: 0, technology: 0, humanitarian: 0,
            scientific: 0, adaptability: 0
        };

        document.addEventListener('DOMContentLoaded', () => {
            updateProgress();
            setTimeout(() => {
                showBotQuestion(questions[0].question);
                setTimeout(() => {
                    showSuggestions(questions[0].suggestions);
                    isWaitingForAnswer = true;
                }, 800);
            }, 500);
        });

        // ========================
        // ✅ MODAL FUNCTIONS
        // ========================
        function openExitModal() {
            const modal = document.getElementById('exit-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeExitModal() {
            const modal = document.getElementById('exit-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function showValidationModal() {
            const modal = document.getElementById('validation-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideValidationModal() {
            const modal = document.getElementById('validation-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function continueWithoutOpenAnswers() {
            hideValidationModal();
            ignoreOpenAnswers = true;
            userAnswers = userAnswers.filter(
                answer => answer.question_id !== 25 && answer.question_id !== 26
            );
            showAnalyzingOverlay();
        }

        function editOpenAnswers() {
            hideValidationModal();
            isSubmitting = false;
            currentQuestionIndex = FIRST_OPEN_QUESTION_INDEX;
            hideAnalyzingOverlay();
            displayCurrentQuestion();
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeExitModal();
                hideValidationModal();
            }
        });

        function showBotQuestion(text) {
            const chatContainer = document.getElementById('chat-container');
            const typingId = 'typing-' + Date.now();
            const roundedCorner = translations.is_rtl ? 'rounded-bl-sm' : 'rounded-br-sm';

            chatContainer.insertAdjacentHTML('beforeend', `
                <div id="${typingId}" class="message-enter flex gap-3 items-end">
                    <div class="w-10 h-10 bg-blue-300 rounded-full flex justify-center flex-shrink-0 shadow-lg">
                        <img src="${translations.advisor_image}"
                             alt="AlMostashar"
                             class="w-12 h-12 rounded-full object-cover">
                    </div>
                    <div class="bg-white rounded-2xl ${roundedCorner} p-4 shadow-md border border-gray-100">
                        <div class="flex gap-1">
                            <div class="w-2 h-2 bg-gray-300 rounded-full typing-dot"></div>
                            <div class="w-2 h-2 bg-gray-300 rounded-full typing-dot"></div>
                            <div class="w-2 h-2 bg-gray-300 rounded-full typing-dot"></div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 mb-1">${translations.now}</span>
                </div>
            `);
            scrollToBottom();

            setTimeout(() => {
                const typingEl = document.getElementById(typingId);
                if (typingEl) {
                    typingEl.outerHTML = `
                        <div class="message-enter flex gap-3 items-end">
                            <div class="w-10 h-10 bg-blue-300 rounded-full flex justify-center flex-shrink-0 shadow-lg">
                                <img src="${translations.advisor_image}"
                                     alt="AlMostashar"
                                     class="w-12 h-12 rounded-full object-cover">
                            </div>
                            <div class="bg-white rounded-2xl ${roundedCorner} p-5 shadow-md border border-gray-100 max-w-[85%]">
                                <div class="text-gray-700 leading-relaxed">${text}</div>
                            </div>
                            <span class="text-xs text-gray-400 mb-1">${translations.now}</span>
                        </div>
                    `;
                }
                scrollToBottom();
            }, 1200);
        }

        function showBotCommentAndQuestion(comment, question) {
            const fullMessage = `${comment}<br><br><strong>${question}</strong>`;
            showBotQuestion(fullMessage);
        }

        function showUserMessage(text) {
            const chatContainer = document.getElementById('chat-container');
            const roundedCorner = translations.is_rtl ? 'rounded-br-sm' : 'rounded-bl-sm';

            chatContainer.insertAdjacentHTML('beforeend', `
                <div class="message-enter flex gap-3 items-end ${translations.is_rtl ? 'flex-row-reverse' : 'flex-row-reverse'}">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="bg-blue-600 rounded-2xl ${roundedCorner} p-5 shadow-md max-w-[85%]">
                        <p class="text-white leading-relaxed">${text}</p>
                    </div>
                    <span class="text-xs text-gray-400 mb-1">${translations.now}</span>
                </div>
            `);
            scrollToBottom();
        }

        function showSuggestions(suggestions) {
            const container = document.getElementById('suggestions-container');
            const list = document.getElementById('suggestions-list');

            list.innerHTML = suggestions.map((option, index) => `
                <button
                    onclick='selectSuggestion(${JSON.stringify(option).replace(/'/g, "&#39;")})'
                    class="chip px-4 py-2.5 bg-white border-2 border-gray-200 rounded-xl text-gray-700 text-sm font-medium hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition flex items-center gap-2">
                    ${option.text}
                </button>
            `).join('');

            container.classList.remove('hidden');
        }

        function hideSuggestions() {
            document.getElementById('suggestions-container').classList.add('hidden');
        }

        function selectSuggestion(option) {
            if (!isWaitingForAnswer) return;
            hideSuggestions();
            processAnswer(option.text, option.id, option);
        }

        function sendAnswer() {
            const input = document.getElementById('user-input');
            const text = input.value.trim();

            if (!text || !isWaitingForAnswer) return;

            const currentQuestion = questions[currentQuestionIndex];
            const isFreeQuestion = currentQuestion.type === 'text';

            if (isFreeQuestion) {
                const words = text.trim().split(/\s+/).filter(word => word.length > 1);

                if (words.length < 3) {
                    showBotValidationMessage(translations.validation.too_short);
                    return;
                }

                const validChars = /[a-zA-Z\u0600-\u06FF]/;
                if (!validChars.test(text)) {
                    showBotValidationMessage(translations.validation.not_understood);
                    return;
                }

                if (/^([a-zA-Z\u0600-\u06FF])\1+$/.test(text.replace(/\s/g, ''))) {
                    showBotValidationMessage(translations.validation.repeated_chars);
                    return;
                }

                const englishWords = text.match(/[a-zA-Z]+/g) || [];
                const keyboardMash = englishWords.length > 0 && englishWords.every(word => !/[aeiou]/i.test(word));

                if (keyboardMash) {
                    showBotValidationMessage(translations.validation.keyboard_mash);
                    return;
                }
            }

            hideSuggestions();
            input.value = '';
            processAnswer(text, null);
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') sendAnswer();
        }

        function processAnswer(text, optionId = null, optionData = null) {
            isWaitingForAnswer = false;

            userAnswers.push({
                question_id: questions[currentQuestionIndex].id,
                question: questions[currentQuestionIndex].question,
                option_id: optionId,
                answer: text
            });

            if (optionData) {
                profile.analysis += optionData.analysis;
                profile.creativity += optionData.creativity;
                profile.leadership += optionData.leadership;
                profile.communication += optionData.communication;
                profile.research += optionData.research;
                profile.business += optionData.business;
                profile.technology += optionData.technology;
                profile.humanitarian += optionData.humanitarian;
                profile.scientific += optionData.scientific;
                profile.adaptability += optionData.adaptability;
            }

            showUserMessage(text);
            currentQuestionIndex++;
            updateProgress();

            let commentToShow = null;

            if (currentQuestionIndex === 1 && translations.comments.after_q1) {
                const comments = translations.comments.after_q1;
                commentToShow = comments[Math.floor(Math.random() * comments.length)];
            }

            if (currentQuestionIndex === 2 && translations.comments.after_q2) {
                const comments = translations.comments.after_q2;
                commentToShow = comments[Math.floor(Math.random() * comments.length)];
            }

            if (currentQuestionIndex % 4 === 0 && currentQuestionIndex < questions.length) {
                const topDimension = getTopDimension();
                if (feedbacks[topDimension]) {
                    const comments = feedbacks[topDimension];
                    commentToShow = comments[Math.floor(Math.random() * comments.length)];
                }
            }

            if (currentQuestionIndex < questions.length) {
                setTimeout(() => displayCurrentQuestion(commentToShow), 600);
            } else {
                setTimeout(() => showAnalyzingOverlay(), 600);
            }
        }

        function displayCurrentQuestion(comment = null) {
            if (comment) {
                showBotCommentAndQuestion(comment, questions[currentQuestionIndex].question);
            } else {
                showBotQuestion(questions[currentQuestionIndex].question);
            }

            setTimeout(() => {
                showSuggestions(questions[currentQuestionIndex].suggestions);
                isWaitingForAnswer = true;
            }, 1200);
        }

        function showBotValidationMessage(text) {
            const chatContainer = document.getElementById('chat-container');
            const roundedCorner = translations.is_rtl ? 'rounded-bl-sm' : 'rounded-br-sm';

            chatContainer.insertAdjacentHTML('beforeend', `
                <div class="message-enter flex gap-3 items-end">
                    <div class="w-10 h-10 bg-blue-300 rounded-full flex justify-center flex-shrink-0 shadow-lg">
                        <img src="${translations.advisor_image}"
                             alt="AlMostashar"
                             class="w-12 h-12 rounded-full object-cover">
                    </div>
                    <div class="bg-amber-50 rounded-2xl ${roundedCorner} p-5 shadow-md border border-amber-200 max-w-[85%]">
                        <p class="text-amber-800 leading-relaxed">${text}</p>
                    </div>
                    <span class="text-xs text-gray-400 mb-1">${translations.now}</span>
                </div>
            `);
            scrollToBottom();
        }

        function getTopDimension() {
            return Object.keys(profile).reduce((a, b) => profile[a] > profile[b] ? a : b);
        }

        function updateProgress() {
            const total = questions.length;
            const current = currentQuestionIndex;
            const percentage = Math.round((current / total) * 100);

            document.getElementById('progress-bar').style.width = percentage + '%';
            document.getElementById('progress-text').textContent =
                translations.progress.complete.replace(':percent', percentage);
            document.getElementById('question-counter').textContent =
                translations.progress.question_counter
                    .replace(':current', Math.min(current + 1, total))
                    .replace(':total', total);
        }

        function scrollToBottom() {
            const container = document.getElementById('chat-container');
            container.scrollTop = container.scrollHeight;
        }

        function hideAnalyzingOverlay() {
            const overlay = document.getElementById('analyzing-overlay');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            document.getElementById('user-input').disabled = false;
            document.getElementById('send-btn').disabled = false;
        }

        function showAnalyzingOverlay() {
            if (isSubmitting) return;

            isSubmitting = true;
            hideSuggestions();

            document.getElementById('user-input').disabled = true;
            document.getElementById('send-btn').disabled = true;

            const overlay = document.getElementById('analyzing-overlay');
            if (!overlay) return;

            // ✅ إعادة تعيين الـ overlay إلى الحالة الأولية
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            const analyzingPhases = translations.analyzing;
            let currentPhase = 0;

            const progressBar = document.getElementById('analyzing-progress');
            const titleText = document.getElementById('analyzing-title');
            const subtitleText = document.getElementById('analyzing-subtitle');

            // ✅ إصلاح المشكلة #1: عرض المرحلة الأولى فوراً
            if (analyzingPhases.length > 0) {
                const firstPhase = analyzingPhases[0];
                if (progressBar) progressBar.style.width = '0%';
                if (titleText) titleText.textContent = firstPhase.title;
                if (subtitleText) subtitleText.textContent = firstPhase.subtitle;
            }

            // بدء الـ interval بعد عرض المرحلة الأولى
            const progressInterval = setInterval(() => {
                currentPhase++; // نبدأ من 1 لأن 0 عُرضت فوراً

                if (currentPhase < analyzingPhases.length) {
                    const phase = analyzingPhases[currentPhase];
                    const progress = Math.round(((currentPhase + 1) / analyzingPhases.length) * 100);

                    if (progressBar) progressBar.style.width = progress + '%';
                    if (titleText) titleText.textContent = phase.title;
                    if (subtitleText) subtitleText.textContent = phase.subtitle;
                }
            }, 2500);

            // إرسال الطلب
            fetch('/quiz/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    answers: userAnswers,
                    profile: profile,
                    ignore_open_answers: ignoreOpenAnswers
                })
            })
            .then(async response => {
                clearInterval(progressInterval);

                if (progressBar) progressBar.style.width = '100%';
                if (titleText) titleText.textContent = translations.completed_title;
                if (subtitleText) subtitleText.textContent = translations.completed_subtitle;

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response');
                }
            })
            .then(data => {
                //إعادة تعيين الحالة عند validation_failed
                if (data.success === false && data.type === 'validation_failed') {
                    setTimeout(() => {
                        // إعادة تعيين الـ overlay للحالة الأولية
                        if (progressBar) progressBar.style.width = '0%';
                        if (titleText) titleText.textContent = analyzingPhases[0]?.title || '';
                        if (subtitleText) subtitleText.textContent = analyzingPhases[0]?.subtitle || '';

                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                        isSubmitting = false;

                        // إعادة تفعيل الإدخال
                        document.getElementById('user-input').disabled = false;
                        document.getElementById('send-btn').disabled = false;

                        showValidationModal();
                    }, 1000);
                    return;
                }

                if (data.success === false) {
                    setTimeout(() => {
                        // إعادة تعيين الـ overlay
                        if (progressBar) progressBar.style.width = '0%';
                        if (titleText) titleText.textContent = analyzingPhases[0]?.title || '';
                        if (subtitleText) subtitleText.textContent = analyzingPhases[0]?.subtitle || '';

                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                        isSubmitting = false;

                        document.getElementById('user-input').disabled = false;
                        document.getElementById('send-btn').disabled = false;

                        alert(translations.validation.error_with_message.replace(':message', data.message || ''));
                    }, 1000);
                    return;
                }

                if (data.success && data.attempt_id) {
                    setTimeout(() => {
                        window.location.href = `/results/${data.attempt_id}`;
                    }, 1500);
                } else {
                    setTimeout(() => {
                        overlay.classList.add('hidden');
                        overlay.classList.remove('flex');
                        isSubmitting = false;
                        alert(translations.validation.unknown_error);
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
                clearInterval(progressInterval);
                isSubmitting = false;

                setTimeout(() => {
                    // إعادة تعيين الـ overlay
                    if (progressBar) progressBar.style.width = '0%';
                    if (titleText) titleText.textContent = analyzingPhases[0]?.title || '';
                    if (subtitleText) subtitleText.textContent = analyzingPhases[0]?.subtitle || '';

                    overlay.classList.add('hidden');
                    overlay.classList.remove('flex');

                    document.getElementById('user-input').disabled = false;
                    document.getElementById('send-btn').disabled = false;

                    showBotValidationMessage(translations.validation.error);
                }, 1000);
            });

            if (data.success && data.attempt_id) {
                // منع الرجوع لصفحة الاختبار
                PreventBack.block();

                setTimeout(() => {
                    window.location.href = `/results/${data.attempt_id}`;
                }, 1500);
            }
        }
    </script>
</body>
</html>
