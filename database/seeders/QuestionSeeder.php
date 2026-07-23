<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        Question::create([
            'question_ar' => 'عندما تتعلم مهارة جديدة، تفضل أن:',
            'question_en' => 'When learning a new skill, you prefer to:',
            'type' => 'multiple_choice',
            'branch' => 'thinking',
            'order' => 1,
        ]);

        Question::create([
            'question_ar' => 'إذا اختلفت المعلومات من مصدرين:',
            'question_en' => 'If information from two sources conflicts:',
            'type' => 'multiple_choice',
            'branch' => 'thinking',
            'order' => 2,
        ]);

        Question::create([
            'question_ar' => 'عندما تواجه مشكلة معقدة لأول مرة:',
            'question_en' => 'When facing a complex problem for the first time:',
            'type' => 'multiple_choice',
            'branch' => 'thinking',
            'order' => 3,
        ]);

        Question::create([
            'question_ar' => 'طُلب منكم تنفيذ مشروع جماعي مهم، ما الدور الذي تنجذب إليه غالبًا؟',
            'question_en' => 'In an important team project, which role would you naturally take?',
            'type' => 'multiple_choice',
            'branch' => 'thinking',
            'order' => 4,
        ]);

        Question::create([
            'question_ar' => 'ما الذي يجعلك فخوراً بنفسك أكثر؟',
            'question_en' => 'What makes you most proud of yourself?',
            'type' => 'multiple_choice',
            'branch' => 'thinking',
            'order' => 5,
        ]);

        Question::create([
            'question_ar' => 'أي خبر يجعلك تفتح الرابط فوراً؟',
            'question_en' => 'Which type of news makes you click immediately?',
            'type' => 'multiple_choice',
            'branch' => 'interests',
            'order' => 6,
        ]);

        Question::create([
            'question_ar' => 'عندما ترى منتجاً ناجحاً في السوق، ما أول شيء يخطر ببالك؟',
            'question_en' => 'When you see a successful product, what is the first thing that comes to your mind?',
            'type' => 'multiple_choice',
            'branch' => 'interests',
            'order' => 7,
        ]);

        Question::create([
            'question_ar' => 'لديك يوم كامل بدون أي التزامات، ماذا ستفعل غالباً؟',
            'question_en' => 'You have a completely free day. What would you most likely do?',
            'type' => 'multiple_choice',
            'branch' => 'interests',
            'order' => 8,
        ]);

        Question::create([
            'question_ar' => 'أي نوع من المشاكل يجذب اهتمامك أكثر؟',
            'question_en' => 'Which type of problems interests you the most?',
            'type' => 'multiple_choice',
            'branch' => 'interests',
            'order' => 9,
        ]);

        Question::create([
            'question_ar' => 'عندما تسمع عن تقنية جديدة مثل الذكاء الاصطناعي أو الروبوتات الطبية، ما أول رد فعل لديك؟',
            'question_en' => 'When you hear about a new technology such as AI or medical robots, what is your first reaction?',
            'type' => 'multiple_choice',
            'branch' => 'interests',
            'order' => 10,
        ]);

        Question::create([
            'question_ar' => 'أي بيئة عمل تتخيل نفسك مرتاحاً فيها؟',
            'question_en' => 'Which work environment do you imagine yourself being most comfortable in?',
            'type' => 'multiple_choice',
            'branch' => 'work_environment',
            'order' => 11,
        ]);

        Question::create([
            'question_ar' => 'عندما تبدأ مشروعًا جديدًا، كيف تفضل إنجاز العمل؟',
            'question_en' => 'When starting a new project, how do you prefer to work?',
            'type' => 'multiple_choice',
            'branch' => 'work_environment',
            'order' => 12,
        ]);

        Question::create([
            'question_ar' => 'إذا كان لديك يوم عمل مثالي، فسيكون مليئاً بـ:',
            'question_en' => 'If you had a perfect workday, it would be filled with:',
            'type' => 'multiple_choice',
            'branch' => 'work_environment',
            'order' => 13,
        ]);

        Question::create([
            'question_ar' => 'لو حصلت على عرضين وظيفيين، ماذا ستختار؟',
            'question_en' => 'If you received two job offers, which would you choose?',
            'type' => 'multiple_choice',
            'branch' => 'career_values',
            'order' => 14,
        ]);

        Question::create([
            'question_ar' => 'بعد 10 سنوات، تتمنى أن تصبح:',
            'question_en' => 'In 10 years, you hope to become:',
            'type' => 'multiple_choice',
            'branch' => 'career_values',
            'order' => 15,
        ]);

        Question::create([
            'question_ar' => 'ما العامل الأهم بالنسبة لك في حياتك المهنية؟',
            'question_en' => 'What is the most important factor in your professional life?',
            'type' => 'multiple_choice',
            'branch' => 'career_values',
            'order' => 16,
        ]);

        Question::create([
            'question_ar' => 'إذا ضُمن لك النجاح الكامل، فأي طريق تختار؟',
            'question_en' => 'If success was guaranteed, which path would you choose?',
            'type' => 'multiple_choice',
            'branch' => 'career_values',
            'order' => 17,
        ]);

        Question::create([
            'question_ar' => 'في المناسبات الاجتماعية غالباً:',
            'question_en' => 'In social events, you usually:',
            'type' => 'multiple_choice',
            'branch' => 'communication',
            'order' => 18,
        ]);

        Question::create([
            'question_ar' => 'عند حدوث خلاف مع زميل:',
            'question_en' => 'When a disagreement occurs with a colleague:',
            'type' => 'multiple_choice',
            'branch' => 'communication',
            'order' => 19,
        ]);

        Question::create([
            'question_ar' => 'أشعر بالحماس عند شرح أفكاري أمام مجموعة من الناس',
            'question_en' => 'I feel excited when explaining my ideas to a group of people',
            'type' => 'multiple_choice',
            'branch' => 'communication',
            'order' => 20,
        ]);

        Question::create([
            'question_ar' => 'عندما يعمل فريق على مشروع مهم، أي دور تجد نفسك تقوم به تلقائيًا؟',
            'question_en' => 'When working on an important team project, which role do you naturally take?',
            'type' => 'multiple_choice',
            'branch' => 'communication',
            'order' => 21,
        ]);

        Question::create([
            'question_ar' => 'إذا فشلت في مهمة مهمة:',
            'question_en' => 'If you fail at an important task:',
            'type' => 'multiple_choice',
            'branch' => 'adaptability',
            'order' => 22,
        ]);

        Question::create([
            'question_ar' => 'إذا واجهت مشكلة لا يوجد لها حل معروف، ماذا يثير اهتمامك أكثر؟',
            'question_en' => 'If you face a problem with no known solution, what interests you most?',
            'type' => 'multiple_choice',
            'branch' => 'adaptability',
            'order' => 23,
        ]);

        Question::create([
            'question_ar' => 'في النقاشات المهمة، بماذا تعتمد غالباً لإقناع الآخرين؟',
            'question_en' => 'In important discussions, what do you usually rely on to convince others?',
            'type' => 'multiple_choice',
            'branch' => 'critical_thinking',
            'order' => 24,
        ]);

        Question::create([
            'question_ar' => 'لو ضُمنت لك نسبة نجاح 100% في أي مجال، ماذا ستختار؟ ولماذا؟',
            'question_en' => 'If success was guaranteed in any field, what would you choose and why?',
            'type' => 'text',
            'branch' => 'open',
            'order' => 25,
        ]);

        Question::create([
            'question_ar' => 'اذكر ثلاث نقاط قوة تعتقد أنها تميزك عن الآخرين.',
            'question_en' => 'Mention three strengths that distinguish you from others.',
            'type' => 'text',
            'branch' => 'open',
            'order' => 26,
        ]);

    }
}
