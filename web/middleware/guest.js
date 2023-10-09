export default function ({ store, route, redirect, app }) {
  console.log('guest middleware');
  if (!store.state.auth.loggedIn){
    const pathExceptPrefix = route.fullPath.slice(3);
    if (
      pathExceptPrefix !== '/login'
        && pathExceptPrefix !== '/register'
        && pathExceptPrefix !== '/forgot-password'
        && pathExceptPrefix.indexOf('/password-reset/') !== 0
    ) {
      return redirect(`/${app.i18n.locale}/login`);
    }
  }
}