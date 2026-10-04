/* نماء - منصة العلوم الشرعية */

// رابط API الموحد
const API_URL = 'api.php';

// ========== القائمة في الجوال ==========
function toggleMenu() {
    const nav = document.getElementById('nav-menu');
    nav.classList.toggle('active');
}

// ========== التمرير للفعاليات ==========
function scrollToEvents(event) {
    event.preventDefault();
    const eventsSection = document.getElementById('all-events');
    if (eventsSection) {
        eventsSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// ========== العودة للأعلى ==========
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

window.addEventListener('scroll', function () {
    const scrollBtn = document.getElementById('scrollTop');
    if (scrollBtn) {
        if (window.scrollY > 300) {
            scrollBtn.classList.add('show');
        } else {
            scrollBtn.classList.remove('show');
        }
    }
});

// ========== فتح نافذة التسجيل ==========
function openModal(eventTitle, eventDate) {
    const modal = document.getElementById('registration-modal');
    const eventDisplay = document.getElementById('event-name-display');

    if (!modal || !eventDisplay) return;

    // عرض اسم الفعالية
    eventDisplay.textContent = eventTitle + ' - ' + eventDate;

    // حفظ البيانات المخفية
    document.getElementById('event-title').value = eventTitle;
    document.getElementById('event-date').value = eventDate;

    // إظهار النافذة
    modal.style.display = 'block';

    // منع التمرير في الخلفية
    document.body.style.overflow = 'hidden';
}

// ========== إغلاق نافذة التسجيل ==========
function closeModal() {
    const modal = document.getElementById('registration-modal');
    if (!modal) return;

    modal.style.display = 'none';

    // إعادة التمرير
    document.body.style.overflow = 'auto';

    // مسح النموذج
    const form = document.getElementById('registration-form');
    if (form) form.reset();
}

// ========== إغلاق عند الضغط خارج النافذة ==========
window.onclick = function (event) {
    const modal = document.getElementById('registration-modal');
    if (event.target == modal) {
        closeModal();
    }
}

// ========== إرسال التسجيل ==========
async function submitRegistration(event) {
    event.preventDefault();

    // جمع البيانات
    const nameInput = document.getElementById('reg-name');
    const emailInput = document.getElementById('reg-email');
    const phoneInput = document.getElementById('reg-phone');
    const eventTitleInput = document.getElementById('event-title');
    const eventDateInput = document.getElementById('event-date');

    // التحقق من وجود العناصر
    if (!nameInput || !emailInput || !phoneInput || !eventTitleInput || !eventDateInput) {
        console.error('❌ لم يتم العثور على عناصر النموذج');
        alert('❌ حدث خطأ في النموذج. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
        return false;
    }

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();
    const phone = phoneInput.value.trim();
    const eventTitle = eventTitleInput.value.trim();
    const eventDate = eventDateInput.value.trim();

    // التحقق من البيانات
    if (!name || !email || !phone) {
        alert('❌ يرجى ملء جميع الحقول المطلوبة:\n- الاسم الكامل\n- البريد الإلكتروني\n- رقم الجوال');
        return false;
    }

    // التحقق من صحة البريد الإلكتروني
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('❌ يرجى إدخال بريد إلكتروني صحيح');
        emailInput.focus();
        return false;
    }

    // التحقق من صحة رقم الجوال (يجب أن يبدأ بـ 05)
    if (!phone.match(/^05\d{8}$/)) {
        alert('❌ يرجى إدخال رقم جوال صحيح (يبدأ بـ 05 ويحتوي على 10 أرقام)');
        phoneInput.focus();
        return false;
    }

    // تحديد نوع الفعالية
    let type = 'محاضرة';
    if (eventTitle.includes('ندوة') || eventTitle.toLowerCase().includes('seminar')) {
        type = 'ندوة';
    } else if (eventTitle.includes('دورة') || eventTitle.toLowerCase().includes('course')) {
        type = 'دورة';
    }

    // البيانات المرسلة
    const data = {
        name: name,
        email: email,
        phone: phone,
        eventTitle: eventTitle,
        title: eventTitle, // دعم كلا الحقلين
        eventDate: eventDate,
        type: type
    };

    try {
        // إرسال إلى الخادم
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        console.log('📊 نتيجة التسجيل:', result);

        if (result.success) {
            // نجح التسجيل
            closeModal();

            // رسالة نجاح مخصصة
            const regId = result.registration_id || result.data?.id || Math.floor(Math.random() * 10000);
            showSuccessMessage(name, eventTitle, eventDate, regId);

        } else {
            // فشل التسجيل
            alert('❌ حدث خطأ في التسجيل:\n' + result.message);
        }

    } catch (error) {
        console.error('❌ خطأ في الاتصال:', error);
        alert('❌ حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
    }

    return false;
}

// ========== رسالة النجاح المخصصة ==========
function showSuccessMessage(name, eventTitle, eventDate, registrationId) {
    const message = `
✅ تم التسجيل بنجاح!

━━━━━━━━━━━━━━━━━━━━━━

📝 رقم التسجيل: ${registrationId}

👤 الاسم: ${name}
🎯 الفعالية: ${eventTitle}
📅 التاريخ: ${eventDate}

━━━━━━━━━━━━━━━━━━━━━━

✉️ سيتم إرسال رسالة تأكيد إلى بريدك الإلكتروني
📞 سيتم التواصل معك قبل موعد الفعالية

شكراً لتسجيلك في منصة نماء! 🌱
    `;

    alert(message);
}

// ========== الاشتراك في النشرة البريدية ==========
async function subscribeNewsletter(event) {
    event.preventDefault();

    const emailInput = event.target.querySelector('input[type="email"]');
    const email = emailInput.value.trim();
    const submitBtn = event.target.querySelector('button[type="submit"]');

    // التحقق من البريد الإلكتروني
    if (!email || !email.includes('@')) {
        alert('❌ يرجى إدخال بريد إلكتروني صحيح');
        return false;
    }

    // تعطيل الزر
    const originalHTML = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التسجيل...';

    const data = {
        email: email,
        type: 'newsletter',
        name: email.split('@')[0], // استخدام جزء من البريد كاسم
        phone: '-'
    };

    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            alert(`✅ شكراً لاشتراكك في النشرة البريدية!\n\nتم تسجيل البريد:\n${email}\n\nسيتم إرسال آخر الأخبار والفعاليات إلى بريدك.`);
            emailInput.value = '';
        } else {
            alert('❌ حدث خطأ: ' + result.message);
        }

    } catch (error) {
        console.error('❌ خطأ في الاتصال:', error);
        alert('❌ حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHTML;
    }

    return false;
}

// ========== إرسال رسالة التواصل (صفحة اتصل بنا) ==========
async function sendContactMessage(event) {
    event.preventDefault();

    const name = document.getElementById('contact-name').value.trim();
    const email = document.getElementById('contact-email').value.trim();
    const phone = document.getElementById('contact-phone').value.trim();
    const subject = document.getElementById('contact-subject').value.trim();
    const message = document.getElementById('contact-message').value.trim();

    // التحقق من البيانات
    if (!name || !email || !phone || !subject || !message) {
        alert('❌ يرجى ملء جميع الحقول المطلوبة');
        return false;
    }

    const data = {
        name: name,
        email: email,
        phone: phone,
        subject: subject,
        message: message,
        type: 'contact'
    };

    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            alert(`✅ تم إرسال رسالتك بنجاح!\n\nشكراً ${name}\n\nسنتواصل معك قريباً على:\n📧 ${email}\n📱 ${phone}`);
            event.target.reset();
        } else {
            alert('❌ حدث خطأ: ' + result.message);
        }

    } catch (error) {
        console.error('❌ خطأ في الاتصال:', error);
        alert('❌ حدث خطأ في الاتصال بالخادم. يرجى المحاولة مرة أخرى.');
    }

    return false;
}

