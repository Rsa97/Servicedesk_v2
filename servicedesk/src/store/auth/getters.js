export const payload = (state) => {
  if (!state.isAuthorized) {
    return {};
  }
  const data = state.token.split('.')[1];
  return JSON.parse(decodeURIComponent(escape(atob(data.replace('_', '/').replace('-', '+')))));
};

export const isAuthorized = (state) => state.isAuthorized;
