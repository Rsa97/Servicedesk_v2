<template>
  <q-footer elevated class="bg-primary text-white">
    <q-toolbar class='text-h6'>
      <q-toolbar-title>
        <q-icon name='fas fa-phone-alt'/>
        8&nbsp;(8212)&nbsp;400-440
      </q-toolbar-title>
      <q-toolbar-title style='text-align: center'>
        <q-btn
          flat
          no-caps
          type='a'
          href='https://www.sodrk.ru/rms'
          download
        >
          <q-icon left name='fas fa-desktop' />
          <span class='text-h6'>Удалённый помощник</span>
        </q-btn>
      </q-toolbar-title>
      <q-toolbar-title style='font-size: 0.8rem; text-align: right; line-height: 1.6em;'>
        <q-icon name='far fa-copyright'/>
        <span> by RSA, 2020</span>
        <br />
        <a class='text-white' href='https://quasar.dev' target='_blank'>
          <img src="../assets/quasar.png" class='vertical-middle' />
          <span> Quasar</span>
        </a>
      </q-toolbar-title>
    </q-toolbar>
  </q-footer>
</template>

<script>
export default {
  data () {
    return {}
  },
  methods: {
    loadRMS: function () {
      this.$jsonRPC('Test::check', {})
    }
  }
}
</script>
