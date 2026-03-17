
<script>
(function () {
'use strict';
const ROLE_PERM_MAP  = JSON.parse(document.getElementById('rolePermData').textContent);
const DIRECT_IDS     = new Set(JSON.parse(document.getElementById('directPermData').textContent));
const ROLE_INIT_IDS  = new Set(JSON.parse(document.getElementById('roleInitData').textContent));
const roleCbs  = [...document.querySelectorAll('.role-cb')];
const permCbs  = [...document.querySelectorAll('.perm-cb')];
const groupCbs = [...document.querySelectorAll('.group-cb')];

permCbs.forEach(cb => {
    cb.dataset.state = cb.dataset.state || 'none';
});

function getCheckedRoleIds() {
    return roleCbs.filter(cb => cb.checked).map(cb => Number(cb.dataset.roleId));
}

function getAllRolePermIds(roleIds) {
    const set = new Set();
    roleIds.forEach(rid => (ROLE_PERM_MAP[rid] || []).forEach(pid => set.add(Number(pid))));
    return set;
}

function syncPermissionsFromRoles(changedRoleId, nowChecked) {
    const checkedRoleIds    = getCheckedRoleIds();
    const allActiveRolePerms = getAllRolePermIds(checkedRoleIds);
    const removedRolePerms = nowChecked
        ? new Set()
        : new Set((ROLE_PERM_MAP[changedRoleId] || []).map(Number).filter(pid => !allActiveRolePerms.has(pid)));
    permCbs.forEach(cb => {
        const pid = Number(cb.value);
        if (nowChecked) {
            if ((ROLE_PERM_MAP[changedRoleId] || []).map(Number).includes(pid)) {
                if (cb.dataset.state !== 'manual' || cb.checked) {
                    cb.checked = true;
                    cb.dataset.state = cb.dataset.state === 'manual' ? 'manual' : 'role';
                    updatePermItem(cb);
                }
            }
        } else {
            if (removedRolePerms.has(pid) && cb.dataset.state === 'role') {
                cb.checked = false;
                cb.dataset.state = 'none';
                updatePermItem(cb);
            }
        }
    });
    updateGroupCheckboxes();
}

function updatePermItem(cb) {
    const item = cb.closest('.perm-item');
    if (!item) return;
    item.classList.toggle('bg-success', cb.checked && cb.dataset.state === 'manual');
    item.classList.toggle('bg-opacity-10', cb.checked && cb.dataset.state === 'manual');
}

function updateGroupCheckboxes() {
    groupCbs.forEach(gcb => {
        const slug    = gcb.dataset.group;
        const all     = permCbs.filter(cb => cb.dataset.group === slug);
        const checked = all.filter(cb => cb.checked);
        if (!all.length) return;
        gcb.checked       = checked.length === all.length;
        gcb.indeterminate = checked.length > 0 && checked.length < all.length;
    });
}

roleCbs.forEach(cb => {
    cb.addEventListener('change', function () {
        const card = document.getElementById('roleCard_' + this.dataset.roleId);
        card?.classList.toggle('border-primary',   this.checked);
        card?.classList.toggle('bg-primary',       this.checked);
        card?.classList.toggle('bg-opacity-10',    this.checked);

        syncPermissionsFromRoles(Number(this.dataset.roleId), this.checked);
        updateAvatarPreview();
    });
});

permCbs.forEach(cb => {
    cb.addEventListener('change', function () {
        this.dataset.state = this.checked ? 'manual' : 'none';
        updatePermItem(this);
        updateGroupCheckboxes();
    });
});

groupCbs.forEach(gcb => {
    gcb.addEventListener('change', function () {
        const slug = this.dataset.group;
        permCbs.filter(cb => cb.dataset.group === slug).forEach(cb => {
            cb.checked = this.checked;
            cb.dataset.state = this.checked ? 'manual' : 'none';
            updatePermItem(cb);
        });
        updateGroupCheckboxes();
    });
});

document.getElementById('btnSelectAll')?.addEventListener('click', () => {
    permCbs.forEach(cb => { cb.checked = true; cb.dataset.state = 'manual'; updatePermItem(cb); });
    updateGroupCheckboxes();
});

document.getElementById('btnDeselectAll')?.addEventListener('click', () => {
    permCbs.forEach(cb => { cb.checked = false; cb.dataset.state = 'none'; updatePermItem(cb); });
    updateGroupCheckboxes();
});

const AVATAR_BASE = '<?php echo e(URL::asset("build/images/users")); ?>';
const TIER_ORDER  = { op: 0, delegado: 1, manager: 2, admin: 3 };
const TIER_LABELS = { op: 'Operador', delegado: 'Delegado', manager: 'Coordinador', admin: 'Administrador' };
const TIER_FILE   = { op: 'op', delegado: 'manager', manager: 'manager', admin: 'admin' };

function updateAvatarPreview() {
    const gender = document.querySelector('input[name="gender"]:checked')?.value ?? 'm';
    const gLbl   = gender === 'm' ? 'M' : 'F';
    let tier = 'op';
    roleCbs.filter(cb => cb.checked).forEach(cb => {
        const n = cb.closest('.role-card')?.querySelector('.fw-semibold')?.textContent?.toLowerCase() ?? '';
        if (n.includes('admin'))                                        tier = TIER_ORDER['admin']   > TIER_ORDER[tier] ? 'admin'   : tier;
        else if (n.includes('supervisor'))                              tier = TIER_ORDER['manager'] > TIER_ORDER[tier] ? 'manager' : tier;
        else if (n.includes('registrador') || n.includes('modificador')) tier = TIER_ORDER['delegado'] > TIER_ORDER[tier] ? 'delegado' : tier;
    });
    const file = `avatar-${TIER_FILE[tier]}-${gender}.png`;
    const el   = document.getElementById('avatarPreview');
    if (el) el.src = `${AVATAR_BASE}/${file}`;
    const hint = document.getElementById('avatarHint');
    if (hint) hint.textContent = `${TIER_LABELS[tier]} (${gLbl})`;
}

document.querySelectorAll('input[name="gender"]').forEach(r => r.addEventListener('change', updateAvatarPreview));

const emailInput    = document.getElementById('emailInput');
const emailFeedback = document.getElementById('emailFeedback');
const userId        = window.__userId;

emailInput?.addEventListener('blur', function () {
    const email = this.value.trim();
    if (!email) return;
    const url = `<?php echo e(route('users.check-email')); ?>?email=${encodeURIComponent(email)}` + (userId ? `&user_id=${userId}` : '');
    fetch(url).then(r => r.json()).then(data => {
        if (data.exists) {
            emailFeedback.textContent = '⚠ Este email ya está registrado.';
            emailFeedback.className   = 'form-text text-danger';
        } else {
            emailFeedback.textContent = '✓ Email disponible.';
            emailFeedback.className   = 'form-text text-success';
        }
    }).catch(() => {});
});

document.querySelector('form')?.addEventListener('submit', function (e) {
    const pw  = document.getElementById('passwordInput')?.value ?? '';
    const pwc = document.getElementById('passwordConfirm')?.value ?? '';
    const isEdit = window.__isEdit === true;
    if (!isEdit && !pw) { e.preventDefault(); Swal.fire({ icon:'error', title:'Contraseña requerida', text:'Debes ingresar una contraseña.' }); return; }
    if (pw && pw !== pwc) { e.preventDefault(); Swal.fire({ icon:'error', title:'Contraseñas no coinciden', text:'Verifica la confirmación.' }); }
});

updateGroupCheckboxes();
updateAvatarPreview();

})();
</script>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views/users/_form_js.blade.php ENDPATH**/ ?>