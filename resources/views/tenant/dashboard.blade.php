@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="mb-4">لوحة التحكم</h2>
        <p class="text-muted">مرحباً بك في نظام إدارة مزارع النخيل</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-tractor text-success fa-2x"></i>
                    </div>
                </div>
                <h4 class="text-success mb-1">{{ $stats['total_farms'] }}</h4>
                <p class="text-muted mb-0">إجمالي المزارع</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-cubes text-info fa-2x"></i>
                    </div>
                </div>
                <h4 class="text-info mb-1">{{ $stats['total_blocks'] }}</h4>
                <p class="text-muted mb-0">إجمالي القطع</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-tree text-warning fa-2x"></i>
                    </div>
                </div>
                <h4 class="text-warning mb-1">{{ $stats['total_palm_trees'] }}</h4>
                <p class="text-muted mb-0">أشجار النخيل</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-users text-primary fa-2x"></i>
                    </div>
                </div>
                <h4 class="text-primary mb-1">{{ $stats['total_workers'] }}</h4>
                <p class="text-muted mb-0">إجمالي العمال</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-search text-primary me-2"></i>
                    آخر الفحوصات
                </h5>
            </div>
            <div class="card-body">
                @if($stats['recent_inspections']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($stats['recent_inspections'] as $inspection)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <div class="fw-bold">شجرة {{ $inspection->palmTree->tree_code }}</div>
                                    <small class="text-muted">{{ $inspection->palmTree->block->name }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $inspection->health_status == 'excellent' ? 'success' : ($inspection->health_status == 'good' ? 'info' : ($inspection->health_status == 'fair' ? 'warning' : 'danger')) }}">
                                        {{ $inspection->health_status == 'excellent' ? 'ممتاز' : ($inspection->health_status == 'good' ? 'جيد' : ($inspection->health_status == 'fair' ? 'مقبول' : 'سيء')) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $inspection->inspection_date->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0">لا توجد فحوصات حديثة</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-wheat-awn text-success me-2"></i>
                    آخر الحصادات
                </h5>
            </div>
            <div class="card-body">
                @if($stats['recent_harvests']->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($stats['recent_harvests'] as $harvest)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <div class="fw-bold">شجرة {{ $harvest->palmTree->tree_code }}</div>
                                    <small class="text-muted">{{ $harvest->palmTree->block->name }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">{{ $harvest->total_quantity }} كجم</div>
                                    <small class="text-muted">{{ $harvest->harvest_date->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center mb-0">لا توجد حصادات حديثة</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    إجراءات سريعة
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('tenant.farms.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus fa-fw"></i><br>
                            إضافة مزرعة
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('tenant.palm-trees.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-tree fa-fw"></i><br>
                            إضافة شجرة نخيل
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('tenant.inspections.create') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-search fa-fw"></i><br>
                            فحص شجرة
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('tenant.harvests.create') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-wheat-awn fa-fw"></i><br>
                            تسجيل حصاد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
