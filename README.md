# نماء

## 1 ما هو المشروع

منصة صفحات ثابتة للعلوم الشرعية تعرض محاضرات وندوات ودورات، وتسجّل الزائر عبر `api.php` في MySQL. الاسم في الملفات: نماء، منصة العلوم الشرعية. قاعدة البيانات `nemaa_db`.

## 2 لماذا وُجد

الملف السابق يصف نشر العلوم الشرعية عبر المحاضرات والندوات والدورات، مع رسائل تواصل ونشرة بريدية ولوحة تسجيلات. `api.php` ينفّذ الحفظ في الجداول الخمسة. لا إرسال بريد فعلي للنشرة في الكود.

## 3 المستخدمون

زائر يملأ النماذج في `index.html` و`lectures.html` و`courses.html` و`seminars.html` و`contact.html` و`about.html`. لوحة `admin-panel-full.php` بلا تسجيل دخول في الملف. لا جدول مستخدمين في `nemaa_database.sql`.

## 4 القدرات

- تسجيل دورة أو محاضرة أو ندوة بحالة `ينتظر`.
- حفظ رسالة تواصل بحالة `جديد`.
- اشتراك نشرة مع رفض البريد المكرر (`unique_email`).
- جلب كل التسجيلات والرسائل عبر `action=get_all`.
- حذف سجل عبر `action=delete`.
- لوحة في المتصفح تعرض إحصاءات وتصفّي وتطبع الجدول وتصدّر من المتصفح (`exportToExcel` و`printTable` في `script.js`).

## 5 كيف يعمل

`script.js` يضبط `API_URL = 'api.php'` ويرسل JSON. `api.php` يقرأ `action` ثم يفرع: GET لـ `get_all`، وDELETE أو POST `delete` للحذف، ثم POST الباقي للتسجيل أو التواصل أو النشرة. الاتصال من `config.php` عبر PDO. الملف المستدعى في السطر هو `Config.php`.

تناقض اسم الملف: القرص يحتوي `config.php`. على نظام لا يفرّق حالة الأحرف ينجح `require_once 'Config.php'`. استنتاج من الكود: على نظام يفرّق الحالة يفشل التحميل لأن الاسم على القرص `config.php`. الملف السابق يطلب تعديل `Config.php` بنفس الثوابت الموجودة في `config.php`.

## 6 أمثلة واقعية

`submitRegistration` في `script.js` يرسل الاسم والبريد والجوال والنوع والعنوان. `api.php` إن احتوى النوع «دورة» أو `course` يدرج في `course_registrations`. المحاضرة إلى `lecture_registrations`. الندوة إلى `seminar_registrations`. الملاحظة تحفظ «تاريخ الفعالية:» زائد `eventDate` إن وُجد.

`subscribeNewsletter` يرسل نوع نشرة. الخادم يتحقق من `newsletter_subscriptions` ثم يدرج.

`clearAll` في `script.js` يستدعي `api.php?action=delete_all`. لا فرع `delete_all` داخل `api.php`. الطلب يسقط في مسار POST العام فيُرفض أو يُفسَّر كنموذج ناقص.

## 7 رحلة المستخدم

الزائر يفتح `index.html` (الصفحة الفعلية للمحتوى). يختار فعالية ويملأ النموذج فيُرجع `api.php` رمز 201 و`registration_id`. رسالة التواصل من صفحة اتصل بنا تُحفظ في `contact_messages`. من يفتح `admin-panel-full.php` يرى `get_all` بلا حساب.

`index.php` في الجذر يبني عنواناً من المضيف ويحوّل إلى المسار `/dashboard/` ثم يتوقف. هذا ملف توجيه افتراضي وليس صفحة نماء. الصفحة العامة هي `index.html`.

## 8 الوحدات

| الوحدة | الملف |
| --- | --- |
| الصفحات | `index.html` `about.html` `contact.html` `courses.html` `lectures.html` `seminars.html` |
| السلوك | `script.js` |
| المظهر | `styles.css` |
| الخادم | `api.php` و`config.php` |
| اللوحة | `admin-panel-full.php` |
| المخطط | `nemaa_database.sql` |
| توجيه مختلف | `index.php` |

