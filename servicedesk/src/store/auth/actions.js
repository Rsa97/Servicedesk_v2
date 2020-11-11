export function setToken(context, token) {
  context.commit('SET_TOKEN', token);
}

export function setRefreshToken(context, refreshToken) {
  context.commit('SET_REFRESH_TOKEN', refreshToken);
}

export function setAuthorized(context) {
  context.commit('SET_AUTHORIZED_STATE', true);
}

export function unsetAuthorized(context) {
  context.commit('SET_AUTHORIZED_STATE', false);
}

export function setRefreshingState(context) {
  context.commit('SET_REFRESHING_STATE', true);
}

export function unsetRefreshingState(context) {
  context.commit('SET_REFRESHING_STATE', false);
}

export function setRefreshingCall(context, refreshingCall) {
  context.commit('SET_REFRESHING_CALL', refreshingCall);
}