// ========== تبديل الأسئلة الشائعة (FAQ) ==========
function toggleFaq(element) {
    const faqItem = element.parentElement;
    const answer = faqItem.querySelector('.faq-answer');
    const icon = element.querySelector('i');

    // إغلاق جميع الإجابات الأخرى
    document.querySelectorAll('.faq-item').forEach(item => {
        if (item !== faqItem && item.classList.contains('active')) {
            item.classList.remove('active');
            item.querySelector('.faq-answer').style.maxHeight = null;
            item.querySelector('.faq-question i').style.transform = 'rotate(0deg)';
        }
    });

    // تبديل الإجابة الحالية
    faqItem.classList.toggle('active');

    if (faqItem.classList.contains('active')) {
        answer.style.maxHeight = answer.scrollHeight + 'px';
        icon.style.transform = 'rotate(180deg)';
    } else {
        answer.style.maxHeight = null;
        icon.style.transform = 'rotate(0deg)';
    }
}

// ========== تصفية المحاضرات (صفحة المحاضرات) ==========
function filterLectures(category) {
    const cards = document.querySelectorAll('.lecture-card');
    const buttons = document.querySelectorAll('.filter-btn');

    // Update active button
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.closest('.filter-btn').classList.add('active');

    // Filter cards
    cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.5s';
        } else {
            card.style.display = 'none';
        }
    });
}