## 9 الجهات والكيانات

العلامة في النصوص: نماء. لا جدول جهات. البذرة أسماء وأمثلة بريد مثل `ahmed@example.com` داخل SQL.

## 10 الصلاحيات

غير موجود في الملفات الحالية. `get_all` و`delete` متاحان لأي متصل يستطيع طلب `api.php`. اللوحة HTML بلا جلسة.

## 11 الأتمتة

`registration_date` و`created_at` و`subscribed_at` تُملأ بـ `NOW()` أو `CURRENT_TIMESTAMP`. حالة التسجيل الابتدائية `ينتظر`. حالة الرسالة `جديد`. حالة النشرة `نشط`. لا مهمة تغيّر الحالة إلى مقبول أو مقروء.

## 12 أثر الوحدات على بعضها

التسجيل في الواجهة يكتب جدولاً واحداً حسب النوع. `get_all` يدمج الجداول الأربعة (دورات، محاضرات، ندوات، رسائل) في مصفوفة واحدة مع `event_type`. اشتراكات النشرة لا تدخل هذا الدمج في الاستعلامات المفحوصة داخل فرع `get_all`. حذف بالجدول الذي يرسله العميل أو بالبحث في أربعة جداول إن غاب اسم الجدول. النشرة ليست ضمن ذلك البحث.

## 13 المعجم

| المصطلح | المعنى |
| --- | --- |
| دورة / محاضرة / ندوة | صف في الجدول المقابل والحالة الابتدائية `ينتظر` |
| رسالة | صف `contact_messages` |
| نشرة | صف `newsletter_subscriptions` |
| لوحة | `admin-panel-full.php` تعمل في المتصفح ضد `api.php` |

## 14 الأسئلة الشائعة

| السؤال | الجواب |
| --- | --- |
| أي عنوان أفتح؟ | المحتوى في `index.html`. `index.php` يحوّل إلى `/dashboard/` خارج هذا المجلد |
| هل تُرسل النشرة بالبريد؟ | الحفظ فقط. لا دالة بريد |
| هل زر حذف الجميع يعمل؟ | الواجهة تستدعي `delete_all` والخادم لا يعرّف هذا الإجراء |
| ما إصدار PHP؟ | الملف السابق: 7.4 أو أحدث وMySQL 5.7 أو أحدث. غير موثق في ملف اعتماديات |

## 15 المعمارية

```
صفحات HTML + script.js
        |
        +-- fetch api.php
                 |
                 +-- require Config.php  --> الملف على القرص config.php
                 +-- PDO nemaa_db
admin-panel-full.php -- نفس api.php (get_all / delete)
index.php -- تحويل إلى /dashboard/  (مسار آخر)
```

## 16 التقنيات المستخدمة

HTML وCSS وJavaScript. PHP PDO. MySQL `utf8mb4`. خط Cairo من Google Fonts وFont Awesome 6.4.0 في رأس `admin-panel-full.php`. لا Composer.

## 17 شجرة الملفات

```
Nama/
├── index.html index.php
├── about.html contact.html courses.html lectures.html seminars.html
├── admin-panel-full.php
├── api.php config.php script.js styles.css
├── nemaa_database.sql
└── README.md
```

## 18 الواجهة الأمامية

اتجاه عربي في لوحة التحكم (`lang="ar"` و`dir="rtl"`). الصفحات العامة تشارك `script.js` و`styles.css`. دوال ظاهرة: `toggleMenu` و`openModal` و`submitRegistration` و`subscribeNewsletter` و`sendContactMessage` و`toggleFaq` و`filterLectures` و`loadRegistrations` و`deleteRegistration` و`clearAll` و`exportToExcel`. لا `rel="icon"` في الملفات المفحوصة.

## 19 الواجهة الخلفية

`api.php` يضبط JSON و`Access-Control-Allow-Origin: *` والطرق POST وGET وOPTIONS وDELETE. أخطاء SQL تُرجع أحياناً في الحقل `error` مع رسالة عامة. فشل الاتصال في `config.php` يرجع JSON فيه نص استثناء PDO.

