# توجيهات شاملة لإنشاء Palm Farm SaaS ERP (Laravel + MySQL) - النسخة المدمجة والمحدثة

> **ملاحظة مهمة**: هذا الملف هو دمج للملفات الثلاثة الأصلية (prompt_laravel_palm_farm_saa_s_instructions.md، prompt_laravel_palm_farm_saa_s_att.md، prompt_laravel_palm_farm_saa_s_att2.md) مع الاقتراحات والتحسينات من الذكاء الاصطناعي. تم تعديل النهج ليكون **database-per-tenant** (قاعدة بيانات منفصلة لكل عميل) بدلاً من row-level tenancy، لزيادة الأمان والأداء. التكامل مع أجهزة الاستشعار والIoT تم تأجيله إلى مرحلة التحسين المستقبلية، مع ذكر خطة عامة له.

## 1. نظرة عامة على المشروع
- **الهدف**: بناء ERP SaaS شامل لإدارة مزارع النخيل، يدعم تعدد العملاء (multi-tenant) مع قاعدة بيانات منفصلة لكل عميل، إدارة المخازن، الإدارة المالية، والواجهات المتقدمة.
- **البيئة**: Laravel 10+، PHP 8.1+، MySQL 8+، Bootstrap 5، Redis للكاش، Queues للمعالجة، Stripe للدفع.
- **النهج الجديد**: Database-per-tenant (استخدام حزم مثل stancl/tenancy لإنشاء قاعدة بيانات منفصلة لكل tenant، مع ترحيل الجداول تلقائياً).
- **التوسعات المستقبلية**: التكامل مع IoT (أجهزة استشعار للتربة/الطقس) سيكون في مرحلة التحسين، مع إضافة جداول مثل `sensor_data` وAPIs للاستقبال.

## 2. المتطلبات والتجهيزات (Prerequisites)
- **الأدوات**: Composer، PHP >=8.1، MySQL Server، Node.js & npm، Git، Docker (اختياري للنشر).
- **البيئة**: .env.example مع متغيرات لـ DB، Stripe، Redis.
- **الحزم المطلوبة**: Laravel Sanctum (للتوثيق)، Breeze (للواجهات)، stancl/tenancy (للـ database-per-tenant)، spatie/laravel-activitylog (للتسجيل)، barryvdh/laravel-dompdf (للـ PDF)، maatwebsite/excel (للـ Excel)، pragmarx/google2fa-laravel (لـ 2FA).
- **الأمان**: تشفير البيانات الحساسة، 2FA، rate limiting، logging.

## 3. الخطوات الرئيسية لإنشاء التطبيق
### خطوة 1: Bootstrap المشروع
- أنشئ المشروع: `composer create-project laravel/laravel palm-farm-saas "10.*"`.
- cd إلى المجلد: `cd palm-farm-saas`.
- توليد المفتاح: `php artisan key:generate`.
- أنشئ .env من .env.example وأعدل قيم DB.
- git init; `git checkout -b feature/bootstrap`.
- commit: `git add . && git commit -m "chore: bootstrap Laravel project"`.
- **تحقق نجاح**: المشروع يعمل مع `php artisan serve`.

### خطوة 2: إعداد Multi-Tenancy (Database-per-Tenant)
- تثبيت الحزمة: `composer require stancl/tenancy`.
- نشر الملفات: `php artisan vendor:publish --provider="Stancl\Tenancy\TenancyServiceProvider"`.
- أنشئ Tenant Model وController:
  ```php
  // app/Models/Tenant.php
  namespace App\Models;
  use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
  class Tenant extends BaseTenant {
      protected $fillable = ['id', 'name', 'domain', 'database'];
  }
  ```
- أضف middleware لتحديد الـ tenant من الدومين (e.g., tenant1.yourapp.com).
- ترحيل الجداول: `php artisan tenants:migrate`.
- **تحقق نجاح**: إنشاء tenant جديد ينشئ قاعدة بيانات منفصلة.

### خطوة 3: إعداد التوثيق والأدوار
- تثبيت Breeze: `composer require laravel/breeze --dev && php artisan breeze:install api`.
- أضف أدوار في users table: `php artisan make:migration add_role_to_users_table`.
- استخدم Policies للـ RBAC: `php artisan make:policy PalmTreePolicy`.
- **تحقق نجاح**: تسجيل دخول وتوليد token عبر Sanctum.

