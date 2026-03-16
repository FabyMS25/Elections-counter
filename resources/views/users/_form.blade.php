@php
    $isEdit         = $isEdit ?? false;
    $userRoleIds    = $userRoleIds ?? [];
    $userDirectPermIds = $userDirectPermIds ?? [];
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="text-center p-3 bg-light rounded border" style="border-style:dashed!important">
            <img id="avatarPreview"
                 src="{{ URL::asset('build/images/users/'.($user?->avatar ?? 'avatar-op-m.png')) }}"
                 alt="avatar" class="rounded-circle mb-2"
                 style="width:80px;height:80px;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.12)">
            <p class="small text-muted mb-2" id="avatarHint">—</p>
            <div class="btn-group btn-group-sm w-100" role="group">
                @php
                    $currentGender = old('gender',
                        ($user?->avatar && str_ends_with(pathinfo($user->avatar, PATHINFO_FILENAME), '-w')) ? 'w' : 'm'
                    );
                @endphp
                <input type="radio" class="btn-check" name="gender" id="gM" value="m"
                       {{ $currentGender === 'm' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="gM"><i class="ri-men-line"></i> M</label>
                <input type="radio" class="btn-check" name="gender" id="gW" value="w"
                       {{ $currentGender === 'w' ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary" for="gW"><i class="ri-women-line"></i> F</label>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <h6 class="fw-semibold mb-3 text-muted text-uppercase small">Datos Personales</h6>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Nombres <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       name="name" value="{{ old('name', $user?->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                       name="last_name" value="{{ old('last_name', $user?->last_name) }}">
                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Carnet de Identidad</label>
                <input type="text" class="form-control @error('id_card') is-invalid @enderror"
                       name="id_card" value="{{ old('id_card', $user?->id_card) }}">
                @error('id_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                       name="phone" value="{{ old('phone', $user?->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
                <label class="form-label">Dirección</label>
                <textarea class="form-control @error('address') is-invalid @enderror"
                          name="address" rows="2">{{ old('address', $user?->address) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <h6 class="fw-semibold mb-3 text-muted text-uppercase small">Credenciales de Acceso</h6>
        <div class="row g-2">
            <div class="col-12">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="emailInput" name="email"
                       value="{{ old('email', $user?->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div id="emailFeedback" class="form-text"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">
                    Contraseña @if(!$isEdit)<span class="text-danger">*</span>@else<small class="text-muted">(dejar vacío para no cambiar)</small>@endif
                </label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="passwordInput" name="password" {{ !$isEdit ? 'required' : '' }}>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control"
                       id="passwordConfirm" name="password_confirmation">
            </div>
            @if($isEdit)
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="isActive"
                           name="is_active" value="1"
                           {{ old('is_active', $user?->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Usuario activo</label>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<hr class="my-3">
<h6 class="fw-semibold mb-3 text-muted text-uppercase small">Roles y Permisos</h6>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 bg-soft-primary">
                <h6 class="card-title mb-0 small">
                    <i class="ri-shield-user-line me-1"></i>Roles
                </h6>
            </div>
            <div class="card-body p-2" style="max-height:420px;overflow-y:auto">
                @foreach($roles as $role)
                @php
                    $isChecked = in_array($role->id, old('roles', $userRoleIds));
                @endphp
                <div class="border rounded p-2 mb-1 role-card {{ $isChecked ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                     style="cursor:pointer"
                     id="roleCard_{{ $role->id }}"
                     onclick="document.getElementById('roleCb_{{ $role->id }}').click()">
                    <div class="d-flex align-items-start gap-2">
                        <input type="checkbox"
                               class="form-check-input role-cb flex-shrink-0 mt-1"
                               id="roleCb_{{ $role->id }}"
                               name="roles[]"
                               value="{{ $role->id }}"
                               data-role-id="{{ $role->id }}"
                               onclick="event.stopPropagation()"
                               {{ $isChecked ? 'checked' : '' }}>
                        <div>
                            <div class="fw-semibold small">{{ $role->display_name ?? $role->name }}</div>
                            @if($role->description)
                            <small class="text-muted">{{ $role->description }}</small>
                            @endif
                            <small class="text-muted d-block mt-1" style="font-size:.65rem">
                                {{ $role->permissions->count() }} permisos
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="alert alert-info py-2 mt-2 small mb-0">
            <i class="ri-information-line me-1"></i>
            Al marcar un rol sus permisos se activan. Al desmarcarlo, los permisos <em>exclusivos</em> de ese rol se desactivan. Los permisos manuales nunca se tocan automáticamente.
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 small"><i class="ri-key-line me-1"></i>Permisos directos adicionales</h6>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-xs btn-soft-success" id="btnSelectAll">Todos</button>
                    <button type="button" class="btn btn-xs btn-soft-danger"  id="btnDeselectAll">Ninguno</button>
                </div>
            </div>
            <div class="card-body p-0" style="max-height:440px;overflow-y:auto;overflow-x:hidden">
                @foreach($permissions as $group => $groupPermissions)
                @php
                    $groupSlug = Str::slug($group);
                    $roleDefaultIds = collect(old('roles', $userRoleIds))
                        ->flatMap(fn($rid) => $rolePermMap[$rid] ?? [])->unique()->values()->toArray();
                @endphp
                <div class="perm-group border-bottom">
                    <div class="d-flex align-items-center px-3 py-2 bg-light sticky-top" style="top:0;z-index:1">
                        <input type="checkbox" class="form-check-input group-cb flex-shrink-0 me-2"
                               id="grp_{{ $groupSlug }}" data-group="{{ $groupSlug }}">
                        <label class="form-check-label fw-semibold small mb-0 flex-grow-1"
                               for="grp_{{ $groupSlug }}">{{ $group }}</label>
                        <span class="badge bg-secondary ms-2" style="font-size:.6rem">{{ count($groupPermissions) }}</span>
                    </div>
                    <div class="px-3 py-2 perm-columns">
                        @foreach($groupPermissions as $perm)
                        @php
                            $isChecked = in_array($perm->id, old('permissions', $userDirectPermIds));
                        @endphp
                        <div class="perm-item rounded px-1 py-1 mb-1 {{ $isChecked ? 'bg-success bg-opacity-10' : '' }}"
                             id="permItem_{{ $perm->id }}"
                             style="break-inside:avoid;display:block">
                            <div class="form-check mb-0">
                                <input type="checkbox"
                                       class="form-check-input perm-cb"
                                       id="perm_{{ $perm->id }}"
                                       name="permissions[]"
                                       value="{{ $perm->id }}"
                                       data-group="{{ $groupSlug }}"
                                       data-state="{{ $isChecked ? 'manual' : 'none' }}"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <label class="form-check-label small" for="perm_{{ $perm->id }}"
                                       title="{{ $perm->description }}">
                                    {{ $perm->display_name ?? $perm->name }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<script id="rolePermData" type="application/json">
@json($rolePermMap)
</script>
<script id="directPermData" type="application/json">
@json($userDirectPermIds)
</script>
<script id="roleInitData" type="application/json">
@json(collect($userRoleIds)->flatMap(fn($rid) => $rolePermMap[$rid] ?? [])->unique()->values()->toArray())
</script>
<script>
window.__isEdit  = {{ ($isEdit ?? false) ? 'true' : 'false' }};
window.__userId  = {{ ($user?->id) ? $user->id : 'null' }};
</script>
