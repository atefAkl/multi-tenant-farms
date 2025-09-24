# Prompt Instructions for Palm Farm SaaS in Laravel + MySQL

> هذا الملف هو النسخة المحدثة والشاملة التي ستُعطى لوكيل AI داخل IDE ليبني مشروع **Palm Farm SaaS** باستخدام Laravel + MySQL. يُركّز التحديث الحالي على: **إدارة المخازن والمالية**، ومتطلبات **الواجهات (UI/UX)** التفصيلية، وإضافات بنيوية مهمة (أمان، توسع، تقارير، CI/CD، إلخ).

---

## نظرة عامة سريعة
- إطار العمل: **Laravel 10+**, PHP 8.1+, MySQL 8+
- واجهة المستخدم: **Bootstrap 5** مع دعم RTL للغة العربية.
- نمط العمل: **SaaS multi-tenant** (tenant_id في الجداول).
- مميزات إضافية: 2FA، تشفير بيانات حسّاسة، Redis Caching، Laravel Queue، تكامل Stripe.

---

## إضافة 1 — إدارة المخازن (Inventory Management)

### الجداول المقترحة (Migrations)
- **resources** (موجود مسبقًا لكن نوسّعه):
  - `id`, `tenant_id`, `sku` (unique per tenant), `name`, `category`, `unit`, `stock_qty`, `reorder_level`, `cost_price`, `sale_price` (اختياري), `location` (rack/section), `barcode` (string), `created_at`, `updated_at`.
- **resource_movements** (سجل الحركات):
  - `id`, `tenant_id`, `resource_id`, `movement_type` ('in','out','adjustment','transfer'), `quantity`, `unit`, `reference` (e.g., treatment_id, harvest_id), `performed_by_worker_id` (FK to workers), `performed_by_user_id` (FK app_users), `notes`, `created_at`.
- **purchase_orders** (أوامر شراء للمخزون):
  - `id`, `tenant_id`, `po_number`, `supplier_name`, `status` (draft,ordered,received,cancelled), `total_amount`, `expected_date`, `created_by`, `created_at`.
- **purchase_order_items**: `po_id`, `resource_id`, `qty`, `unit_price`, `total`.

### وظائف أساسية
- استلام المخزون مع تحديث `resource_movements` و `stock_qty` تلقائيًا.
- سحب عند الاستخدام (treatment/telqeeh/harvest) مرتبط بالحركة.
- إعداد تنبيهات إعادة الطلب عندما يقل `stock_qty` عن `reorder_level`.
- مسح الباركود: تخزين barcode/sku لكل مورد، وAPI لمسح وإجراء عمليات (in/out).

### واجهات API
- `GET /api/resources` (فلترة، بحث بالباركود) 
- `POST /api/resources/scan` (scan payload: barcode, qty, action)
- `POST /api/purchase-orders`، `POST /api/purchase-orders/{id}/receive`

---

## إضافة 2 — الإدارة المالية (Accounting Basics)

### الجداول مقترحة
- **invoices**:
  - `id`, `tenant_id`, `invoice_number`, `customer_name` (or buyer), `status` (draft, issued, paid, cancelled), `issue_date`, `due_date`, `total_amount`, `tax_amount`, `notes`, `created_by`, `created_at`.
- **invoice_items**: `invoice_id`, `description`, `resource_id` (optional), `qty`, `unit_price`, `total`.
- **expenses**:
  - `id`, `tenant_id`, `expense_number`, `category`, `amount`, `paid_to` (vendor), `date`, `notes`, `receipt_url`.
- **payments**:
  - `id`, `tenant_id`, `payable_type` (invoice/expense), `payable_id`, `amount`, `method` (cash,bank,card,stripe), `reference`, `paid_at`, `created_by`.
- **financial_reports** (aggregated): تخزين نتائج التجميع الدوري (optional).

### تكامل مع بوابات دفع
- **Stripe** (مودج): تكامل لإنشاء `payment_intent` ثم حفظ الدفع في جدول `payments`. دعم webhooks لتحديث حالة الفاتورة.
- خيارات أخرى: PayTabs / HyperPay حسب المنطقة.

### مراعاة المحاسبة
- حساب هامشي مبسط: `profit = sum(invoices) - sum(expenses)` (حسب الموسم/البلوك).
- إضافة حقل `cost_center` في الجداول لربط التكاليف حسب بلوك/موسم.

---

## الواجهات (UI/UX) — المواصفات التفصيلية