## 20 تدفق الطلب

مثال تسجيل محاضرة:

```
POST api.php
JSON: name, email, phone, type يحتوي «محاضرة», eventTitle
  -> filter_var للبريد
  -> الجدول lecture_registrations
  -> INSERT status = ينتظر
  -> 201 و registration_id
```

## 21 جداول قاعدة البيانات

| الجدول | حقول أساسية |
| --- | --- |
| course_registrations | name, email, phone, type, title, status, notes, registration_date |
| lecture_registrations | نفس النمط والفهارس على email وphone وstatus والتاريخ |
| seminar_registrations | نفس النمط |
| contact_messages | subject, message, status, created_at, read_at |
| newsletter_subscriptions | email فريد، name, status, subscribed_at, unsubscribed_at |

تعليق رأس SQL يذكر تاريخ الإنشاء 2025. البذرة صفوف دورات ومحاضرات وندوات ورسائل واشتراك.

## 22 نقاط النهاية

| الطلب | النتيجة |
| --- | --- |
| `GET api.php?action=get_all` | دمج أربعة جداول |
| `POST api.php?action=delete` أو DELETE | حذف حسب id وtable |
| `POST api.php` بلا إجراء خاص | تسجيل أو تواصل أو نشرة حسب الحقول |
| `POST api.php?action=delete_all` | غير معرّف في `api.php` |
| صفحات `*.html` | واجهة |
| `admin-panel-full.php` | لوحة |
| `index.php` | تحويل إلى `/dashboard/` |

## 23 المصادقة

غير موجود في الملفات الحالية.

## 24 ضوابط الأمان الموجودة فعلياً

- PDO وتحضير القيم في الإدراج.
- `FILTER_VALIDATE_EMAIL` لمسار التواصل والتسجيل.
- ترويسات CORS مفتوحة `*`.
- عند الحذف: إن كان اسم الجدول فارغاً يُبحث في قائمة أربعة جداول. إن أرسل العميل `table` يُدرج الاسم في جملة `DELETE FROM {$table}` بلا قائمة بيضاء في ذلك الفرع.
- لا جلسة ولا CSRF.
- رسائل الخطأ قد تتضمن `$e->getMessage()`.

## 25 الإعدادات

ثوابت `config.php`: `DB_HOST` = `localhost`، `DB_USER` = `root`، `DB_PASS` فارغة، `DB_NAME` = `nemaa_db`. لا `.env`. الملف السابق يطابق هذه الأسماء.

## 26 التكاملات

خطوط Google وFont Awesome من cdnjs في اللوحة. لا مزود بريد ولا دفع.

## 27 المهام المجدولة

غير موجود في الملفات الحالية.

## 28 تخزين الملفات

غير موجود في الملفات الحالية. لا رفع.

## 29 السجلات

غير موجود في الملفات الحالية. `console.error` في المتصفح عند فشل الجلب في `script.js`.

## 30 التثبيت

1. PHP مع PDO MySQL وMySQL. الملف السابق يذكر 7.4 وMySQL 5.7 وخادم Apache أو Nginx أو حزمة محلية.
2. استيراد `nemaa_database.sql` فينشئ `nemaa_db`.
3. مطابقة ثوابت `config.php`.
4. فتح `index.html` عبر خادم الويب حتى يصل `fetch` إلى `api.php`.

## 31 دليل التطوير

النماذج في HTML، والإرسال في `script.js`، والقواعد في `api.php`. نوع جديد يعني جدولاً وفرعاً في تطبيع النوع ودمجه في `get_all` إن لزم ظهوره في اللوحة. اسم ملف الاتصال على القرص `config.php` بينما الاستدعاء `Config.php`.

## 32 النشر

غير موثق. `index.php` إن وُضع كصفحة افتراضية للمجلد يحوّل الزائر بعيداً عن `index.html` إلى `/dashboard/`. انشر الصفحة الافتراضية على `index.html` أو أزل أثر هذا التحويل خارج نطاق هذا الدليل.

## 33 النسخ الاحتياطي

غير موثق. انسخ قاعدة `nemaa_db`. لا مرفقات.

## 34 استكشاف الأخطاء

