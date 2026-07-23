<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;

class OptionSeeder extends Seeder
{
    private function createOption(
        $questionId,
        $optionAr,
        $optionEn,
        $scores = []
    ) {

        Option::create(array_merge([

            'question_id' => $questionId,

            'option_ar' => $optionAr,
            'option_en' => $optionEn,

            'analysis' => 0,
            'creativity' => 0,
            'leadership' => 0,
            'communication' => 0,
            'research' => 0,
            'business' => 0,
            'technology' => 0,
            'humanitarian' => 0,
            'scientific' => 0,
            'adaptability' => 0,

        ], $scores));
    }

    public function run(): void
    {
        $this->createOption(1,
            'أفهم النظرية والأساس أولاً',
            'I understand the theory first',
            [
                'analysis' => 3,
                'research' => 2,
                'scientific' => 1,
            ]
        );

        $this->createOption(1,
            'أطبق مباشرة وأتعلم من الخطأ',
            'I learn by doing and making mistakes',
            [
                'adaptability' => 3,
            ]
        );

        $this->createOption(1,
            'أشاهد أمثلة عملية',
            'I learn through examples',
            [
                'analysis' => 1,
                'adaptability' => 2,
            ]
        );

        $this->createOption(1,
            'أشرح الفكرة لشخص آخر لأتأكد من فهمي',
            'I explain it to someone else',
            [
                'communication' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(2,
            'أبحث عن الأدلة والبراهين',
            'Search for evidence',
            [
                'analysis' => 3,
                'research' => 2,
                'scientific' => 1,
            ]
        );

        $this->createOption(2,
            'أختار المصدر الأكثر موثوقية',
            'Choose the most reliable source',
            [
                'research' => 2,
                'analysis' => 1,
            ]
        );

        $this->createOption(2,
            'أحلل المصدرين بنفسي',
            'Analyze both sources myself',
            [
                'analysis' => 3,
            ]
        );

        $this->createOption(2,
            'أتبع ما يبدو منطقياً',
            'Follow what seems logical',
            [
                'analysis' => 1,
                'adaptability' => 1,
            ]
        );

        $this->createOption(3,
            'أقسمها إلى أجزاء صغيرة',
            'Break it into smaller parts',
            [
                'analysis' => 3,
            ]
        );

        $this->createOption(3,
            'أبحث عن أمثلة مشابهة',
            'Look for similar examples',
            [
                'research' => 2,
                'analysis' => 1,
            ]
        );

        $this->createOption(3,
            'أجرب عدة حلول',
            'Try several solutions',
            [
                'creativity' => 2,
                'adaptability' => 2,
            ]
        );

        $this->createOption(3,
            'أطلب رأي شخص خبير',
            'Ask an expert',
            [
                'communication' => 2,
                'research' => 1,
            ]
        );

        $this->createOption(4,
            'تحليل المشكلة ووضع خطة واضحة',
            'Analyze the problem and create a clear plan.',
            [
                'analysis' => 3,
                'scientific' => 2,
            ]
        );

        $this->createOption(4,
            'ابتكار أفكار وحلول جديدة',
            'Generate innovative ideas and solutions.',
            [
                'creativity' => 3,
                'technology' => 2,
            ]
        );

        $this->createOption(4,
            'قيادة الفريق وتنسيق العمل',
            'Organize people and tasks',
            [
                'leadership' => 3,
                'communication' => 2,
            ]
        );

        $this->createOption(4,
            'دعم الفريق والتأكد من تعاون الجميع',
            'Support the team and ensure everyone collaborates.',
            [
                'humanitarian' => 3,
                'communication' => 2,
            ]
        );

        $this->createOption(5,
            'إنجاز صعب حققته',
            'A difficult achievement',
            [
                'adaptability' => 2,
                'analysis' => 1,
            ]
        );

        $this->createOption(5,
            'مساعدة شخص في محنة',
            'Helping someone in need',
            [
                'humanitarian' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(5,
            'فكرة مبتكرة نفذتها',
            'An innovative idea',
            [
                'creativity' => 3,
            ]
        );

        $this->createOption(5,
            'معرفة جديدة اكتسبتها',
            'New knowledge learned',
            [
                'research' => 3,
                'scientific' => 1,
            ]
        );

        $this->createOption(6,
            'تقنية جديدة قد تغير العالم',
            'A new technology that could change the world',
            [
                'technology' => 3,
                'scientific' => 1,
            ]
        );

        $this->createOption(6,
            'اكتشاف علمي أو طبي مهم',
            'An important scientific or medical discovery',
            [
                'scientific' => 3,
                'research' => 1,
            ]
        );

        $this->createOption(6,
            'قصة نجاح مشروع أو شركة',
            'A successful company story',
            [
                'business' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(6,
            'عمل إبداعي أو تصميم مميز',
            'A creative work or design',
            [
                'creativity' => 3,
            ]
        );

        $this->createOption(7,
            'كيف تم تطويره تقنياً؟',
            'How was it technically developed?',
            [
                'technology' => 3,
                'analysis' => 1,
            ]
        );

        $this->createOption(7,
            'كيف نجحت الشركة في تسويقه؟',
            'How was it marketed successfully?',
            [
                'business' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(7,
            'كيف تم تصميمه بهذه الجاذبية؟',
            'How was it designed attractively?',
            [
                'creativity' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(7,
            'ما المبادئ العلمية المستخدمة فيه؟',
            'What scientific principles are used in it?',
            [
                'scientific' => 3,
                'research' => 1,
            ]
        );

        $this->createOption(8,
            'بناء أو إصلاح شيء معقد',
            'Build or fix something complex',
            [
                'technology' => 2,
                'analysis' => 2,
            ]
        );

        $this->createOption(8,
            'قراءة أو تعلم موضوع جديد',
            'Read or learn something new',
            [
                'research' => 3,
                'scientific' => 1,
            ]
        );

        $this->createOption(8,
            'تنظيم مشروع أو نشاط',
            'Organize a project or activity',
            [
                'leadership' => 3,
                'business' => 1,
            ]
        );

        $this->createOption(8,
            'إنتاج عمل إبداعي',
            'Create something creative',
            [
                'creativity' => 3,
            ]
        );

        $this->createOption(9,
            'المشاكل التقنية',
            'Technical problems',
            [
                'technology' => 3,
                'analysis' => 1,
            ]
        );

        $this->createOption(9,
            'المشاكل العلمية أو الصحية',
            'Scientific or health problems',
            [
                'scientific' => 2,
                'humanitarian' => 2,
            ]
        );

        $this->createOption(9,
            'المشاكل الإدارية أو التجارية',
            'Business and management problems',
            [
                'business' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(9,
            'المشاكل الاجتماعية والإنسانية',
            'Social and humanitarian problems',
            [
                'humanitarian' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(10,
            'أريد معرفة كيف تُبنى وتُطوّر',
            'I want to know how it is built',
            [
                'technology' => 3,
                'research' => 1,
            ]
        );

        $this->createOption(10,
            'أفكر كيف يمكن تحويلها إلى مشروع ناجح',
            'How it can become a successful business',
            [
                'business' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(10,
            'أفكر في تأثيرها على المجتمع والناس',
            'Its impact on society and people',
            [
                'humanitarian' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(10,
            'أتخيل ما يمكن ابتكاره باستخدامها',
            'Imagine what can be created with it',
            [
                'creativity' => 3,
                'technology' => 1,
            ]
        );

        $this->createOption(11,
            'شركة تقنية أو هندسية',
            'Technology or engineering company',
            [
                'technology' => 3,
                'analysis' => 1,
            ]
        );

        $this->createOption(11,
            'مختبر أو مركز أبحاث',
            'Research lab or research center',
            [
                'research' => 2,
                'scientific' => 2,
            ]
        );

        $this->createOption(11,
            'مؤسسة صحية أو إنسانية',
            'Healthcare or humanitarian organization',
            [
                'humanitarian' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(11,
            'شركة أو مؤسسة تجارية',
            'Business company',
            [
                'business' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(12,
            'أعمل بشكل مستقل وأتحمل كامل المسؤولية.',
            'I prefer to work independently and take full responsibility.',
            [
                'analysis' => 2,
                'research' => 1,
            ]
        );

        $this->createOption(12,
            'أعمل ضمن فريق مع توزيع واضح للمهام.',
            'I prefer working as part of a team with clearly divided responsibilities.',
            [
                'communication' => 2,
                'humanitarian' => 1,
            ]
        );

        $this->createOption(12,
            'أقود الفريق وأنظم العمل بين الأعضاء.',
            'I naturally take the lead and organize the team`s work.',
            [
                'communication' => 2,
                'leadership' => 3,
            ]
        );

        $this->createOption(12,
            'يعتمد ذلك على طبيعة المشروع.',
            'It depends on the nature of the project.',
            [
                'adaptability' => 3,
                'analysis' => 1,
            ]
        );

        $this->createOption(13,
            'حل المشكلات وتحليلها',
            'Solving and analyzing problems',
            [
                'analysis' => 3,
            ]
        );

        $this->createOption(13,
            'التعلم واكتشاف أشياء جديدة',
            'Learning and discovering new things',
            [
                'research' => 2,
                'scientific' => 1,
            ]
        );

        $this->createOption(13,
            'التعامل مع الناس والتواصل',
            'Interacting with people',
            [
                'communication' => 3,
                'humanitarian' => 1,
            ]
        );

        $this->createOption(13,
            'الإبداع وصناعة الأفكار',
            'Creativity and generating ideas',
            [
                'creativity' => 3,
            ]
        );

        $this->createOption(14,
            'الأعلى راتباً',
            'Highest salary',
            [
                'business' => 1,
                'leadership' => 1,
                'adaptability' => 1
            ]
        );

        $this->createOption(14,
            'الأكثر تأثيراً في المجتمع',
            'Most impact on society',
            [
                'humanitarian' => 3,
            ]
        );

        $this->createOption(14,
            'الأقرب لشغفي',
            'Closest to my passion',
            [
                'creativity' => 1,
                'adaptability' => 1,
            ]
        );

        $this->createOption(14,
            'الأكثر استقراراً',
            'Most stable',
            [
                'analysis' => 1,
            ]
        );

        $this->createOption(15,
            'خبيراً معروفاً في مجالي',
            'A recognized expert',
            [
                'research' => 2,
                'analysis' => 1,
            ]
        );

        $this->createOption(15,
            'صاحب شركة أو مشروع ناجح',
            'Successful business owner',
            [
                'business' => 3,
                'leadership' => 2,
            ]
        );

        $this->createOption(15,
            'باحثاً أو أكاديمياً',
            'Researcher or academic',
            [
                'research' => 3,
                'scientific' => 2,
            ]
        );

        $this->createOption(15,
            'شخصاً ترك أثراً إيجابياً في حياة الناس',
            'Someone who positively impacted people',
            [
                'humanitarian' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(16,
            'الدخل المرتفع',
            'High income',
            [
                'business' => 2,
                'leadership' => 1,
            ]
        );

        $this->createOption(16,
            'الاستقرار',
            'Stability',
            [
                'analysis' => 1,
            ]
        );

        $this->createOption(16,
            'الشغف والمتعة',
            'Passion and enjoyment',
            [
                'creativity' => 2,
                'adaptability' => 1,
            ]
        );

        $this->createOption(16,
            'التأثير والإنجاز',
            'Impact and achievement',
            [
                'humanitarian' => 2,
                'leadership' => 1,
            ]
        );

        $this->createOption(17,
            'تطوير تقنيات تغير العالم',
            'Develop world-changing technologies',
            [
                'technology' => 3,
                'creativity' => 1,
            ]
        );

        $this->createOption(17,
            'المساهمة في تحسين حياة الناس وصحتهم',
            'Improve people’s lives and health',
            [
                'humanitarian' => 3,
                'scientific' => 1,
            ]
        );

        $this->createOption(17,
            'بناء مشروع أو شركة كبيرة',
            'Build a large company',
            [
                'business' => 3,
                'leadership' => 2,
            ]
        );

        $this->createOption(17,
            'اكتشاف معرفة جديدة أو إجراء أبحاث',
            'Discover new knowledge',
            [
                'research' => 3,
                'scientific' => 2,
            ]
        );

        $this->createOption(18,
            'أبدأ الحديث وأتعرف على أشخاص جدد.',
            'Start conversations and meet new people.',
            [
                'communication' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(18,
            'أتحدث مع أشخاص أعرفهم وأستمتع بالنقاش معهم.',
            'Talk with people I already know and enjoy the conversation.',
            [
                'communication' => 2,
                'humanitarian' => 1,
            ]
        );

        $this->createOption(18,
            'أفضل الاستماع أكثر من التحدث.',
            'Prefer listening more than speaking.',
            [
                'analysis' => 2,
                'research' => 1,
            ]
        );

        $this->createOption(18,
            'أفضل البقاء مع عدد قليل من الأشخاص أو بمفردي.',
            'Prefer staying with a small group or by myself.',
            [
                'analysis' => 1,
                'research' => 1,
            ]
        );

        $this->createOption(19,
            'أواجه المشكلة مباشرة',
            'Face the problem directly',
            [
                'leadership' => 2,
                'communication' => 1,
            ]
        );

        $this->createOption(19,
            'أحلل الموقف بهدوء ومنطق',
            'Analyze the situation logically',
            [
                'analysis' => 3,
            ]
        );

        $this->createOption(19,
            'أتجنب التصعيد',
            'Avoid escalation',
            [
                'humanitarian' => 1,
                'communication' => 1,
            ]
        );

        $this->createOption(19,
            'أستمع أولاً لكل وجهات النظر',
            'Listen to all viewpoints first',
            [
                'communication' => 2,
                'humanitarian' => 1,
            ]
        );

        $this->createOption(20,
            'أوافق بشدة',
            'Strongly Agree',
            [
                'communication' => 4,
                'leadership' => 2,
            ]
        );

        $this->createOption(20,
            'أوافق',
            'Agree',
            [
                'communication' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(20,
            'محايد',
            'Neutral',
            [
                'communication' => 1,
            ]
        );

        $this->createOption(20,
            'لا أوافق',
            'Disagree',
            []
        );

        $this->createOption(20,
            'لا أوافق بشدة',
            'Strongly Disagree',
            [
                'analysis' => 1,
                'research' => 1,
            ]
        );

        $this->createOption(21,
            'تنظيم العمل وتوزيع المهام.',
            'Organizing the work and assigning tasks.',
            [
                'leadership' => 3,
                'communication' => 2,
            ]
        );

        $this->createOption(21,
            'تقديم الأفكار والحلول الإبداعية.',
            'Contributing creative ideas and solutions.',
            [
                'creativity' => 4,
                'technology' => 1,
            ]
        );

        $this->createOption(21,
            'تنفيذ المهام المطلوبة بدقة.',
            'Completing assigned tasks accurately.',
            [
                'analysis' => 2,
                'adaptability' => 2,
            ]
        );

        $this->createOption(21,
            'دعم الفريق وحل المشكلات بين الأعضاء.',
            'Supporting the team and resolving conflicts.',
            [
                'communication' => 2,
                'humanitarian' => 3,
            ]
        );

        $this->createOption(22,
            'أحلل الخطأ وأتعلم منه',
            'Analyze the mistake and learn from it',
            [
                'analysis' => 2,
                'adaptability' => 2,
            ]
        );

        $this->createOption(22,
            'أحاول مجدداً فوراً',
            'Try again immediately',
            [
                'adaptability' => 3,
            ]
        );

        $this->createOption(22,
            'أطلب آراء الآخرين',
            'Ask others for advice',
            [
                'communication' => 2,
                'humanitarian' => 1,
            ]
        );

        $this->createOption(22,
            'أغيّر أسلوبي بالكامل',
            'Change my approach completely',
            [
                'creativity' => 2,
                'adaptability' => 2,
            ]
        );

        $this->createOption(23,
            'تحليل أسبابها',
            'Analyze its causes',
            [
                'analysis' => 3,
                'research' => 1,
            ]
        );

        $this->createOption(23,
            'تجربة حلول جديدة',
            'Experiment with new solutions',
            [
                'creativity' => 2,
                'adaptability' => 2,
            ]
        );

        $this->createOption(23,
            'دراسة تأثيرها على الناس',
            'Study its impact on people',
            [
                'humanitarian' => 3,
                'communication' => 1,
            ]
        );

        $this->createOption(23,
            'تحويلها إلى فرصة أو مشروع',
            'Turn it into an opportunity',
            [
                'business' => 3,
                'leadership' => 1,
            ]
        );

        $this->createOption(24,
            'الأرقام والدراسات',
            'Numbers and studies',
            [
                'analysis' => 2,
                'scientific' => 2,
            ]
        );

        $this->createOption(24,
            'القصص والتجارب الإنسانية',
            'Human stories and experiences',
            [
                'communication' => 2,
                'humanitarian' => 2,
            ]
        );

        $this->createOption(24,
            'أمثلة النجاح من الواقع',
            'Real-world success stories',
            [
                'business' => 2,
                'leadership' => 1,
            ]
        );

        $this->createOption(24,
            'الأفكار والرؤى الجديدة',
            'New ideas and visions',
            [
                'creativity' => 3,
            ]
        );

    }
}