### أساسيات التصميم
- **Bootstrap 5** مع تخصيص صغير (variables) لعمل الثيم الفاتح/الغامق.
- Use RTL support for Arabic and automatic LTR for English (Laravel localization + html `dir` attribute).
- مسافات داخلية وخارجية: `py-1`-`py-2` (vertical small), `px-3` (normal horizontal). Use small gutters.

### الشريط الجانبي (Sidebar)
- يظهر بشاشة الكمبيوتر فقط (>= 992px). Hide على شاشات التابلت والهواتف (<992px).
- على الشاشات الصغيرة يظهر زر قائمة (hamburger) في الـ topbar يفتح قائمة منسدلة أو offcanvas.
- بنية الشريط: نظام **أكورديون بمستويين**:
  - مستوى أول: أقسام رئيسية (Dashboards, Inventory, Trees, Operations, Finance, Reports, Admin)
  - مستوى ثاني: روابط فرعية لكل قسم (مثلاً Inventory -> Resources, Movements, Purchase Orders)
- كل رابط يجب أن يدعم أيقونة صغيرة (use lucide / fontawesome).
- sidebar state (collapsed/expanded) محفوظ في localStorage لكل مستخدم.

### الرئيسية / مساحات المحتوى
- المنطقة الرئيسية Content area تعرض الصفحة الأساسية (Index view) للكيان.
- كل عمليات الإضافة/التعديل/العرض التفصيلي تظهر **على شكل Modals** (Bootstrap modal) فوق الصفحة الحالية.
- يسمح بتضمين النماذج inline (مثلاً inline expand داخل الـ Index) عند الحاجة: زر "Expand" يمدّد صفًّا لعرض نموذج مدمج.
- use accessible modals (focus trap, aria attributes).

### شاشات ومسارات مهمة
- Index pages: جداول قابلة للفلترة والبحث والفرز، مع زر "Add" يفتح modal.
- Detail views: Modal أو Offcanvas يحتويان على تبويبات (Tabs) لعرض المعلومات (Overview, Inspections, Treatments, Harvests, Attributes).
- Forms: use client validation + server-side validation errors displayed in modal.

### التكامل مع Barcode
- Input field in resource and movement modals to scan barcode (via USB barcode scanner acting as keyboard) or camera-based scanning in mobile web (optional).
- Endpoint `POST /api/resources/scan` يستقبل barcode و action.

### الثيم واللغات
- Light/Dark toggle في topbar يحفظ preference per user (db or localStorage).
- دعم ثنائي اللغة: العربية (RTL) والإنجليزية (LTR). Use `laravel-localization` approach.
- All texts translatable via lang files: `resources/lang/en/*.php`, `resources/lang/ar/*.php`.

---

## بنية البيانات المالية وعمليات المخزون (ملاحظات تقنية)
- عند معالجة `treatment` أو `harvest` إذا استُهلك resource، أنشئ record في `resource_movements` واحتسب تكلفة الاستهلاك لغايات مالية (debit expense).
- ربط المصروفات بالفَصائل: `cost_center` = block_id أو farm_id أو activity_type.
- عند تسجيل بيع/تسليم تمر (invoice)، إنشاء `invoice_items` مرتبطة بـ `harvest_detail` إن أمكن.

---

## الأمان
- **2FA**: استخدم Laravel Fortify أو package مثل `laravel-two-factor-authentication` أو `pragmarx/google2fa-laravel` مع backup codes.
- **تشفير الحقول الحساسة**: استخدم Laravel Encrypted Casts أو حزم مثل `spatie/laravel-encryptable` لحفظ الحقول الحساسة (مثلاً: iqama_id، national_id).
- **Logging & Auditing**: استخدم `spatie/laravel-activitylog` لتتبع تغييرات CRUD وربطها بالمستخدم أو worker.
- **Rate limiting**: API throttling per tenant.
- **Backups & Encryption at rest**: تأكد من تشفير النسخ الاحتياطية والـ S3 buckets.

---

## التوسع والأداء
- **Caching**: Redis cache للـ lookups الثابتة (palm_stages, inventories lists). Use `cache:clear` strategies.
- **Queues**: Laravel Queue (Redis) لتعامل مع معالجة الصور، webhooks، إرسال الإيميلات.
- **CDN**: استضافة الصور/ملفات الحقل في S3 + CloudFront (or regional CDN).
- **DB Optimization**: Indexes على: `tenant_id`, `block_id`, `tree_code`, `barcode`, partition harvests by `season` when حجم البيانات ضخم.

---

