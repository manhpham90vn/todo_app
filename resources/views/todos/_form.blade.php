@csrf
<div class="mb-3">
    <label class="form-label">Tiêu đề</label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $todo->title ?? '') }}" required>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Mô tả</label>
    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $todo->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label">Độ ưu tiên</label>
        <select name="priority" class="form-select @error('priority') is-invalid @enderror">
            @foreach (['high'=>'Cao','medium'=>'Trung bình','low'=>'Thấp'] as $val => $label)
                <option value="{{ $val }}" @selected(old('priority', $todo->priority ?? 'medium') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_complete" value="1"
                @checked(old('is_complete', $todo->completed_at ?? false))>
            <label class="form-check-label">Đã hoàn thành</label>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary">
    {{ $submitLabel ?? 'Lưu' }}
</button>
<a href="{{ route('todos.index') }}" class="btn btn-outline-secondary">Hủy</a>
