# توجيهات تشغيلية (Prompt) — مشروع: Palm Farm SaaS (Laravel + MySQL)

> الهدف: تنفيذ كل خطوات إنشاء مشروع Laravel لإدارة مزارع النخيل بنظام SaaS (multi-tenant row-level tenancy) باستخدام MySQL، خطوة بخطوة، من الـ bootstrap حتى قابلية التشغيل والاختبارات والنشر التجريبي.

---

## 1. ملاحظات عامة وقيود

1. **البيئة المقصودة**: Laravel 10.0+، PHP >= 8.1، MySQL 8.x، Composer، Node.js 16+.
2. **أسلوب الـ tenancy**: "Row-level tenancy" بمعنى وجود حقل `tenant_id` في الجداول الرئيسية. هذا الأسلوب مطلوب لأننا سنشغّل التطبيق كسحابة SaaS متعددة العملاء.
3. **المخارج**: كل خطوة يجب أن تُختم بتقرير حالة في ملف `build/logs/task_status.md` (انظر القسم 11).
4. **أسماء الفروع وCommits**: اتبع سياسة الفروع `feature/<short-desc>` لكل مجموعة تغييرات؛ كل خطوة رئيسية يجب أن تُوَثق بـ commit واضح (شاهد القسم 10).
5. **لا تحفظ أسرار في الكود**: تستخدم متغيرات بيئة `.env` فقط. لا تُسجل كلمات السر أو مفاتيح الوصول داخل الـ repo.
6. **إعادة المحاولة**: عند فشل خطوة تلقائيًا حاول تنفيذها مرتين أكثر قبل تسجيل فشل نهائي.

---

## 2. prerequisites — ما يجب أن يتوافر قبل التنفيذ

- Composer مثبت
- PHP >=8.1
- MySQL server (متاح ببيانات اتصال) أو Docker Desktop
- Git
- Node.js & npm
- صلاحيات إنشاء وتشغيل الحاويات (لو اخترت Sail/Docker)

---

## 3. ملف .env.example المطلوب (محتوى افتراضي — استبدل القيم لاحقًا)

```
.env
APP_NAME="palm-farm"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=palm_farm
DB_USERNAME=root
DB_PASSWORD=secret

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=s3
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

# S3 placeholders
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=

SANCTUM_STATEFUL_DOMAINS=localhost

# For CI/Deploy add secrets separately

```

---

## 4. المهام الرئيسية (التسلسل الذي ينفذه الوكيل الآلي)

**ملاحظة:** كل بند أدناه يتضمن أمر/قالب ملف/تحقق نجاح. الوكيل يجب أن ينفذ بالترتيب ويحفظ نتائج التنفيذ في `build/logs/task_status.md`.

### خطوة 0: تهيئة المشروع

- أمر: `composer create-project laravel/laravel palm-farm-saas "^10.0"` أو استخدم `laravel new palm-farm-saas` إن متاح.
- cd إلى المجلد: `cd palm-farm-saas`
- generate key: `php artisan key:generate`
- create `.env` من `.env.example` مع قيم اتصال DB التجريبية.
- git init; `git checkout -b feature/bootstrap-project`.
- Commit: `git add . && git commit -m "chore: bootstrap laravel project"`.

**تحقق نجاح:** وجود `artisan` + ملف `.env` + commit.

---

### خطوة 1: إعداد Docker Compose (اختياري لكن موصى به)

**إنشاء ملف** `docker-compose.yml` في جذر المشروع (مقترح مبسط لدعم MySQL وphp-fpm وnginx) أو استخدم Laravel Sail:

- الأمر البديل: `composer require laravel/sail --dev` ثم `php artisan sail:install --with=mysql`.

**تحقق نجاح:** قدرة تشغيل MySQL محليًا وكون `DB` متاحة.

---

### خطوة 2: إعداد Authentication و API

- نستخدم Sanctum للتوثيق API: `composer require laravel/sanctum` ثم `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"` ثم `php artisan migrate`.
- أو استخدم Breeze + API scaffolding: `composer require laravel/breeze --dev && php artisan breeze:install api` ثم `npm install && npm run build`.

**تحقق نجاح:** تسجيل مستخدم API وتوليد توكن عبر Sanctum بنجاح.

---

### خطوة 3: Migrations — أنشئ جميع migrations المذكورة بالاسم والمحتوى أدناه

