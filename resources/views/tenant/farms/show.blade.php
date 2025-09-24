@extends('layouts.app')

@section('title', $farm->name)
@section('page-title', $farm->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>{{ $farm->name }}</h2>
                <p class="text-muted mb-0">{{ $farm->location }}</p>
            </div>
            <div>
                @if($farm->is_active)
                    <span class="badge bg-success fs-6">نشطة</span>
                @else
                    <span class="badge bg-secondary fs-6">غير نشطة</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Farm Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    تفاصيل المزرعة
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-user text-muted me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">المالك</h6>
                                <p class="mb-0">{{ $farm->owner ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-ruler text-muted me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">المساحة</h6>
                                <p class="mb-0">{{ $farm->size ? $farm->size . ' هكتار' : 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-map-marker-alt text-muted me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">الإحداثيات</h6>
                                <p class="mb-0">{{ $farm->coordinates ?? 'غير محدد' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex">
                            <i class="fas fa-calendar text-muted me-3 mt-1"></i>
                            <div>
                                <h6 class="mb-1">تاريخ الإنشاء</h6>
                                <p class="mb-0">{{ $farm->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($farm->description)
                        <div class="col-12">
                            <div class="d-flex">
                                <i class="fas fa-align-left text-muted me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">الوصف</h6>
                                    <p class="mb-0">{{ $farm->description }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Blocks -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cubes text-success me-2"></i>
                    القطع الزراعية
                </h5>
                <a href="{{ route('tenant.blocks.create', ['farm' => $farm->id]) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i>إضافة قطعة
                </a>
            </div>
            <div class="card-body">
                @if($farm->blocks->count() > 0)
                    <div class="row g-3">
                        @foreach($farm->blocks as $block)
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $block->name }}</h6>
                                                <small class="text-muted">{{ $block->area ?? 'غير محدد' }}</small>
                                            </div>
                                            <div class="text-end">
                                                @if($block->is_active)
                                                    <span class="badge bg-success">نشطة</span>
                                                @else
                                                    <span class="badge bg-secondary">غير نشطة</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-tree me-1"></i>
                                                {{ $block->palmTrees->count() }} شجرة نخيل
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-cubes fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">لا توجد قطع زراعية</h6>
                        <p class="text-muted">ابدأ بإضافة قطعة زراعية للمزرعة</p>
                        <a href="{{ route('tenant.blocks.create', ['farm' => $farm->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>إضافة القطعة الأولى
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar text-info me-2"></i>
                    الإحصائيات
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>عدد القطع:</span>
                    <span class="badge bg-primary">{{ $farm->blocks->count() }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>أشجار النخيل:</span>
                    <span class="badge bg-success">{{ $farm->blocks->sum(fn($block) => $block->palmTrees->count()) }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <span>العمال:</span>
                    <span class="badge bg-warning">{{ $farm->workers->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cog text-secondary me-2"></i>
                    الإجراءات
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('tenant.farms.edit', $farm) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>تعديل المزرعة
                    </a>

                    <a href="{{ route('tenant.workers.create', ['farm' => $farm->id]) }}" class="btn btn-outline-success">
                        <i class="fas fa-user-plus me-2"></i>إضافة عامل
                    </a>

                    <hr>

                    <form method="POST" action="{{ route('tenant.farms.destroy', $farm) }}" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذه المزرعة؟')">
                            <i class="fas fa-trash me-2"></i>حذف المزرعة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
