@extends('layouts.app')

@section('title', 'إدارة المزارع')
@section('page-title', 'إدارة المزارع')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>المزارع</h2>
        <p class="text-muted mb-0">إدارة وتتبع جميع المزارع</p>
    </div>
    <a href="{{ route('tenant.farms.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>إضافة مزرعة جديدة
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="البحث في المزارع..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشطة</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i>بحث
                </button>
                <a href="{{ route('tenant.farms.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-undo me-2"></i>إعادة تعيين
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Farms Table -->
<div class="card">
    <div class="card-body">
        @if($farms->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>اسم المزرعة</th>
                            <th>الموقع</th>
                            <th>المساحة (هكتار)</th>
                            <th>عدد القطع</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($farms as $farm)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                            <i class="fas fa-tractor text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $farm->name }}</h6>
                                            @if($farm->owner)
                                                <small class="text-muted">{{ $farm->owner }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $farm->location }}</td>
                                <td>{{ $farm->size ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $farm->blocks->count() }}</span>
                                </td>
                                <td>
                                    @if($farm->is_active)
                                        <span class="badge bg-success">نشطة</span>
                                    @else
                                        <span class="badge bg-secondary">غير نشطة</span>
                                    @endif
                                </td>
                                <td>{{ $farm->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('tenant.farms.show', $farm) }}" class="dropdown-item">
                                                    <i class="fas fa-eye me-2"></i>عرض
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('tenant.farms.edit', $farm) }}" class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i>تعديل
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('tenant.farms.destroy', $farm) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف هذه المزرعة؟')">
                                                        <i class="fas fa-trash me-2"></i>حذف
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $farms->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-tractor fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد مزارع</h5>
                <p class="text-muted">ابدأ بإضافة مزرعة جديدة</p>
                <a href="{{ route('tenant.farms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>إضافة المزرعة الأولى
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