**قاعدة التصميم**: استخدم `id` (bigIncrements) في Laravel أو `uuid` حسب تفضيلك. سنعطي أمثلة باستخدام `id` (bigIncrements) مع `tenant_id` كـ `unsignedBigInteger()`.

**ملف 1 — create_tenants_table.php**

```
php
// database/migrations/2025_09_22_000001_create_tenants_table.php
public function up()
{
    Schema::create('tenants', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('plan')->nullable();
        $table->string('contact_email')->nullable();
        $table->timestamps();
    });
}
```

**ملف 2 — users (تعديل) — add tenant_id & role**

- تعديل migration `create_users_table` ليشمل `tenant_id` و`role` (enum: superadmin, admin, manager, engineer, worker, readonly).

**ملف 3 — farms**

```php
Schema::create('farms', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id');
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
    $table->string('name');
    $table->text('location')->nullable();
    $table->string('owner')->nullable();
    $table->decimal('size', 10, 2)->nullable();
    $table->timestamps();
});
```

**ملف 4 — blocks**

```php
Schema::create('blocks', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id');
    $table->unsignedBigInteger('farm_id');
    $table->string('name');
    $table->decimal('area',10,2)->nullable();
    $table->string('soil_type')->nullable();
    $table->string('irrigation_type')->nullable();
    $table->timestamps();
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
    $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
});
```

**ملف 5 — palm_stages**

```php
Schema::create('palm_stages', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id');
    $table->string('name');
    $table->integer('age_from_months')->nullable();
    $table->integer('age_to_months')->nullable();
    $table->timestamps();
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
});
```

**ملف 6 — palm_trees**

```php
Schema::create('palm_trees', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id');
    $table->unsignedBigInteger('block_id');
    $table->string('tree_code');
    $table->integer('row_no')->nullable();
    $table->integer('col_no')->nullable();
    $table->decimal('x_coord',10,6)->nullable();
    $table->decimal('y_coord',10,6)->nullable();
    $table->enum('gender',['M','F']);
    $table->unsignedBigInteger('stage_id')->nullable();
    $table->string('variety')->nullable();
    $table->date('planting_date')->nullable();
    $table->enum('status',['healthy','sick','dead','needs_maintenance'])->default('healthy');
    $table->decimal('height_m',8,2)->nullable();
    $table->integer('num_bunches')->nullable();
    $table->integer('leaf_count')->nullable();
    $table->timestamp('last_inspection_date')->nullable();
    $table->timestamps();
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
    $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
    $table->foreign('stage_id')->references('id')->on('palm_stages')->onDelete('set null');
    $table->unique(['block_id','row_no','col_no']);
});
```

**ملف 7 — palm_attributes**

```php
Schema::create('palm_attributes', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('tenant_id');
    $table->unsignedBigInteger('tree_id');
    $table->timestamp('recorded_at')->useCurrent();
    $table->decimal('height_m',8,2)->nullable();
    $table->integer('num_bunches')->nullable();
    $table->integer('leaf_count')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
    $table->foreign('tree_id')->references('id')->on('palm_trees')->onDelete('cascade');
});
```

**ملف 8 — inspection, treatment, treatment_resource, resource, resource_movement, harvest_detail, worker**

(اكتب أي ملف migration آخر مشابهًا وفق التصميم المتفق عليه — المعلّقات مطلوبة في الخطوات العملية أدناه.)

**تحقق نجاح:** تنفيذ `php artisan migrate` بنجاح (أو مع Docker: `sail artisan migrate`).

---

### خطوة 4: Models — إضف Models لكل جدول مع العلاقات التالية (نماذج كاملة)