// ========== الأحداث عند تحميل الصفحة ==========
document.addEventListener('DOMContentLoaded', () => {

    // تأثير الأرقام (Stats Counter)
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('.stat-number');
                counters.forEach(counter => {
                    if (!counter.classList.contains('animated')) {
                        animateCounter(counter);
                        counter.classList.add('animated');
                    }
                });
            }
        });
    }, { threshold: 0.5 });

    const statsSection = document.querySelector('.stats');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    // إغلاق القائمة عند الضغط خارجها
    document.addEventListener('click', (e) => {
        const nav = document.getElementById('nav-menu');
        const menuToggle = document.querySelector('.menu-toggle');

        if (nav && nav.classList.contains('active') &&
            !nav.contains(e.target) &&
            !menuToggle.contains(e.target)) {
            nav.classList.remove('active');
        }
    });

    // إغلاق النافذة بمفتاح ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    console.log('%c🌱 نماء - منصة العلوم الشرعية',
        'font-size: 20px; font-weight: bold; color: #006C35;');
    console.log('%cالموقع جاهز للعمل ✅',
        'font-size: 14px; color: #C9A961;');
});

// ========== تحريك الأرقام ==========
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = 0;

    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString('ar-SA');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString('ar-SA');
        }
    }, 16);
}

// تم حذف الدالة المكررة submitRegistration - الدالة الصحيحة موجودة في السطر 88
// تم حذف الدوال المساعدة المكررة - الدوال الصحيحة موجودة في الأعلى

// CSS للأنيميشن
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
// ========== تحميل البيانات عند فتح الصفحة ==========
document.addEventListener('DOMContentLoaded', function () {
    // تحميل البيانات فقط إذا كانت لوحة التحكم موجودة
    if (document.getElementById('tableBody')) {
        loadRegistrations();
        setupEventListeners();
    }
});

// ========== إعداد مستمعي الأحداث ==========
function setupEventListeners() {
    // البحث - التحقق من وجود العنصر أولاً
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', filterRegistrations);
    }

    // الفلترة حسب النوع - التحقق من وجود العنصر أولاً
    const filterType = document.getElementById('filterType');
    if (filterType) {
        filterType.addEventListener('change', filterRegistrations);
    }
}

// ========== تحميل جميع التسجيلات من api.php ==========
let allRegistrations = [];