### خطوة 4: إنشاء الجداول (Migrations) - مع database-per-tenant
- أنشئ migrations للجداول الرئيسية (سيتم ترحيلها لكل tenant تلقائياً):
  - **tenants**: id, name, domain, database.
  - **users**: id, tenant_id (nullable for superadmins), role (superadmin, admin, manager, engineer, worker, readonly), name, email, password.
  - **farms**: id, tenant_id, name, location, owner, size.
  - **blocks**: id, tenant_id, farm_id, name, area, soil_type, irrigation_type.
  - **palm_trees**: id, tenant_id, block_id, tree_code, row_no, col_no, gender, stage_id, variety, planting_date, status.
  - **palm_stages**: id, tenant_id, name, age_from_months, age_to_months.
  - **inspections**: id, tenant_id, tree_id, worker_id, notes, date.
  - **treatments**: id, tenant_id, tree_id, worker_id, type, date.
  - **harvests**: id, tenant_id, tree_id, season, date.
  - **harvest_details**: id, tenant_id, harvest_id, worker_id, qty, unit_price.
  - **workers**: id, tenant_id, farm_id, block_id, name, national_id (مشفر), phone, role_in_farm, employment_status, salary.
  - **resources**: id, tenant_id, sku, name, category, unit, stock_qty, cost_price, barcode, location.
  - **resource_movements**: id, tenant_id, resource_id, movement_type, qty, worker_id, notes.
  - **purchase_orders**: id, tenant_id, po_number, supplier, status, total_amount.
  - **invoices**: id, tenant_id, invoice_number, customer_name, status, total_amount, issue_date.
  - **expenses**: id, tenant_id, category, amount, paid_to, date, notes.
  - **payments**: id, tenant_id, payable_type, payable_id, amount, method, paid_at.
- نموذج migration لـ resources:
  ```php
  Schema::create('resources', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('tenant_id');
      $table->string('sku')->nullable();
      $table->string('name');
      $table->string('category')->nullable();
      $table->string('unit')->nullable();
      $table->decimal('stock_qty', 12, 3)->default(0);
      $table->decimal('cost_price', 12, 2)->nullable();
      $table->string('barcode')->nullable();
      $table->string('location')->nullable();
      $table->timestamps();
      $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
  });
  ```
- ترحيل: `php artisan migrate` (للـ main DB)، ثم `php artisan tenants:migrate` للـ tenants.
- **تحقق نجاح**: جميع الجداول موجودة ومرتبطة.

### خطوة 5: إنشاء النماذج (Models) مع العلاقات
- أنشئ Models لكل جدول مع relations:
  - Tenant: hasMany(Users, Farms).
  - User: belongsTo(Tenant).
  - PalmTree: belongsTo(Block, Stage), hasMany(Inspections, Treatments, Harvests).
  - Worker: belongsTo(Tenant, Farm), hasMany(Inspections, Treatments).
  - Resource: hasMany(ResourceMovements).
- استخدم TenantScope لفلترة البيانات تلقائياً.
- **تحقق نجاح**: اختبار العلاقات مع unit tests.

### خطوة 6: بناء APIs
- أنشئ Controllers: PalmTreeController, InspectionController, etc.
- Routes في routes/api.php مع middleware: auth:sanctum, tenancy.
- Endpoints رئيسية: GET/POST /api/palm-trees, /api/inspections, /api/resources, /api/invoices.
- **تحقق نجاح**: اختبار APIs مع Postman.

### خطوة 7: إضافة إدارة المخازن والمالية
- ربط الموارد بالحركات: عند treatment، حدث stock_qty.
- Stripe: تكامل للدفع عبر webhooks.
- **تحقق نجاح**: إنشاء فاتورة ودفع.

### خطوة 8: بناء الواجهات (UI/UX)
- استخدم Bootstrap 5 مع Sidebar (accordion), Modals, Themes (Light/Dark), RTL.
- أنشئ components: Sidebar, ResourceModal.
- دعم اللغات: ar/en.
- **تحقق نجاح**: واجهة متجاوبة.

### خطوة 9: الاختبارات والأمان
- Unit/Feature tests: `php artisan test`.
- أضف 2FA، تشفير، logging.
- **تحقق نجاح**: اختبارات خضراء.

### خطوة 10: النشر والتوسع
- Docker: docker-compose.yml مع MySQL، Redis.
- CI/CD: GitHub Actions.
- **تحقق نجاح**: النشر على خادم.

## 4. التحسينات المستقبلية (بدون IoT حالياً)
- إضافة AI للتنبؤ بالإنتاج.
- تطبيق موبايل للعمال.
- تكامل مع ERP خارجي للمحاسبة.

## 5. خطة للتكامل مع IoT (في مرحلة التحسين)
- أضف جداول: `sensor_data` (tenant_id, device_id, temperature, humidity, timestamp).
- APIs: POST /api/sensor-data (من الأجهزة).
- دمج مع AI للتنبؤ.

## 6. معايير النجاح
- SaaS مع DB منفصلة.
- ERP كامل: زراعة + مخازن + مالية + UI.
- أمان وأداء عالي.

هذا الملف جاهز للتنفيذ! إذا كنت تريد تعديلات أو إنشاء ملفات محددة، أخبرني.