| الظاهرة | الاستنتاج من الكود |
| --- | --- |
| فشل الاتصال JSON 500 | ثوابت القاعدة أو غياب `nemaa_db`، والرسالة قد تحتوي نص PDO |
| api.php لا يجد Config.php | اختلاف حالة الأحرف عن `config.php` |
| فتح المجلد يغادر الموقع | `index.php` يرسل `Location` إلى `/dashboard/` |
| حذف الجميع يفشل | `delete_all` غير موجود في `api.php` |
| اشتراك مكرر | المفتاح `unique_email` أو الفحص قبل الإدراج |

## 35 الاعتماديات

لا مدير حزم. Font Awesome 6.4.0 في لوحة التحكم. PHP PDO و`json`. إصدار PHP من الملف السابق فقط.

## 36 القيود المعروفة

- لوحة وواجهة حذف بلا مصادقة.
- CORS للجميع.
- `delete_all` في الواجهة بلا تنفيذ خادم.
- اسم جدول الحذف من العميل يُبنى داخل SQL عند إرساله.
- `index.php` لا يعرض نماء.
- لا تهريب إضافي غير تحضير PDO للنصوص المخزنة. العرض في اللوحة يعتمد على بناء HTML في `script.js`.
- لا أيقونة تبويب في الملفات المفحوصة.
- حالات «مقبول» و«مرفوض» موثقة كتعليق في SQL بلا شاشة انتقال في `api.php`.

## 37 الحالة الحالية

موقع تعريفي مع حفظ تسجيلات ورسائل واشتراك، ولوحة مفتوحة على نفس الواجهة. بذرة SQL اختيارية مدمجة في ملف الإنشاء.

## 38 قرارات معمارية

- استنتاج من الكود: ثلاثة جداول تسجيل متشابهة بدل جدول فعاليات واحد، والدمج يتم في `get_all`.
- استنتاج من الكود: النشرة مسار داخل POST العام لا إجراء `action` مستقل.
- استنتاج من الكود: اللوحة صفحة PHP بلا خادم للقوائم، والجلب من المتصفح.

## 39 سجل التغييرات

غير موجود في الملفات الحالية. تعليق SQL يذكر سنة 2025 بلا رقم إصدار.

## System Overview

نماء يجمع نماذج المحاضرات والدورات والندوات والتواصل والنشرة في `api.php` ويخزنها في `nemaa_db`، مع لوحة تعرض الدمج بلا حساب.

## Quick Reference

| الجزء | التقنية | الموقع | الدور |
| --- | --- | --- | --- |
| الواجهة | HTML/JS | `*.html` و`script.js` | النماذج واللوحة العميلة |
| الواجهة البرمجية | PHP | `api.php` | حفظ وجلب وحذف |
| الاتصال | PDO | `config.php` | `nemaa_db` |
| اللوحة | PHP/JS | `admin-panel-full.php` | عرض `get_all` |
| المخطط | SQL | `nemaa_database.sql` | 5 جداول |
| توجيه الجذر | PHP | `index.php` | تحويل إلى `/dashboard/` |
| التنسيق | CSS | `styles.css` | المظهر |

## Quick Start

1. استورد `nemaa_database.sql`.
2. راجع ثوابت `config.php`.
3. افتح `index.html` من خادم PHP لا تعتمد `index.php` كصفحة البداية.

## For Non-Technical Users

اختر محاضرة أو دورة أو ندوة واضغط التسجيل واملأ الاسم والبريد والجوال. الرسالة في اتصل بنا تُحفظ للعرض في لوحة التحكم. الاشتراك في النشرة يحفظ البريد ولا يرسل رسائل بريد من هذا المشروع. لوحة التحكم تفتح بلا كلمة مرور في النسخة الحالية.

## For Developers

أضف فرع `delete_all` فقط إذا بقي زر `clearAll`، فهو اليوم بلا مقابل. وحّد اسم ملف الاتصال مع `require_once` قبل النشر على نظام حسّاس لحالة الأحرف. أي حذف يجب أن يبقى على قائمة جداول ثابتة حتى لا يُؤخذ اسم الجدول من الطلب.