async function loadRegistrations() {
    // التحقق من وجود الجدول (موجود فقط في لوحة التحكم)
    if (!document.getElementById('tableBody')) {
        return; // الخروج إذا لم يكن الجدول موجوداً
    }

    try {
        // طلب البيانات من api.php
        const response = await fetch('api.php?action=get_all');
        const result = await response.json();

        if (result.success) {
            allRegistrations = result.data || [];

            // حساب الإحصائيات
            const stats = calculateStats(allRegistrations);

            // تحديث الإحصائيات
            updateStatistics(stats);

            // عرض الجدول
            displayRegistrations(allRegistrations);
        } else {
            showNotification('❌ ' + (result.message || 'فشل تحميل البيانات'), 'error');
            displayRegistrations([]);
        }
    } catch (error) {
        console.error('خطأ في تحميل البيانات:', error);
        showNotification('❌ حدث خطأ في تحميل البيانات', 'error');
        displayRegistrations([]);
    }
}

// ========== حساب الإحصائيات ==========
function calculateStats(registrations) {
    return {
        total: registrations.length,
        courses: registrations.filter(r => r.type === 'course' || r.event_type === 'course').length,
        lectures: registrations.filter(r => r.type === 'lecture' || r.event_type === 'lecture').length,
        programs: registrations.filter(r => r.type === 'program' || r.event_type === 'program').length
    };
}

// ========== تحديث الإحصائيات ==========
function updateStatistics(stats) {
    const totalCount = document.getElementById('totalCount');
    const coursesCount = document.getElementById('coursesCount');
    const lecturesCount = document.getElementById('lecturesCount');
    const programsCount = document.getElementById('programsCount');

    if (totalCount) totalCount.textContent = stats.total;
    if (coursesCount) coursesCount.textContent = stats.courses;
    if (lecturesCount) lecturesCount.textContent = stats.lectures;
    if (programsCount) programsCount.textContent = stats.programs;
}

