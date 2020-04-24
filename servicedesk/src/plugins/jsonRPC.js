import { Dialog, Notify, Loading } from 'quasar'
import SpinnerSD from '../components/SpinnerSD'
import AuthLoginForm from '../components/AuthLoginForm'
import axios from 'axios'

export default {
  install (Vue) {
    const jsonRPC = async function (method, params) {
      let state = 'none'
      if (this.$store.state.auth && this.$store.state.auth.token) {
        state = 'normal'
      }
      let id, data
      while (state !== 'done') {
        id = this.$jsonRPC.nextId()
        switch (state) {
          case 'none':
            data = await this.$jsonRPC.doLogin(id, this)
            if (data.result !== undefined) {
              this.$store.dispatch('auth/setToken', data.result.jwt)
              this.$store.dispatch('auth/setRefreshToken', data.result.ref)
              this.$store.dispatch('auth/setAuthorized')
              state = 'normal'
            }
            break
          case 'refresh':
            data = await this.$jsonRPC.doRefresh(id, this)
            if (data.error !== undefined) {
              this.$store.dispatch('auth/setToken', null)
              this.$store.dispatch('auth/setRefreshToken', null)
              this.$store.dispatch('auth/setunAuthorized')
              state = 'none'
            } else {
              this.$store.dispatch('auth/setToken', data.result.jwt)
              this.$store.dispatch('auth/setRefreshToken', data.result.ref)
              state = 'normal'
            }
            break
          case 'normal':
            data = await this.$jsonRPC.doRequest(id, method, params, this)
            if (data.error !== undefined && data.error.code === -36004) {
              state = 'refresh'
            } else {
              state = 'done'
            }
        }
      }
    }

    jsonRPC.showMessage = (type, text) => {
      Notify.create({
        message: text,
        type: type === 'error' ? 'negative' : 'warning',
        position: 'top',
        timeout: 10000
      })
    }

    jsonRPC.parseResponse = (response, id, that) => {
      if (response.status !== 200) {
        that.$jsonRPC.showMessage('error', `${response.status}: ${response.statusText}`)
        return undefined
      }
      if (response.data === undefined || response.data.jsonrpc !== '2.0' ||
          (response.data.result === undefined && response.data.error === undefined) ||
          (response.data.result !== undefined && response.data.id !== id)) {
        that.$jsonRPC.showMessage('error', 'Неверный ответ от сервера')
        return undefined
      }
      const data = response.data
      if (data.result !== undefined && data.result.warning !== undefined) {
        that.$jsonRPC.showMessage('warning', data.result.warning)
      }
      if (data.error !== undefined) {
        let error = 'Неизвестная ошибка сервера'
        if (data.error.message !== undefined) {
          error = data.error.message
        }
        if (data.error.code !== undefined) {
          error = `${data.error.code}: ${error}`
        }
        that.$jsonRPC.showMessage('error', error)
      }
      return data
    }

    jsonRPC.doLogin = async (id, that) => {
      let login = localStorage.getItem('login')
      if (login === null) {
        login = ''
      }
      const credentials = await new Promise(resolve => {
        Dialog.create({
          component: AuthLoginForm,
          login: login
        }).onOk(credentials => {
          resolve(credentials)
        })
      })
      localStorage.setItem('login', credentials.login)
      const response = await that.$jsonRPC.call.post(
        'http://10.149.0.206/api/',
        {
          jsonrpc: '2.0',
          id: id,
          method: 'Auth::auth',
          params: credentials
        }
      )
      return that.$jsonRPC.parseResponse(response, id, that)
    }

    jsonRPC.doRefresh = async (id, that) => {
      const response = await that.$jsonRPC.call.post(
        'http://10.149.0.206/api/',
        {
          jsonrpc: '2.0',
          id: id,
          method: 'Auth::refresh',
          params: {
            ref: that.$store.state.auth.refreshToken
          }
        }
      )
      return that.$jsonRPC.parseResponse(response, id, that)
    }

    jsonRPC.doRequest = async (id, method, params, that) => {
      params.jwt = that.$store.state.auth.token
      const response = await that.$jsonRPC.call.post(
        'http://10.149.0.206/api/',
        {
          jsonrpc: '2.0',
          id: id,
          method: method,
          params: params
        }
      )
      return that.$jsonRPC.parseResponse(response, id, that)
    }

    jsonRPC.nextId = (() => {
      let rpcId = 0
      return () => ++rpcId
    })()

    jsonRPC.loadingCount = 0

    jsonRPC.call = axios.create({
      baseURL: '//portal.sodrk.ru',
      timeout: 30000,
      headers: {
        'Content-Type': 'application/json;charset=utf-8'
      }
    })

    jsonRPC.call.interceptors.request.use(
      config => {
        if (jsonRPC.loadingCount === 0) {
          Loading.show({
            spinner: SpinnerSD,
            delay: 100
          })
        }
        jsonRPC.loadingCount++
        return config
      },
      error => Promise.reject(error)
    )

    jsonRPC.call.interceptors.response.use(
      config => {
        jsonRPC.loadingCount--
        if (jsonRPC.loadingCount === 0) {
          Loading.hide()
        }
        return config
      },
      error => Promise.reject(error)
    )

    Vue.prototype.$jsonRPC = jsonRPC
  }
}