## التقارير والتحليلات
- Charts: استخدم Chart.js أو ApexCharts داخل الواجهة لعرض trends (إنتاج/تكلفة/إصابات).
- Export: Generate PDF via `barryvdh/laravel-dompdf` و Excel via `maatwebsite/excel`.
- Advanced: scheduled jobs لتوليد تقارير دورية وإرسالها بالبريد لأصحاب الحسابات.

---

## الاختبارات
- Unit tests للموديلات والثوابت.
- Feature tests للـ API endpoints و tenant isolation.
- E2E tests للواجهات (Cypress) خاصة لعمليات الحقل (scan barcode, create harvest, add inspection).
- Edge cases: تعارض التزامن، إدخال بيانات خاطئة بالكميات، محاولات إدخال موارد سالبة.

---

## Docker / CI / CD
- Dockerfile و docker-compose.yml (mysql, redis, app, worker, nginx).
- GitHub Actions: run tests, build docker image, push to registry, deploy to staging.

---

## تكامل الدفع
- Stripe integration: create invoices -> payment intents -> webhooks to update payments table.
- نمط الحماية: verify webhook signatures, idempotency keys.

---

## التحديثات على الـ Migrations (snippets)
### Resource migration sample (Laravel)
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

### Invoice migration sample
```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id');
    $table->string('invoice_number')->unique();
    $table->string('customer_name')->nullable();
    $table->enum('status',['draft','issued','paid','cancelled'])->default('draft');
    $table->date('issue_date')->nullable();
    $table->date('due_date')->nullable();
    $table->decimal('total_amount', 14, 2)->default(0);
    $table->timestamps();
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
});
```

---

## مخرجات مطلوبة بعد التعديل
- تحديث ملف `Prompt-Laravel-PalmFarm-SaaS-Instructions.md` (هذا الملف تم تحديثه).
- تنفيذ migrations الجديدة واختبارات بسيطة.
- إضافة وحدات واجهة (components) للـ Sidebar, Modals, Theme switcher.

---

## تقييم التطبيق ككل (تقييمي المهني)

### نقاط القوة
- **نطاق واضح ومتكامل**: تصميم متكامل يغطي الزراعة، المخازن، والمالية مما يجعل المنتج قابلاً للاستخدام في الواقع التجاري.
- **SaaS-minded architecture**: row-level tenancy مناسب لإطلاق سريع لمنتجات SaaS.
- **قابلية التوسع والتكامل**: تم التخطيط للتكامل مع Redis, Queues, CDN, Stripe مما يسهل التوسع.

### المخاطر والتحديات
- **عزل آمن للمستأجرين**: row-level tenancy يتطلب حذر في كتابة الاستعلامات وسياسات الوصول؛ قد تحتاج مستقبلاً schema-per-tenant لعملاء كبار.
- **التوافق القانوني للبيانات الحساسة**: تخزين الـ national_id / iqama يحتاج توافق مع قوانين الخصوصية.
- **تعقيد المحاسبة**: في حال رغبت بدقة محاسبية متقدمة (accrual accounting، VAT handling)، سيتطلب النظام محاسبي متخصص أو تكامل مع ERP.

### توصيات للأولوية
1. **أمن البيانات وRLS على مستوى التطبيق**: أضف auditing و2FA فورًا.
2. **MVP سريع**: ركّز أولاً على core: إدارة النخل، الفحوصات، المخزون الأساسي، الحصاد، والتقارير الإنتاجية. ثم أضف الفواتير والدفع.
3. **واجهة مستخدم بسيطة وعملية**: الـ Sidebar + Modals approach ممتازة — ابدأ بها وطور طبقاً لملاحظات المزارعين.
4. **اختبار ميداني**: جرّب الـ MVP مع مزرعة حقيقية أو فريق صغير لجمع feedback.

---

إذا رغبت، سأجهّز الآن:
1. ملف `database/migrations` جاهز بالـ snippets أعلاه (resources, resource_movements, purchase_orders, invoices, expenses, payments). 
2. مجموعه من مكونات الواجهة (Blade components + Bootstrap scaffolding) للـ Sidebar, Modals, Theme switcher، وملف ترجمة ابتدائي (en/ar).

اختر أي خيارين تفضل أبدأ بهما الآن، وأنا أنفّذهم وأحدّث الملف والـ repo snippets.# Prompt Instructions for Palm Farm SaaS in Laravel + MySQL

---

## أهداف المشروع
- بناء تطبيق SaaS لإدارة مزارع النخيل.
- يدعم تعدد المستأجرين (Multi-Tenancy).
- يتضمن إدارة المزارع، البلوكات، أشجار النخيل، مراحل النمو، العمليات الزراعية، الموارد، العمالة، والإنتاج.
- يشمل RBAC (الأدوار والصلاحيات) على مستوى النظام، وإدارة العمال داخل المزرعة.