**مثال: app/Models/PalmTree.php**

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PalmTree extends Model
{
    protected $fillable = [
        'tenant_id','block_id','tree_code','row_no','col_no','x_coord','y_coord',
        'gender','stage_id','variety','planting_date','status','height_m','num_bunches','leaf_count','last_inspection_date'
    ];

    public function block(){ return $this->belongsTo(Block::class); }
    public function stage(){ return $this->belongsTo(PalmStage::class); }
    public function attributes(){ return $this->hasMany(PalmAttribute::class); }
    public function inspections(){ return $this->hasMany(Inspection::class); }
    public function harvests(){ return $this->hasMany(HarvestDetail::class); }
}
```

**مطلوب:** إنشاؤها جميعًا: Tenant, User (توسع existing), Farm, Block, PalmStage, PalmTree, PalmAttribute, Inspection, Treatment, TreatmentResource, Resource, ResourceMovement, HarvestDetail, Worker.

**تحقق نجاح:** كل Model تتحقق علاقاتها عن طريق unit test بسيط (موجود في القسم 9).

---

### خطوة 5: Tenant scoping — إضافة Global Scope أو Trait

**طريقة مقترحة**: أنشئ Trait `App\Scopes\TenantScope` أو Trait `TenantScoped` تُستخدم في جميع الموديلات (عدا Tenant نفسه وAdmin users) لتطبيق `where('tenant_id', current_tenant_id())`.

**ملاحظات للتنفيذ الآلي**:

- أنشئ ملف `app/Traits/TenantTrait.php` مع دالة static bootTenantTrait() التي تضيف Global Scope.
- أنشئ Middleware `SetTenant` الذي يقرأ `X-Tenant-ID` من header أو يستخرج tenant من `subdomain` أو `Authorization` ويخزن في `app()->instance('tenant', $tenantModel)`.
- دالة مساعدة helper `current_tenant_id()` ترجع `app('tenant')->id` أو `auth()->user()->tenant_id` حسب السياق.

**تحقق نجاح:** عند إنشاء Tenant جديد ثم إنشاء Farm مرتبط به، قراءة `PalmTree::all()` بعد تسجيل الدخول كمستخدم نفس tenant يجب ألا تُظهر بيانات tenants أخرى.

---

### خطوة 6: API routes & Controllers

- مجموعة واجهات API تحت `routes/api.php` مع group middleware: `auth:sanctum`, `settenant`.

**نماذج Endpoints رئيسية**:

- `GET /api/palm-trees` — قائمة بالنخلات (مصحوبة بفلتر block/fase/variety)
- `POST /api/palm-trees` — إنشاء نخلة
- `GET /api/palm-trees/{id}` — تفاصيل نخلة
- `PUT /api/palm-trees/{id}` — تحديث
- `POST /api/palm-trees/{id}/inspections` — اضافة فحص
- `POST /api/palm-trees/{id}/treatments` — اضافة علاج
- `POST /api/palm-trees/{id}/harvests` — اضافة صف خراف
- `GET /api/reports/production?season=2025&block=...` — تقارير

**تحقق نجاح:** استطلاع endpoints عبر `php artisan test` أو `curl` مع توكن صالح.

---

### خطوة 7: Sync / Offline Mode

- صمم endpoint `POST /api/sync/upload` لقبول دفعات JSON من العميل (client-side) تحتوي على تغيّرات CRUD مع `client_uuid`, `created_at` و`updated_at` ليتم مطابقة السجلات على الخادم.
- التزامن يجب أن يتعامل مع تعارضات (conflict resolution) باستخدام مبدأ `last-writer-wins` كخيار افتراضي.

**تحقق نجاح:** محاكاة إرسال دفعة JSON من عميل غير متصل ثم رفعها وتحقق إدخال البيانات في DB.

---

### خطوة 8: Authorization & RBAC

- استخدم gates/policies في Laravel للتحكم. مثال: `PalmTreePolicy` يمنع تعديل النخلة لأي user ليس له نفس `tenant_id` أو ليس لديه دور admin/manager.

**تحقق نجاح:** اختبار policy في PHPUnit.

---

### خطوة 9: الاختبارات (Unit and Feature)

- إعداد `phpunit.xml` وكتابة اختبارات أساسية:
  - Model relationships smoke tests
  - API endpoint tests: create palm_tree, add inspection, add harvest, verify tenant isolation
  - policy tests

**أوامر**: `php artisan test`.

---

### خطوة 10: Git workflow

- الفروع: `feature/bootstrap`, `feature/migrations-models`, `feature/tenant-scope`, `feature/api-crud`, `feature/tests-ci`.
- لكل فرع: التزامات صغيرة مع رسائل واضحة: `feat: add palm_tree migration`, `feat: palm_tree model & relations`.
- بعد الانتهاء من كل فانكشنالتي، افتح PR للمراجعة.

---

### خطوة 11: سجلات وملف تتبع التقدم (الواجب على الوكيل الآلي)

- أنشئ مجلد: `build/logs/`.
- في نهاية كل مهمة أساسية (بعد migrations, after models, after APIs, after tests) أضف أو حدث `build/logs/task_status.md` بمحتوى:

```md
# Task Status Log

