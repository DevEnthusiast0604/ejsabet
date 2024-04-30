export default ({ store, redirect }) => {
  if (store.getters['auth/check']) {
    const role = store.getters['auth/user'].role
    if (role === 'auditor') {
      return redirect('/audit');
    } else if (role === 'sub_admin') {
      return redirect('/daily_transactions');
    } else  {
      return redirect('/transactions');
    }
  }
}
