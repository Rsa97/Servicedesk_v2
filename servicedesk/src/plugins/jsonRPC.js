import axios from 'axios';
import { Dialog, Notify, Loading } from 'quasar';
import SpinnerSD from '../components/SpinnerSD';
import AuthLoginForm from '../components/AuthLoginForm';

const silentErrors = [-36004, -36002];

export default {
  install(Vue) {
    async function jsonRPC(method, params, background = false) {
      if (!background) {
        this.$jsonRPC.showLoading();
      }
      let state = 'refresh';
      if (this.$store.state.auth && this.$store.state.auth.token) {
        state = 'normal';
      }
      let data;
      while (state !== 'done') {
        switch (state) { // eslint-disable-line default-case
          case 'refresh':
            if (!this.$store.state.auth.isRefreshing) {
              this.$store.dispatch('auth/setRefreshingState');
              this.$store.dispatch('auth/setRefreshingCall', this.$jsonRPC.doRefreshToken(this.$store));
            }
            await this.$store.state.auth.refreshingCall; // eslint-disable-line no-await-in-loop
            this.$store.dispatch('auth/unsetRefreshingState');
            state = 'normal';
            break;
          case 'normal':
            data = await this.$jsonRPC.doRequest( // eslint-disable-line no-await-in-loop
              method,
              params,
              this.$store.state.auth.token,
            );
            if (data.error !== undefined && data.error.code === -36004) {
              state = 'refresh';
            } else {
              state = 'done';
            }
            break;
        }
      }
      if (!background) {
        this.$jsonRPC.hideLoading();
      }
      if (data.result !== undefined) {
        return data.result;
      }
      return null;
    }

    async function doRefreshToken(store) {
      let state = 'refresh';
      if (!store.state.auth || !store.state.auth.token) {
        state = 'none';
      }
      let data;
      while (state !== 'done') {
        switch (state) { // eslint-disable-line default-case
          case 'none':
            data = await this.doLogin(); // eslint-disable-line no-await-in-loop
            if (data.result !== undefined) {
              store.dispatch('auth/setToken', data.result.jwt);
              store.dispatch('auth/setRefreshToken', data.result.ref);
              store.dispatch('auth/setAuthorized');
              state = 'done';
            }
            break;
          case 'refresh':
            // eslint-disable-next-line no-await-in-loop
            data = await this.doRefresh(store.state.auth.refreshToken);
            if (data.error !== undefined) {
              store.dispatch('auth/unsetAuthorized');
              store.dispatch('auth/setToken', null);
              store.dispatch('auth/setRefreshToken', null);
              state = 'none';
            } else {
              store.dispatch('auth/setToken', data.result.jwt);
              store.dispatch('auth/setRefreshToken', data.result.ref);
              state = 'done';
            }
            break;
        }
      }
    }
    jsonRPC.doRefreshToken = doRefreshToken;

    function showLoading() {
      if (this.loadingCount === 0) {
        Loading.show({
          spinner: SpinnerSD,
          delay: 100,
        });
      }
      this.loadingCount += 1;
    }
    jsonRPC.showLoading = showLoading;

    function hideLoading() {
      this.loadingCount -= 1;
      if (this.loadingCount === 0) {
        Loading.hide();
      }
    }
    jsonRPC.hideLoading = hideLoading;

    jsonRPC.showMessage = (type, text) => {
      Notify.create({
        message: text,
        type: type === 'error' ? 'negative' : 'warning',
        position: 'top',
        timeout: 10000,
      });
    };

    function parseResponse(response, id) {
      if (response.status !== 200) {
        this.showMessage('error', `${response.status}: ${response.statusText}`);
        return { error: { code: response.status, message: response.statusText } };
      }
      if (response.data === undefined || response.data.jsonrpc !== '2.0'
          || (response.data.result === undefined && response.data.error === undefined)
          || (response.data.result !== undefined && response.data.id !== id)
      ) {
        this.showMessage('error', 'Неверный ответ от сервера');
        return { error: { code: 0, message: 'Неверный ответ от сервера' } };
      }
      const { data } = response;
      if (data.result !== undefined && data.result.warning !== undefined) {
        this.showMessage('warning', data.result.warning);
      }
      if (data.error !== undefined && !silentErrors.includes(data.error.code)) {
        let error = 'Неизвестная ошибка сервера';
        if (data.error.message !== undefined) {
          error = data.error.message;
        }
        if (data.error.code !== undefined) {
          error = `${data.error.code}: ${error}`;
        }
        this.showMessage('error', error);
      }
      return data;
    }
    jsonRPC.parseResponse = parseResponse;

    async function doLogin() {
      let login = localStorage.getItem('login');
      if (login === null) {
        login = '';
      }
      const credentials = await new Promise(resolve => {
        Dialog.create({ component: AuthLoginForm, login })
          .onOk(creds => {
            resolve(creds);
          });
      });
      localStorage.setItem('login', credentials.login);
      return this.post('Auth::auth', credentials);
    }
    jsonRPC.doLogin = doLogin;

    function doRefresh(refreshToken) {
      return this.post('Auth::refresh', { ref: refreshToken });
    }
    jsonRPC.doRefresh = doRefresh;

    function doRequest(method, params, token) {
      params.jwt = token;
      return this.post(method, params);
    }
    jsonRPC.doRequest = doRequest;

    async function post(method, params) {
      const id = this.nextId();
      try {
        const response = await axios.post(
          'http://10.149.0.206/api/',
          {
            jsonrpc: '2.0',
            id,
            method,
            params,
          },
          {
            timeout: 30000,
            headers: { 'Content-Type': 'application/json;charset=utf-8' },
          },
        );
        return this.parseResponse(response, id);
      } catch (err) {
        this.showMessage('error', err.message);
        return { error: { code: 0, message: err.message } };
      }
    }
    jsonRPC.post = post;

    jsonRPC.nextId = (() => {
      let rpcId = 0;
      return () => {
        rpcId += 1;
        return rpcId;
      };
    })();

    jsonRPC.loadingCount = 0;

    Vue.prototype.$jsonRPC = jsonRPC;
  },
};
