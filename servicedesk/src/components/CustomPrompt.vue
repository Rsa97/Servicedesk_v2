<template>
  <q-dialog
    ref="dialog"
    @hide="onDialogHide"
  >
    <q-card style="width: 700px; max-width: 80vw;">
      <q-card-section class="bg-primary text-white text-center text-h5 q-pa-xs">
        <q-toolbar>
          <q-toolbar-title>
            {{ header }}
          </q-toolbar-title>
          <q-btn
            v-close-popup
            flat
            round
            dense
            icon="fas fa-times"
          />
        </q-toolbar>
      </q-card-section>
      <q-form class="q-pa-sm">
        <q-card-section class="q-pa-xs">
          <q-input
            ref="text"
            v-model="text"
            :label="label"
            dense
          >
            <template v-slot:prepend>
              <q-icon :name="icon" />
            </template>
          </q-input>
        </q-card-section>
        <q-card-actions
          align="right"
          class="q-pa-xs"
        >
          <q-btn
            type="submit"
            color="primary"
            label="ОК"
            icon="fas fa-check"
            size="sm"
            @click.prevent.stop="onOkClick"
          />
          <q-btn
            v-close-popup
            color="negative"
            label="Отмена"
            icon="fas fa-times"
            size="sm"
          />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'CustomPrompt',

  props: {
    header: {
      type: String,
      default: 'Ответ',
    },
    label: {
      type: String,
      default: 'Ответ',
    },
    icon: {
      type: String,
      default: 'fas fa-question',
    },
  },

  data() {
    return {
      text: '',
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
      this.$emit('ok', this.text.trim());
      this.hide();
    },
  },
};
</script>
