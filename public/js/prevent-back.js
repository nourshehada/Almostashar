
(function() {
    'use strict';

    // ✅ منع الرجوع باستخدام History API
    function preventBackNavigation() {
        // إضافة state فارغ للتاريخ
        history.pushState(null, null, location.href);

        // الاستماع لمحاولة الرجوع
        window.addEventListener('popstate', function(event) {
            // إعادة إضافة state فوراً
            history.pushState(null, null, location.href);

        });
    }

    function blockBackNavigation() {
        history.pushState(null, null, location.href);

        window.onpopstate = function() {
            history.go(1);
        };
    }

    window.PreventBack = {
        prevent: preventBackNavigation,
        block: blockBackNavigation
    };

})();
