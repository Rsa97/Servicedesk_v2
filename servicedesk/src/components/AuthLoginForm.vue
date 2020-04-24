<template>
  <q-dialog ref='dialog' no-backdrop-dismiss no-route-dismiss>
    <q-card>
      <q-card-section class='bg-primary text-white text-center text-h6 q-pa-sm'>Авторизация</q-card-section>
      <q-form>
        <q-card-section>
          <q-input v-model='name' label='Имя пользователя' dense />
          <q-input v-model='password' label='Пароль' type='password' dense />
        </q-card-section>
        <q-card-actions align='right'>
          <q-btn type='submit' color='primary' label='Войти' icon='fas fa-check' size='sm' @click.prevent="onOkClick" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'AuthLoginForm',
  data () {
    return {
      name: this.login,
      password: ''
    }
  },
  props: {
    login: String
  },
  methods: {
    show () {
      this.$refs.dialog.show()
    },
    hide () {
      this.$refs.dialog.hide()
    },
    onOkClick (e) {
      this.$emit('ok', { login: this.name, password: this.password })
      this.hide()
    }
  }
}
</script>