// ========== عرض التسجيلات في الجدول ==========
function displayRegistrations(registrations) {
    const tbody = document.getElementById('tableBody');

    // التحقق من وجود الجدول (موجود فقط في لوحة التحكم)
    if (!tbody) {
        return; // الخروج إذا لم يكن الجدول موجوداً
    }

    if (registrations.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="no-data">
                    <i class="fas fa-inbox"></i>
                    <p>لا توجد تسجيلات حتى الآن</p>
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = '';

    registrations.forEach((reg, index) => {
        // دعم كلا الصيغتين (type و event_type)
        const eventType = reg.type || reg.event_type;
        const eventName = reg.eventName || reg.event_name;

        const typeText = getTypeText(eventType);
        const typeBadge = `<span class="type-badge ${eventType}">${typeText}</span>`;

        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${reg.name}</td>
                <td>${reg.email}</td>
                <td>${reg.phone}</td>
                <td>${typeBadge}</td>
                <td>${eventName}</td>
                <td>${reg.city || '-'}</td>
                <td>${reg.date || reg.registration_date || '-'}</td>
                <td>
                    <button class="btn btn-delete" onclick="deleteRegistration(${reg.id})">
                        <i class="fas fa-trash"></i>
                        حذف
                    </button>
                </td>
            </tr>
        `;

        tbody.innerHTML += row;
    });
}

// ========== الحصول على نص النوع بالعربي ==========
function getTypeText(type) {
    switch (type) {
        case 'course':
            return 'دورة';
        case 'lecture':
            return 'محاضرة';
        case 'program':
            return 'برنامج دعوي';
        default:
            return type || '-';
    }
}

// ========== فلترة التسجيلات (البحث + النوع) ==========
function filterRegistrations() {
    const searchInput = document.getElementById('searchInput');
    const filterTypeSelect = document.getElementById('filterType');

    // التحقق من وجود العناصر
    if (!searchInput || !filterTypeSelect) {
        return;
    }

    const searchTerm = searchInput.value.toLowerCase();
    const filterType = filterTypeSelect.value;

    let filteredRegistrations = [...allRegistrations];

    // فلترة حسب النوع
    if (filterType !== 'all') {
        filteredRegistrations = filteredRegistrations.filter(r => {
            const eventType = r.type || r.event_type;
            return eventType === filterType;
        });
    }

    // فلترة حسب البحث
    if (searchTerm) {
        filteredRegistrations = filteredRegistrations.filter(r => {
            const eventName = r.eventName || r.event_name || '';
            return (
                r.name.toLowerCase().includes(searchTerm) ||
                r.email.toLowerCase().includes(searchTerm) ||
                r.phone.includes(searchTerm) ||
                eventName.toLowerCase().includes(searchTerm)
            );
        });
    }

    displayRegistrations(filteredRegistrations);
}

// ========== حذف تسجيل واحد ==========
async function deleteRegistration(id) {
    if (!confirm('هل أنت متأكد من حذف هذا التسجيل؟')) {
        return;
    }

    try {
        const response = await fetch('api.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('✅ تم حذف التسجيل بنجاح', 'success');
            loadRegistrations(); // إعادة تحميل البيانات
        } else {
            showNotification('❌ ' + (result.message || 'فشل حذف التسجيل'), 'error');
        }
    } catch (error) {
        console.error('خطأ في حذف التسجيل:', error);
        showNotification('❌ حدث خطأ في حذف التسجيل', 'error');
    }
}

// ========== حذف جميع التسجيلات ==========
async function clearAll() {
    if (!confirm('⚠️ هل أنت متأكد من حذف جميع التسجيلات؟\n\nهذا الإجراء لا يمكن التراجع عنه!')) {
        return;
    }

    if (!confirm('تأكيد نهائي: سيتم حذف جميع البيانات!')) {
        return;
    }

    try {
        const response = await fetch('api.php?action=delete_all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('✅ تم حذف جميع التسجيلات بنجاح', 'success');
            loadRegistrations(); // إعادة تحميل البيانات
        } else {
            showNotification('❌ ' + (result.message || 'فشل حذف البيانات'), 'error');
        }
    } catch (error) {
        console.error('خطأ في حذف البيانات:', error);
        showNotification('❌ حدث خطأ في حذف البيانات', 'error');
    }
}

// ========== تصدير إلى Excel ==========
function exportToExcel() {
    if (allRegistrations.length === 0) {
        alert('لا توجد بيانات للتصدير');
        return;
    }

    // إنشاء محتوى CSV
    let csv = '\uFEFF'; // BOM للدعم العربي
    csv += 'الرقم,الاسم,البريد الإلكتروني,الجوال,النوع,اسم الفعالية,المدينة,التاريخ\n';

    allRegistrations.forEach((reg, index) => {
        const eventType = reg.type || reg.event_type;
        const eventName = reg.eventName || reg.event_name || '';
        const date = reg.date || reg.registration_date || '-';

        csv += `${index + 1},"${reg.name}","${reg.email}","${reg.phone}","${getTypeText(eventType)}","${eventName}","${reg.city || '-'}","${date}"\n`;
    });

    // تحميل الملف
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', `registrations_${new Date().getTime()}.csv`);
    link.style.visibility = 'hidden';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showNotification('✅ تم تصدير البيانات بنجاح', 'success');
}

// ========== طباعة الجدول ==========
function printTable() {
    window.print();
}

// ========== عرض إشعار ==========
function showNotification(message, type = 'success') {
    // إزالة أي إشعار سابق
    const oldNotification = document.querySelector('.custom-notification');
    if (oldNotification) {
        oldNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = 'custom-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'};
        color: white;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 100000;
        font-size: 16px;
        font-weight: 600;
        animation: slideIn 0.4s ease;
        min-width: 300px;
        text-align: center;
    `;

    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.4s ease';
        setTimeout(() => notification.remove(), 400);
    }, 4000);
}

// ========== CSS للأنيميشن (تم إضافته مسبقاً في السطر 526) ==========
// تم إزالة التكرار - الكود موجود في الأعلى