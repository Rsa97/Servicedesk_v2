<template>
  <q-dialog
    ref="dialog"
    @hide="onDialogHide"
  >
    <q-card style="width: 700px; max-width: 80vw;">
      <q-card-section class="bg-primary text-white text-center text-h5 q-pa-xs">
        <q-toolbar>
          <q-toolbar-title>
            Решение проблемы
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
            v-model="problem"
            type="textarea"
            label="Обнаруженная проблема"
            input-style="resize: none;"
            class="q-pb-xs"
            dense
            outlined
          />
          <q-input
            v-model="solution"
            type="textarea"
            label="Решение"
            input-style="resize: none;"
            class="q-pb-xs"
            dense
            outlined
          />
          <q-input
            v-model="recomendation"
            type="textarea"
            label="Рекомендации клиенту"
            input-style="resize: none;"
            dense
            outlined
          />
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
  name: 'CustomPromptSolution',

  data() {
    return {
      problem: '',
      solution: '',
      recomendation: '',
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
          problem: this.problem.trim(),
          solution: this.solution.trim(),
          recomendation: this.recomendation.trim(),
        },
      );
      this.hide();
    },
  },
};
</script>
