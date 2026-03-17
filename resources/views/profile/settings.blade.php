@extends('layouts.master')
@section('title') Configuración de Perfil @endsection

@section('css')
<style>
.avatar-upload{width:140px;height:140px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,.15)}
.avatar-edit{position:relative;display:inline-block}
.avatar-edit .avatar-edit-btn{position:absolute;bottom:0;right:0;background:#0ab39c;border:2px solid #fff;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;transition:all .2s}
.avatar-edit .avatar-edit-btn:hover{background:#099885;transform:scale(1.1)}
.avatar-dropdown-container {
    position: relative;
    width: 100%;
    margin-top: 15px;
}
.avatar-dropdown-btn {
    width: 100%;
    padding: 8px 12px;
    background: #f8f9fa;
    border: 1px solid #e2e5e8;
    border-radius: 6px;
    text-align: left;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.2s;
}
.avatar-dropdown-btn:hover {
    background: #e9ecef;
    border-color: #0ab39c;
}
.avatar-dropdown-btn .selected-avatar-preview {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}
.avatar-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e5e8;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    margin-top: 4px;
    padding: 8px;
}
.avatar-dropdown-menu.show {
    display: block;
}
.avatar-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s;
    margin-bottom: 4px;
}
.avatar-option:hover {
    background: #f0f9f7;
}
.avatar-option.selected {
    background: #e6f7f2;
    border-left: 3px solid #0ab39c;
}
.avatar-option img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.avatar-option .avatar-info {
    flex: 1;
}
.avatar-option .avatar-name {
    font-weight: 500;
    font-size: 0.9rem;
}
.avatar-option .avatar-desc {
    font-size: 0.75rem;
    color: #6c757d;
}
.btn-group-xs > .btn, .btn-xs {
    padding: .25rem .4rem;
    font-size: .75rem;
    border-radius: .2rem;
}
.info-row {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.info-row:last-child {
    border-bottom: none;
}
.info-k {
    width: 120px;
    font-size: 0.85rem;
    color: #64748b;
}
.info-v {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('profile.index') }}">Mi Perfil</a> @endslot
    @slot('title') Configuración de Perfil @endslot
@endcomponent

