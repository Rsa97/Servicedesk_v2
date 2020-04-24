export default function () {
  return {
    isAuthorized: false,
    token: null,
    refreshToken: null,
    isRefreshing: false,
    refreshingCall: null
  }
}
