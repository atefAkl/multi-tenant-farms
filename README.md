# نظام إدارة مزارع النخيل 🏜️

نظام شامل لإدارة مزارع النخيل باستخدام Laravel و Multi-Tenancy

## ✨ المميزات

- 🏢 **Multi-Tenancy**: دعم عدة مزارع في نفس النظام
- 🌴 **إدارة شاملة**: مزارع، قطع، أشجار، عمال، حصاد
- 📊 **تقارير متقدمة**: إحصائيات وتحليلات مفصلة
- 📱 **واجهة عربية**: تصميم RTL متجاوب
- 🔐 **أمان عالي**: نظام أدوار متقدم
- 💾 **قاعدة بيانات محسنة**: جداول منظمة ومفهرسة

## 🚀 متطلبات النظام

- PHP 8.1+
- MySQL 5.7+
- Composer
- Node.js & NPM

## ⚡ التثبيت والتشغيل

### 1. تحميل المشروع
```bash
git clone <repository-url>
cd administration/tenants
```

### 2. تثبيت Dependencies
```bash
composer install
npm install
```

### 3. إعداد قاعدة البيانات
```bash
# إنشاء قاعدة البيانات
mysql -u root -p
CREATE DATABASE tenants_administration;
exit;

# تشغيل Migrations
php artisan migrate:fresh --seed

# إنشاء Tenant تجريبي
php artisan tenants:seed
```

### 4. تشغيل الخادم
```bash
php artisan serve
```

## 👥 المستخدمين التجريبيين

### المستخدم الرئيسي:
- **البريد الإلكتروني**: admin@palms.com
- **كلمة المرور**: password
- **الدور**: مدير عام

### مستخدمي Tenant التجريبي:
- **البريد الإلكتروني**: admin@demo-farm.com
- **كلمة المرور**: password
- **الدور**: مدير

## 📋 الجداول الرئيسية

| الجدول | الوصف |
|--------|--------|
| `farms` | بيانات المزارع |
| `blocks` | القطع الزراعية |
| `palm_trees` | أشجار النخيل |
| `palm_stages` | مراحل نمو النخيل |
| `workers` | العمال |
| `inspections` | الفحوصات |
| `treatments` | المعالجات |
| `harvests` | الحصاد |
| `resources` | المخزون |
| `invoices` | الفواتير |
| `expenses` | المصروفات |

## 🏗️ بنية المشروع

```
app/
├── Console/Commands/     # أوامر Artisan
├── Http/Controllers/     # المتحكمات
│   ├── Tenant/          # متحكمات Tenant
│   └── Auth/            # التحقق من الهوية
├── Models/              # النماذج
├── Providers/           # مزودو الخدمات
└── Helpers/             # الدوال المساعدة

database/
├── migrations/          # ملفات الهجرة
└── seeders/            # ملفات البيانات التجريبية

resources/
├── views/              # ملفات العرض
│   ├── layouts/        # التصاميم الأساسية
│   └── tenant/         # صفحات Tenant
├── lang/               # ملفات الترجمة
└── css/                # ملفات التنسيق

routes/
├── tenant.php          # روتس Tenant
└── web.php            # الروتس الرئيسية
```

## 🔧 الأوامر المتاحة

```bash
# تشغيل الخادم
php artisan serve

# إنشاء Tenant جديد
php artisan tenants:migrate
php artisan tenants:seed

# إنشاء متحكم
php artisan make:controller Tenant/FarmController

# إنشاء نموذج
php artisan make:model Farm

# إنشاء migration
php artisan make:migration create_farms_table

# تشغيل Tests
php artisan test
```

## 🌐 الروتس الرئيسية

### Tenant Routes:
- `GET /` - لوحة التحكم
- `GET /farms` - قائمة المزارع
- `GET /farms/create` - إضافة مزرعة
- `GET /palm-trees` - أشجار النخيل
- `GET /workers` - العمال
- `GET /reports` - التقارير

## 🔐 نظام الأدوار

- **مدير عام** (`superadmin`): صلاحيات كاملة
- **مدير** (`admin`): إدارة المزرعة
- **مشرف** (`manager`): مراقبة العمليات
- **مهندس** (`engineer`): فحص ومعالجة
- **عامل** (`worker`): تسجيل البيانات
- **قراءة فقط** (`readonly`): عرض البيانات

## 📊 المميزات المتقدمة

- ✅ تتبع مراحل نمو النخيل
- ✅ جدولة الفحوصات الدورية
- ✅ إدارة المخزون والموارد
- ✅ تتبع الحصاد والإنتاجية
- ✅ نظام الفواتير والمصروفات
- ✅ تقارير مالية وإنتاجية
- ✅ واجهة عربية كاملة
- ✅ تصميم متجاوب للجوال

## 🐛 استكشاف الأخطاء

### مشاكل شائعة:

1. **خطأ في قاعدة البيانات:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **مشاكل في Multi-Tenancy:**
   ```bash
   php artisan tenants:migrate-fresh
   ```

3. **مشاكل في الصلاحيات:**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

## 📞 الدعم

للدعم الفني أو الاستفسارات:
- البريد الإلكتروني: support@palms.com
- الهاتف: +966 50 123 4567

## 📄 الترخيص

هذا المشروع مرخص تحت رخصة MIT.

---

**تم تطوير هذا النظام بواسطة فريق Windsurf** 🚀
