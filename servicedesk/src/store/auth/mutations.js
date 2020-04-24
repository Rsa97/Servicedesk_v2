export function SET_AUTHORIZED_STATE (state, isAuthorized) {
  state.isAuthorized = isAuthorized
}

export function SET_TOKEN (state, token) {
  state.token = token
}

export function SET_REFRESH_TOKEN (state, refreshToken) {
  state.refreshToken = refreshToken
}

export function SET_REFRESHING_STATE (state, refreshingState) {
  state.isRefreshing = refreshingState
}

export function SET_REFRESHING_CALL (state, refreshingCall) {
  state.refreshingCall = refreshingCall
}
