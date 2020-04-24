export const payload = (state) => {
  if (!state.isAuthorized) {
    return {}
  }
  const payload = state.token.split('.')[1]
  return JSON.parse(decodeURIComponent(escape(atob(payload.replace('_', '/').replace('-', '+')))))
}

export const isAuthorized = (state) => {
  return state.isAuthorized
}