- step: create_project
  status: success
  timestamp: 2025-09-22T12:00:00Z
  details: "Laravel project created"

- step: migrations
  status: failed|success
  timestamp: ...
  details: "output log or error"

```

- إذا فشلت خطوة: أضف `build/logs/errors/<step_name>.log` يحتوي على كامل stacktrace و `git diff` و `php artisan migrate --verbose` output.

---

### خطوة 12: seeds — إضافة بيانات تجريبية

- أنشئ seeders لملء tenant demo مع مستخدم admin، مزرعة، بلوك، 10 نخلات، وبعض الفحوصات والحصاد.
- مثال: `php artisan db:seed --class=DemoTenantSeeder`.

---

### خطوة 13: CI / CD (مثال GitHub Actions)

- ملف `.github/workflows/ci.yml` يتضمن:
  - checkout
  - setup php & composer
  - install composer deps
  - run migrations (sqlite in memory for speed) or MySQL service
  - run phpunit

---

### خطوة 14: التنفيذ على الخادم التجريبي

- مقترح: نشر على Heroku / DigitalOcean App Platform / Render أو Kubernetes.
- استخدم Dockerfile و `docker-compose.prod.yml` أو إعداد CI يَدفع صورة Docker للحاوية.

---

### خطوة 15: ملف التوثيق الداخلي للمطورين

- أنشئ `docs/README_DEV.md` يوضح:
  - كيفية تشغيل المشروع محليًا (docker-compose up)
  - قواعد الأسماء، الـ env vars، endpoints الأساسية
  - كيفية تشغيل الوظائف الخلفية والـ queues

---

## 5. نقاط عملية إضافية للبوت/الوكيل الآلي

1. **التعامل مع الأخطاء**: عند فشل الأمر، سَجّل، أعد المحاولة مرتين، ثم أنشئ issue file `build/logs/errors/<step>.md` وواصل لباقي الخطوات.
2. **الوقت المسموح لكل خطوة**: 10 دقائق تنفيذية محلية لكل مهمة إن أمكن (إلا المهام الكبيرة كـ migrations الضخمة).
3. **مخرجات يجب أن تُرجَع إلى المستخدم (ملف)**:
   - `build/logs/task_status.md` (تقرير مرحلي)
   - `build/artifacts/` (ملف SQL dump بسيط `schema.sql` بعد مبدئ `migrate --schema`)
4. **تنسيق commit**: استخدم Conventional Commits: `feat:`, `fix:`, `chore:`.

---

## 6. Acceptance Criteria — متى نعتبر الخطوة مكتملة

- جميع الموديلات والمهاجرات قابلة للترحيل (`php artisan migrate`) بنجاح.
- يوجد Seeder واحد يملأ بيانات تجريبية قابلة للاختبار.
- endpoints API الأساسية تعمل (CRUD على PalmTree + Inspections + Harvests + Resources).
- سياسات التقييد بالـ tenant تعمل (لا يظهر بيانات الآخرين).
- اختبارات PHPUnit الأساسية تمر (green).
- ملف `build/logs/task_status.md` محدث لكل خطوة.

---

## 7. Template لرسالة الـ commit لكل خطوة

- `git add -A && git commit -m "feat(migrations): add palm_trees and palm_attributes migrations"`

---

## 8. ملفات جاهزة (snippets) التي يجب كتابتها حرفيًا

> في حال كان الوكيل قادراً على إنشاء ملفات مباشرة، اعمل create بالمسارات والـ contents التالية: `database/migrations/...` و `app/Models/...` كما في القوالب أعلاه.

---

## 9. تعليمات خاصة بالطرف البشري (المستخدم)

- بعد وضع هذا الملف داخل IDE، شغّل نموذج AI (Agent) واختر أمر "run" أو "execute".
- اسمح للوكيل بالعمل دون مقاطعة حتى يُنجز المهمة الأساسية الأولى (bootstrap + migrations + models + seed).
- بعد انتهاء الوكيل من المرحلة الأولى، راجع `build/logs/task_status.md` وقرّ بنفسك النتائج.

---

## 10. خاتمة

هذا المستند هو مرجع تفصيلي ليُعطى لأي نموذج AI داخل IDE. يمكن تعديل القيم والـ paths حسب الحاجة. عند الانتهاء من كل مجموعة مهام، يرفع الوكيل ملف `build/logs/task_status.md` إلى الـ repo وينشئ Pull Request على الفرع المقابل.




