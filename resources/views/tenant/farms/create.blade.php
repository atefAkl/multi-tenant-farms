@extends('layouts.app')

@section('title', 'إضافة مزرعة جديدة')
@section('page-title', 'إضافة مزرعة جديدة')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus text-primary me-2"></i>
                    إضافة مزرعة جديدة
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tenant.farms.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">اسم المزرعة *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="owner" class="form-label">المالك</label>
                            <input type="text" class="form-control @error('owner') is-invalid @enderror"
                                   id="owner" name="owner" value="{{ old('owner') }}">
                            @error('owner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="location" class="form-label">الموقع *</label>
                            <textarea class="form-control @error('location') is-invalid @enderror"
                                      id="location" name="location" rows="3" required>{{ old('location') }}</textarea>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="size" class="form-label">المساحة (هكتار)</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('size') is-invalid @enderror"
                                   id="size" name="size" value="{{ old('size') }}">
                            @error('size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="coordinates" class="form-label">الإحداثيات (GPS)</label>
                            <input type="text" class="form-control @error('coordinates') is-invalid @enderror"
                                   id="coordinates" name="coordinates" value="{{ old('coordinates') }}"
                                   placeholder="مثال: 24.7136, 46.6753">
                            @error('coordinates')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    المزرعة نشطة
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('tenant.farms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>العودة للقائمة
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>حفظ المزرعة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
