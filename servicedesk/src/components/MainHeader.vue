<template>
  <q-header elevated class="bg-primary text-white">
    <q-toolbar>
      <q-toolbar-title>
        <q-btn flat round dense icon="fas fa-bars" class="q-mr-sm" @click='$emit("switchDrawerForceOpen")'/>
      </q-toolbar-title>
      <q-space />
      <img src="../assets/logo.svg" class='main-logo vertical-middle q-pa-sm' />
      <q-space />
      <q-toolbar-title style='text-align: right'>
        <q-btn flat no-caps @click='doLoginLogout'>
          <span class='text-h6'>{{ userName }}</span>
          <q-icon right :name='isAuthorized ? "fas fa-sign-out-alt" : "fas fa-sign-in-alt"' />
        </q-btn>
      </q-toolbar-title>
    </q-toolbar>
  </q-header>
</template>

<script>
export default {
  props: {
    hasDrawerButton: Boolean
  },
  data () {
    return {
      pageText: 'Портал',
      pageIcon: 'fas fa-home',
      routes: []
    }
  },
  computed: {
    userName () {
      return this.$store.state.auth && this.$store.state.auth.isAuthorized
        ? this.$store.getters['auth/payload'].name
        : 'ВОЙТИ'
    },
    isAuthorized () {
      return this.$store.state.auth && this.$store.state.auth.isAuthorized
    }
  },
  methods: {
    doLoginLogout () {
      if (this.isAuthorized) {
        this.$store.dispatch('auth/setToken', null)
        this.$store.dispatch('auth/setRefreshToken', null)
        this.$store.dispatch('auth/unsetAuthorized')
      }
      this.$jsonRPC(
        'Auth::checkAuth',
        {}
      )
    }
  }
}
</script>

<style lang='scss' scoped>
  .main-logo {
    min-width: 217px;
  }
  button.fa-sm i {
    font-size: 1.2em !important;
  }
</style>
