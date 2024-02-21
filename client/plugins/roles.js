export default ({ app, store }, inject) => {
    inject('isSuperAdmin', () => {
        if (!store.getters['auth/user']) return false;
        return store.getters['auth/user'].role === 'super_admin';
    });
    inject('isSubAdmin', () => {
        if (!store.getters['auth/user']) return false;
        return store.getters['auth/user'].role === 'sub_admin';
    });
    inject('isAdmin', () => {
        if (!store.getters['auth/user']) return false;
        return store.getters['auth/user'].role === 'admin';
    });
    inject('isAuditor', () => {
        if (!store.getters['auth/user']) return false;
        return store.getters['auth/user'].role === 'auditor';
    });
    // inject('hasEditPermission', (company_id) => {
    //     const auth_user = store.getters['auth/user']
    //     if (!auth_user) return false;
    //     return $isSuperAdmin() || ($isAdmin() && auth_user.company_id === company_id);
    // });
    // inject('hasReceivePermission', (company_id) => {
    //     const auth_user = store.getters['auth/user']
    //     if (!auth_user) return false;
    //     return $isSuperAdmin() || auth_user.company_id === company_id;
    // });
    // inject('hasDeletePermission', (company_id) => {
    //     const auth_user = store.getters['auth/user']
    //     if (!auth_user) return false;
    //     return $isSuperAdmin() || ($isAdmin() && auth_user.company_id === company_id);
    // });
}