---

## الخطوات الرئيسية

### 1. Bootstrap
- Laravel new project.
- إعداد قاعدة البيانات MySQL.
- إضافة multi-tenancy structure (Tenant model + middleware).

### 2. Auth & Users
- تركيب Breeze أو Jetstream.
- users table: `tenant_id`, `role` (superadmin, admin, manager, engineer, worker, readonly).
- Authorization عبر Policies و Gates.

### 3. الجداول الأساسية (Migrations)
- tenants
- users
- farms
- blocks
- palm_trees
- palm_stages
- inspections
- treatments
- harvests
- harvest_details
- resources
- resource_movements
- reports
- workers
- warehouses ✅ (مخازن)
- warehouse_items ✅
- invoices ✅
- expenses ✅
- profits ✅

### 4. جدول العمال (Workers)
```sql
workers
- id
- tenant_id (FK)
- farm_id (FK)
- block_id (FK, optional)
- name
- national_id / iqama_id
- phone
- role_in_farm (enum: harvester, irrigator, inspector, pruner, general)
- employment_status (enum: active, inactive, seasonal, terminated)
- hired_at (date)
- ended_at (date, nullable)
- salary (decimal, nullable)
- notes (text, nullable)
```

### 5. إدارة المخازن (Warehouses)
- `warehouses`: تعريف المخزن (الاسم، الموقع، السعة).
- `warehouse_items`: ربط الموارد/الأدوات بالمخزن + الكمية.
- `resource_movements`: حركة الدخول والخروج من المخزن مرتبطة بـ worker_id.

### 6. الإدارة المالية (Finance)
- `invoices`: فواتير بيع التمر (customer, amount, status).
- `expenses`: المصاريف (نوع، مبلغ، تاريخ، worker_id).
- `profits`: أرباح صافية (محسوبة = invoices - expenses).
- تكامل مع **Stripe API** مستقبلاً للتحصيل الإلكتروني.

### 7. ربط العمال بالعمليات
- **inspections** → worker_id
- **treatments** → worker_id
- **harvest_details** → pivot worker_id(s)
- **resource_movements** → worker_id

### 8. واجهة المستخدم (UI/UX)
- **شريط جانبي (Sidebar)**: يحتوي على روابط مهمة، بنظام accordion بمستويين.
- يظهر في شاشات الكمبيوتر فقط.
- يختفي في الهواتف/التابلت → يظهر كقائمة منسدلة (toggle menu).
- **المساحة المخصصة للمحتوى**: تعرض الـ Index.
- باقي الـ actions (إضافة، تعديل، عرض) → تظهر في **modals**.
- يمكن عرض النماذج بشكل ظاهر عند الحاجة.
- **التكامل مع قارئ باركود** (input field auto-focus).
- استخدام **Bootstrap** مع spacing صغير رأسي وطبيعي أفقي.
- دعم **Dark/Light Theme** مع إمكانية التبديل.
- دعم لغتين افتراضيًا: **العربية والإنجليزية** (i18n) مع إمكانية إضافة لغات مستقبلًا.

### 9. ميزات إضافية
- **الأمان**: 2FA، تشفير البيانات، Logging.
- **التوسع**: Redis cache، Laravel Queue، CDN للصور.
- **التقارير**: Charts.js للتحليلات، تصدير PDF/Excel.
- **الاختبارات**: تغطية Edge Cases خاصة بالزراعة.
- **البيئة**: دعم Docker للنشر.

### 10. معايير النجاح
- التطبيق يعمل كـ SaaS multi-tenant.
- كل الجداول والعلاقات موجودة.
- RBAC مفعل.
- إدارة عمال + إدارة مخازن + إدارة مالية.
- إنتاجية العامل وتقارير مالية متكاملة.
- واجهة مستخدم متجاوبة (Responsive) واحترافية.

---

## التنفيذ خطوة بخطوة
1. إنشاء المشروع وضبط DB.
2. عمل Migration + Models للـ Tenants و Users.
3. إضافة باقي migrations (Farms, Blocks, PalmTrees...).
4. إضافة جدول Workers وربطه بالعمليات.
5. إضافة جداول Warehouses و Finance.
6. بناء API endpoints.
7. بناء واجهة المستخدم (Sidebar, Modals, i18n, Themes).
8. إضافة تقارير وتصدير البيانات.
9. اختبارات (Feature + Unit).
10. نشر (Docker, CI/CD).

---

