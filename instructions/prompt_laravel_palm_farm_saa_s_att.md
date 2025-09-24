# Prompt Instructions for Palm Farm SaaS in Laravel + MySQL

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
- workers ✅ (مُضاف الآن)

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

### 5. ربط العمال بالعمليات
- **inspections** → worker_id
- **treatments** → worker_id
- **harvest_details** → pivot worker_id(s) (many-to-many)
- **resource_movements** → worker_id

### 6. سيناريو الأدوار داخل المزرعة
- Harvester (الخراف): تسجيل مراحل جني التمر بالكيلو.
- Inspector (المراقب): إضافة تقارير صحية للنخيل.
- Irrigator (الري): تسجيل أوقات وكميات الري.
- Pruner (التقليم): تسجيل عمليات تقليم السعف.
- General Worker: أي مهام أخرى.

### 7. صلاحيات العمال
- العمال ليسوا بالضرورة Users (حساب دخول).
- يمكن ربط Worker بـ User إذا احتاج العامل دخول التطبيق (موبايل أو ويب).

### 8. Features إضافية
- السرعة والأمان (Caching, Queues, hashed passwords, Sanctum tokens).
- التقارير (PDF/Excel exports).
- دعم اللغة العربية والإنجليزية.

### 9. معايير النجاح
- التطبيق يعمل كـ SaaS multi-tenant.
- كل الجداول والعلاقات موجودة.
- RBAC مفعل.
- العمال مرتبطين بالعمليات الزراعية.
- إنتاجية العامل قابلة للتقارير.

---

## التنفيذ خطوة بخطوة
1. إنشاء المشروع وضبط DB.
2. عمل Migration + Models للـ Tenants و Users.
3. إضافة باقي migrations (Farms, Blocks, PalmTrees...).
4. إضافة جدول Workers وربطه بالعمليات.
5. بناء API endpoints.
6. إضافة الواجهة (Blade/React/Vue).
7. إضافة تقارير وتصدير البيانات.
8. اختبارات (Feature + Unit).
9. نشر (Docker, CI/CD).

---

