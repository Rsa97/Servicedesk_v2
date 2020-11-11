<template>
  <q-dialog
    ref="dialog"
    persistent
    z-max-max
    @hide="onDialogHide"
  >
    <q-card>
      <q-card-section class="bg-primary text-white text-center text-h6 q-pa-sm">
        Авторизация
      </q-card-section>
      <q-form>
        <q-card-section>
          <q-input
            ref="name"
            v-model="name"
            dense
            label="Имя пользователя"
            lazy-rules
            :rules="[val => val.trim() !== '' || 'Укажите имя пользователя']"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-user" />
            </template>
          </q-input>
          <q-input
            ref="password"
            v-model="password"
            dense
            label="Пароль"
            :type="showPassword ? 'text' : 'password'"
            lazy-rules
            :rules="[val => val.trim() !== '' || 'Укажите пароль']"
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-key" />
            </template>
            <template v-slot:append>
              <q-icon
                v-show="!showPassword"
                name="fas fa-eye"
                class="cursor-pointer"
                @click.stop="showPassword=true"
              />
              <q-icon
                v-show="showPassword"
                name="fas fa-eye-slash"
                class="cursor-pointer"
                @click.stop="showPassword=false"
              />
            </template>
          </q-input>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn
            type="submit"
            color="primary"
            label="Войти"
            icon="fas fa-check"
            size="sm"
            @click.prevent="onOkClick"
          />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'AuthLoginForm',

  props: {
    login: {
      type: String,
      default: '',
    },
  },

  data() {
    return {
      name: this.login,
      password: '',
      showPassword: false,
    };
  },

  methods: {
    show() {
      this.$refs.dialog.show();
    },

    hide() {
      this.$refs.dialog.hide();
    },

    onDialogHide() {
      this.$emit('hide');
    },

    onOkClick() {
      this.$refs.name.validate();
      this.$refs.password.validate();
      if (this.$refs.name.hasError || this.$refs.password.hasError) {
        return;
      }
      this.$emit('ok', { login: this.name, password: this.password });
      this.hide();
    },
  },
};
</script>

<style lang="scss">
  [z-max-max] {
    z-index: 9999;
  }
</style>