@include('components.alerts')

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-image-line me-1"></i>Foto de Perfil</h5>
            </div>
            <div class="card-body text-center">
                <div class="avatar-edit mb-2">
                    <img src="{{ $user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-op-m.png') }}"
                         alt="avatar" class="avatar-upload" id="avatar-preview">
                    <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none">
                        <label for="avatar-input" class="avatar-edit-btn" title="Subir foto personalizada">
                            <i class="ri-camera-line"></i>
                        </label>
                    </form>
                </div>                
                <div class="btn-group w-100" role="group">
                    @php
                        $currentGender = old('gender', 
                            ($user->avatar && str_contains($user->avatar, '-w.')) ? 'w' : 'm'
                        );
                    @endphp
                    <input type="radio" class="btn-check" name="gender" id="genderM" value="m" autocomplete="off"
                           {{ $currentGender === 'm' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary" for="genderM">
                        <i class="ri-men-line"></i> Masculino
                    </label>                    
                    <input type="radio" class="btn-check" name="gender" id="genderW" value="w" autocomplete="off"
                           {{ $currentGender === 'w' ? 'checked' : '' }}>
                    <label class="btn btn-outline-secondary" for="genderW">
                        <i class="ri-women-line"></i> Femenino
                    </label>
                </div>
                <div class="avatar-dropdown-container">
                    @php
                        $currentAvatar = $user->avatar ?? 'avatar-op-m.png';
                        $avatars = [
                            ['file' => 'avatar-admin-m.png', 'name' => 'Administrador', 'gender' => 'm', 'tier' => 'admin'],
                            ['file' => 'avatar-admin-w.png', 'name' => 'Administradora', 'gender' => 'w', 'tier' => 'admin'],
                            ['file' => 'avatar-manager-m.png', 'name' => 'Coordinador', 'gender' => 'm', 'tier' => 'manager'],
                            ['file' => 'avatar-manager-w.png', 'name' => 'Coordinadora', 'gender' => 'w', 'tier' => 'manager'],
                            ['file' => 'avatar-op-m.png', 'name' => 'Operador', 'gender' => 'm', 'tier' => 'op'],
                            ['file' => 'avatar-op-w.png', 'name' => 'Operadora', 'gender' => 'w', 'tier' => 'op'],
                        ];
                        $currentAvatarData = collect($avatars)->firstWhere('file', $currentAvatar) ?? $avatars[4];
                    @endphp                    
                    <button type="button" class="avatar-dropdown-btn" id="avatarDropdownBtn">
                        <img src="{{ URL::asset('build/images/users/'.$currentAvatar) }}" 
                             class="selected-avatar-preview" id="selectedAvatarPreview">
                        <span class="flex-grow-1 text-start" id="selectedAvatarName">
                            {{ $currentAvatarData['name'] }}
                        </span>
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <div class="avatar-dropdown-menu" id="avatarDropdownMenu">
                        @foreach($avatars as $avatar)
                            <div class="avatar-option {{ $currentAvatar === $avatar['file'] ? 'selected' : '' }}"
                                 data-avatar="{{ $avatar['file'] }}"
                                 data-gender="{{ $avatar['gender'] }}"
                                 data-name="{{ $avatar['name'] }}"
                                 onclick="selectAvatar('{{ $avatar['file'] }}', '{{ $avatar['gender'] }}', '{{ $avatar['name'] }}')">
                                <img src="{{ URL::asset('build/images/users/'.$avatar['file']) }}" 
                                     alt="{{ $avatar['name'] }}">
                                <div class="avatar-info">
                                    <div class="avatar-name">{{ $avatar['name'] }}</div>
                                    <div class="avatar-desc">
                                        {{ $avatar['tier'] === 'admin' ? 'Nivel Administrativo' : ($avatar['tier'] === 'manager' ? 'Nivel Coordinación' : 'Nivel Operativo') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <form id="avatar-select-form" action="{{ route('profile.avatar-select') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="avatar" id="selected-avatar-input">
                </form>
                <p class="text-muted small mt-2 mb-0">
                    <i class="ri-information-line me-1"></i>
                    Puedes subir tu propia foto o elegir un avatar del catálogo
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-information-line me-1"></i>Información de la Cuenta</h5>
            </div>
            <div class="card-body p-2">
                <div class="info-row">
                    <div class="info-k"><i class="ri-calendar-line me-2"></i>Miembro desde</div>
                    <div class="info-v">{{ $user->created_at?->format('d/m/Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k"><i class="ri-history-line me-2"></i>Último acceso</div>
                    <div class="info-v">{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k"><i class="ri-shield-user-line me-2"></i>Estado</div>
                    <div class="info-v">
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-k"><i class="ri-fingerprint-line me-2"></i>Carnet</div>
                    <div class="info-v">{{ $user->id_card ?? 'No registrado' }}</div>
                </div>
                @php
                    // Determinar el rol principal para mostrar
                    $mainRole = $user->roles->first();
                    $roleTier = 'op';
                    $roleName = 'Operador';
                    if ($mainRole) {
                        $roleName = strtolower($mainRole->name);
                        if (str_contains($roleName, 'admin')) {
                            $roleTier = 'admin';
                            $roleName = 'Administrador';
                        } elseif (str_contains($roleName, 'supervisor')) {
                            $roleTier = 'manager';
                            $roleName = 'Coordinador';
                        } else {
                            $roleName = 'Operador';
                        }
                    }
                @endphp
                <div class="info-row">
                    <div class="info-k"><i class="ri-user-star-line me-2"></i>Rol principal</div>
                    <div class="info-v">
                        <span class="badge bg-info">
                            {{ $roleName }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personal-info" role="tab">
                            <i class="ri-user-line me-1"></i>Información Personal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#change-password" role="tab">
                            <i class="ri-lock-password-line me-1"></i>Cambiar Contraseña
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    {{-- Personal Information Tab --}}
                    <div class="tab-pane active" id="personal-info" role="tabpanel">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           name="last_name" value="{{ old('last_name', $user->last_name) }}">
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Carnet de Identidad</label>
                                    <input type="text" class="form-control @error('id_card') is-invalid @enderror"
                                           name="id_card" value="{{ old('id_card', $user->id_card) }}">
                                    @error('id_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Dirección</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="reset" class="btn btn-soft-secondary">
                                            <i class="ri-close-line me-1"></i>Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="change-password" role="tabpanel">
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                    <input type="password" 
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        name="current_password" 
                                        value="{{ old('current_password') }}"
                                        required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if(session('error'))
                                        <div class="text-danger small mt-1">{{ session('error') }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" 
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password" 
                                        value="{{ old('password') }}"
                                        required minlength="8">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                    <input type="password" 
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        name="password_confirmation" 
                                        value="{{ old('password_confirmation') }}"
                                        required>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info py-2">
                                        <i class="ri-information-line me-1"></i>
                                        <small>La contraseña debe tener al menos 8 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="reset" class="btn btn-soft-secondary">
                                            <i class="ri-close-line me-1"></i>Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i>Actualizar Contraseña
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown functionality
    const dropdownBtn = document.getElementById('avatarDropdownBtn');
    const dropdownMenu = document.getElementById('avatarDropdownMenu');
    
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
    
    // Función para seleccionar avatar predefinido
    window.selectAvatar = function(avatarFile, gender, avatarName) {
        // Actualizar radio button de género
        const genderRadio = document.querySelector(`input[name="gender"][value="${gender}"]`);
        if (genderRadio) genderRadio.checked = true;
        
        // Actualizar vista previa grande
        document.getElementById('avatar-preview').src = '{{ URL::asset("build/images/users") }}/' + avatarFile;
        
        // Actualizar botón del dropdown
        document.getElementById('selectedAvatarPreview').src = '{{ URL::asset("build/images/users") }}/' + avatarFile;
        document.getElementById('selectedAvatarName').textContent = avatarName;
        
        // Actualizar selección visual en dropdown
        document.querySelectorAll('.avatar-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
        
        // Cerrar dropdown
        dropdownMenu.classList.remove('show');
        
        // Enviar formulario
        document.getElementById('selected-avatar-input').value = avatarFile;
        document.getElementById('avatar-select-form').submit();
    };

    // Avatar upload personalizado
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarForm = document.getElementById('avatar-form');

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El archivo debe ser una imagen (JPG, PNG o GIF)'
                    });
                    this.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La imagen no debe superar los 2MB'
                    });
                    this.value = '';
                    return;
                }
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    // También actualizar preview del dropdown
                    document.getElementById('selectedAvatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);

                // Auto-submit form
                Swal.fire({
                    title: 'Actualizando foto...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        avatarForm.submit();
                    }
                });
            }
        });
    }

    // Validación de contraseñas en tiempo real
    const password = document.querySelector('input[name="password"]');
    const confirmPassword = document.querySelector('input[name="password_confirmation"]');
    
    if (password && confirmPassword) {
        confirmPassword.addEventListener('keyup', function() {
            if (password.value !== this.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Filtrar avatares por género (opcional)
    const genderRadios = document.querySelectorAll('input[name="gender"]');
    genderRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const gender = this.value;
            document.querySelectorAll('.avatar-option').forEach(opt => {
                const avatarGender = opt.dataset.gender;
                if (avatarGender === gender) {
                    opt.style.display = 'flex';
                } else {
                    opt.style.display = 'none';
                }
            });
        });
    });
    
    // Trigger initial filter
    const checkedGender = document.querySelector('input[name="gender"]:checked');
    if (checkedGender) {
        checkedGender.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection