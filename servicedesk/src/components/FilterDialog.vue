<template>
  <q-dialog ref='dialog' @hide='onDialogHide'>
    <q-card class='q-dialog-plugin'>
      <q-card-section class='bg-primary text-white text-center text-h6 q-pa-xs'>
        <q-toolbar>
          <q-toolbar-title>Фильтр заявок</q-toolbar-title>
          <q-btn flat round dense icon="fas fa-times" v-close-popup />
        </q-toolbar>
      </q-card-section>
      <q-form>
        <q-card-section>
        </q-card-section>
        <q-card-actions align='right'>
          <q-btn color='warning' label='Сбросить' icon='fas fa-recycle' size='sm' @click.prevent.stop="onResetClick" />
          <q-btn type='submit' color='primary' label='Применить' icon='fas fa-check' size='sm' @click.prevent.stop="onOkClick" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script>
// import { date } from 'quasar'

export default {
  name: 'FilterDialog',
  data () {
    return {
      selectedDivision: null
    }
  },
  props: {
    filter: Object
  },
  methods: {
    show () {
      this.$refs.dialog.show()
    },
    hide () {
      this.$refs.dialog.hide()
    },
    onDialogHide () {
      this.$emit('hide')
    },
    onOkClick () {
      this.$emit('ok', this.selectedDivision)
    },
    onResetClick () {
    }
  },
  mounted () {
    console.log(this.$refs.dialog)
    this.selectedDivision = this.filter.contract || this.filter.division
  }
}
</script>
