<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - موقع نماء</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ===== رأس الصفحة ===== */
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-content h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-content p {
            font-size: 16px;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        /* ===== بطاقات الإحصائيات ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .stat-icon.total { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .stat-icon.courses { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .stat-icon.lectures { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .stat-icon.seminars { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }

        .stat-content h3 {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-content p {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
        }

        /* ===== الجدول الرئيسي ===== */
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h2 {
            font-size: 24px;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ===== أدوات البحث والفلترة ===== */
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .filter-box select {
            padding: 12px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Cairo', sans-serif;
            background: white;
            cursor: pointer;
            min-width: 200px;
        }

        /* ===== الأزرار ===== */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-family: 'Cairo', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 8px 16px;
            font-size: 14px;
        }

        /* ===== الجدول ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .data-table thead {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            color: white;
        }

        .data-table th {
            padding: 15px;
            text-align: right;
            font-weight: 600;
            font-size: 14px;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .data-table tbody tr:hover {
            background: #f9fafb;
        }

        .data-table td {
            padding: 15px;
            font-size: 14px;
            color: #374151;
        }

        /* ===== شارات النوع ===== */
        .type-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: white;
        }

        .type-badge.دورة,
        .type-badge.course {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .type-badge.محاضرة,
        .type-badge.lecture {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .type-badge.ندوة,
        .type-badge.seminar,
        .type-badge.program {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }

        .type-badge.نشرة {
            background: linear-gradient(135deg, #fa709a, #fee140);
        }

        /* ===== حالة فارغة ===== */
        .no-data {
            text-align: center;
            padding: 60px 20px !important;
            color: #9ca3af;
        }

        .no-data i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-data p {
            font-size: 18px;
            font-weight: 500;
        }

        /* ===== استجابة الشاشات الصغيرة ===== */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header-content h1 {
                font-size: 24px;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                padding: 20px;
                overflow-x: auto;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }

            .search-filter {
                flex-direction: column;
            }

            .search-box,
            .filter-box select {
                width: 100%;
            }
        }

        /* ===== تحميل ===== */
        .loading {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        .loading i {
            font-size: 48px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ===== الإشعارات ===== */
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
    </style>
</head>
<body>
    <div class="container">
        <!-- رأس الصفحة -->
        <div class="header">
            <div class="header-content">
                <h1>
                    <i class="fas fa-th-large"></i>
                    لوحة التحكم - موقع نماء
                </h1>
                <p>إدارة التسجيلات والفعاليات</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-success" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i>
                    تصدير Excel
                </button>
                <button class="btn btn-warning" onclick="printTable()">
                    <i class="fas fa-print"></i>
                    طباعة
                </button>
                <button class="btn btn-danger" onclick="clearAll()">
                    <i class="fas fa-trash-alt"></i>
                    حذف الكل
                </button>
            </div>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>إجمالي التسجيلات</h3>
                    <p id="totalCount">0</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon courses">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-content">
                    <h3>الدورات</h3>
                    <p id="coursesCount">0</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon lectures">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <h3>المحاضرات</h3>
                    <p id="lecturesCount">0</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon seminars">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <h3>الندوات</h3>
                    <p id="seminarsCount">0</p>
                </div>
            </div>
        </div>

        <!-- الجدول الرئيسي -->
        <div class="table-container">
            <div class="table-header">
                <h2>
                    <i class="fas fa-list"></i>
                    قائمة التسجيلات
                </h2>
            </div>

            <!-- البحث والفلترة -->
            <div class="search-filter">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="ابحث بالاسم، البريد، أو رقم الجوال..." onkeyup="filterRegistrations()">
                    <i class="fas fa-search"></i>
                </div>
                
                <div class="filter-box">
                    <select id="filterType" onchange="filterRegistrations()">
                        <option value="all">جميع الأنواع</option>
                        <option value="دورة">الدورات فقط</option>
                        <option value="محاضرة">المحاضرات فقط</option>
                        <option value="ندوة">الندوات فقط</option>
                        <option value="course">Course</option>
                        <option value="lecture">Lecture</option>
                        <option value="seminar">Seminar</option>
                        <option value="program">Program</option>
                    </select>
                </div>
            </div>

            <!-- الجدول -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الجوال</th>
                        <th>النوع</th>
                        <th>اسم الفعالية</th>
                        <th>المدينة</th>
                        <th>التاريخ</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="9" class="loading">
                            <i class="fas fa-spinner"></i>
                            <p>جاري التحميل...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // تحميل التسجيلات عند فتح الصفحة
        window.addEventListener('DOMContentLoaded', function() {
            loadRegistrations();
        });
    </script>
</body>
</html>