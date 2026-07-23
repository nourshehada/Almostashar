<?php

return [
    // Meta
    'title' => 'Quiz - AlMostashar',

    // Progress Section
    'progress' => [
        'title' => 'Interests Discovery Phase',
        'complete' => ':percent% Complete',
        'question_counter' => 'Question :current of :total',
        'exit' => 'Exit',
    ],

    // Welcome Message
    'welcome' => [
        'greeting' => '👋 Welcome! <strong>I am AlMostashar</strong>, and I will help you discover the most suitable path for you. 🎯',
        'duration' => 'It will only take a few minutes.',
        'start' => 'Let\'s begin:',
    ],

    // Input Area
    'input' => [
        'placeholder' => 'Choose from options or type your answer...',
        'privacy' => 'Your answers are encrypted and private • Powered by AI',
    ],

    // Exit Modal
    'exit_modal' => [
        'title' => 'Leave the Quiz?',
        'message' => 'Your current progress will be lost. Are you sure you want to leave?',
        'continue' => 'Continue Quiz',
        'leave' => 'Leave',
    ],

    // Validation Modal
    'validation_modal' => [
        'title' => 'Your Open Answers Are Unclear',
        'message' => 'It seems the free-text answers are not understandable. Would you like to continue without them or edit them?',
        'continue_without' => 'Continue Without Them',
        'edit' => 'Edit Answers',
    ],

    // Analyzing Overlay
    'analyzing' => [
        'phases' => [
            ['title' => 'Analyzing your profile...', 'subtitle' => 'AI is matching your data'],
            ['title' => 'Analyzing thinking patterns...', 'subtitle' => 'Understanding your decision-making'],
            ['title' => 'Discovering strengths...', 'subtitle' => 'Identifying your unique skills'],
            ['title' => 'Comparing with thousands of profiles...', 'subtitle' => 'Finding similar patterns'],
            ['title' => 'Matching with majors...', 'subtitle' => 'Finding the best suitable paths'],
            ['title' => 'Preparing personal plan...', 'subtitle' => 'Building a customized learning plan'],
            ['title' => 'Final review...', 'subtitle' => 'Ensuring result accuracy'],
            ['title' => 'Analysis Complete!', 'subtitle' => 'Redirecting to results...'],
        ],
        'completed_title' => 'Analysis Complete!',
        'completed_subtitle' => 'Redirecting to results...',
    ],

    // Bot Comments
    'comments' => [
        'after_q1' => [
            '🤔 Interesting start. Let\'s explore this aspect further.',
            '✨ Great, I\'m starting to understand your thinking style.',
            '🧩 Your answer reveals an important aspect of your personality.',
            '🔍 Let\'s move to another aspect of your thinking.',
        ],
        'after_q2' => [
            '📊 I\'m starting to form an initial picture of your choices.',
            '💡 I see interesting indicators beginning to repeat.',
            '🧠 Some traits are starting to emerge early on.',
            '🔎 Each answer adds a new piece to the picture.',
        ],
    ],

    // Feedbacks
    'feedbacks' => [
        'analysis' => [
            '🧠 I notice you tend to understand details before reaching a final conclusion. Let\'s see how that reflects on other aspects of your thinking.',
            '🔎 You seem to prefer analyzing data and searching for reasons before making decisions. Let\'s explore this pattern deeper.',
            '📊 Logical thinking pattern is clearly visible in your answers so far. I want to see how you handle different situations.',
            '🤔 Your choices indicate interest in understanding the full picture before judging matters. Let\'s move to another aspect of your personality.',
        ],
        'creativity' => [
            '✨ I see a tendency to search for new ideas and different ways to look at things. Let\'s discover how that appears in other situations.',
            '💡 You don\'t always settle for traditional solutions and prefer exploring alternatives. I want to see how that reflects on your upcoming choices.',
            '🎨 A creative side focusing on innovation and renewal appears in your answers. Let\'s explore this aspect more.',
            '🚀 Some of your choices reflect a desire to try unfamiliar ideas and methods. Let\'s continue building the full picture.',
        ],
        'leadership' => [
            '🎯 I notice a tendency to take responsibility and participate in guiding work. Let\'s see how that appears in other aspects of your personality.',
            '👥 Some of your answers reflect interest in organization and taking initiative when needed. Let\'s explore this pattern more.',
            '📈 You seem comfortable in roles that require coordinating efforts and achieving goals. I want to see how that repeats later.',
            '🏆 Good indicators appear on ability to manage situations and make decisions. Let\'s move to a slightly different aspect.',
        ],
        'communication' => [
            '🤝 Interaction with others and collaboration seems to be an important element for you. Let\'s see how that reflects on your remaining choices.',
            '💬 I notice interest in building understanding and exchanging ideas with those around you. Let\'s explore this aspect more.',
            '👂 Some of your choices reflect appreciation for listening and effective communication. I want to see how that appears in other situations.',
            '🌐 Your answers show a tendency to work in environments that rely on communication and collaboration. Let\'s continue the analysis.',
        ],
        'research' => [
            '📚 I notice intellectual curiosity and a desire to delve deep before forming a final opinion. Let\'s see how that appears in the remaining answers.',
            '🔬 You tend to explore and search for additional information to understand matters. Let\'s continue this pattern.',
            '📝 Some of your answers indicate interest in continuous learning and acquiring knowledge. I want to see how consistent that is.',
            '🧩 You have a tendency to research and investigate before reaching conclusions. Let\'s move to another aspect of your personality.',
        ],
        'business' => [
            '📈 I see interest in looking at practical results and future opportunities. Let\'s explore how that reflects on your upcoming choices.',
            '💼 Some of your choices reflect thinking focused on value and long-term impact. I want to see how often this pattern repeats.',
            '🎯 You seem to balance between ideas and their practical application. Let\'s move to a slightly different aspect.',
            '🚀 Your answers show indicators of interest in projects and making practical decisions. Let\'s continue building the full picture.',
        ],
        'technology' => [
            '💻 Understanding modern systems and technologies seems to clearly interest you. Let\'s see how that appears in the remaining answers.',
            '⚙️ I notice curiosity about how things work and the mechanisms behind them. I want to explore this aspect more.',
            '🔧 Some of your choices reflect a tendency to deal with technical and practical solutions. Let\'s move to another angle of analysis.',
            '🚀 Your answers show notable interest in technology and innovation-related fields. Let\'s continue discovering your interests.',
        ],
        'humanitarian' => [
            '❤️ I notice that the impact of decisions on people receives clear attention in your choices. Let\'s see how that appears in other situations.',
            '🤲 Humanitarian and social aspects seem present in your thinking. I want to explore this aspect more.',
            '🌱 Some of your answers reflect interest in supporting others and improving their lives. Let\'s continue building the full picture.',
            '😊 You have appreciation for aspects that contribute to serving the community and people. Let\'s move to another aspect of your personality.',
        ],
        'scientific' => [
            '🔬 I notice a tendency to rely on evidence and facts when evaluating matters. Let\'s see how that reflects on your upcoming choices.',
            '📖 You prefer accurate understanding of concepts and phenomena before making judgments. Let\'s explore this pattern more.',
            '🧪 Some of your choices reflect interest in scientific and methodical interpretation of events. I want to see how it continues.',
            '📊 Your answers show a thinking pattern based on observation and organized analysis. Let\'s continue the analysis.',
        ],
        'adaptability' => [
            '🌟 You seem capable of adapting to new conditions when needed. Let\'s see how that appears in other aspects of your personality.',
            '🔄 Some of your answers reflect flexibility in dealing with challenges and variables. I want to explore this aspect more.',
            '🛠️ I notice readiness to try different methods to reach results. Let\'s move to another angle of analysis.',
            '🚶 You have a tendency to deal with change in a practical and balanced way. Let\'s continue building the full picture.',
        ],
    ],

    // Validation Messages
    'validation' => [
        'too_short' => '🤔✍️ Your answer is too short. Add some details so I can get a clearer picture.',
        'not_understood' => '🤖 I couldn\'t understand the answer. Try writing a clear sentence.',
        'repeated_chars' => '😅 The answer seems to contain useless repetition.',
        'keyboard_mash' => '🤔 The text seems incomprehensible. Please write a real answer.',
        'error' => 'An error occurred while analyzing results. Please try again.',
        'unknown_error' => 'An unexpected error occurred.',
        'error_with_message' => 'An error occurred: :message',
    ],

    'errors' => [
        'general' => 'An unexpected error occurred. Please try again.',
        'invalid_answers' => 'Insufficient answers. Please complete all questions.',
    ],

    // Now label
    'now' => 'Now',
];
