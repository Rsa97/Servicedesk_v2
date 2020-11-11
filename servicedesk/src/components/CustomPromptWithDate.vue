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
          <q-input
            v-model="selectedDate"
            :label="dateLabel"
            readonly
          >
            <template v-slot:prepend>
              <q-icon
                name="fas fa-calendar"
                class="cursor-pointer"
              >
                <q-popup-proxy
                  ref="qDateProxy"
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date
                    v-model="selectedDate"
                    minimal
                    mask="DD.MM.YYYY"
                    :options="options"
                    @input="() => $refs.qDateProxy.hide()"
                  />
                </q-popup-proxy>
              </q-icon>
            </template>
            <template v-slot:append>
              <q-icon
                v-show="selectedDate !== null"
                name="fas fa-times"
                class="cursor-pointer"
                @click="selectedDate = null"
              />
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
import { date } from 'quasar';

export default {
  name: 'CustomPromptWithDate',

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
    dateLabel: {
      type: String,
      default: 'Дата',
    },
  },

  data() {
    return {
      text: '',
      selectedDate: null,
      currentDate: date.formatDate(Date.now(), 'YYYY/MM/DD'),
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
      this.$emit(
        'ok',
        {
          text: this.text.trim(),
          date: this.selectedDate === null
            ? null
            : date.formatDate(date.extractDate(this.selectedDate, 'DD.MM.YYYY'), 'YYYY-MM-DD'),
        },
      );
      this.hide();
    },

    options(checkDate) {
      return checkDate > this.currentDate;
    },
  },
};
</script>
