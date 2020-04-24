<template>
  <q-layout view="hHh lpR fFf">
    <main-header
      has-drawer-button
      @switchDrawerForceOpen='drawerForceOpen = !drawerForceOpen'
    >
    </main-header>
    <q-drawer
      show-if-above
      :mini='!drawerForceOpen && miniState'
      side="left"
      elevated
      @mouseover='miniState=false'
      @mouseout='miniState=true'
      :mini-to-overlay='!drawerForceOpen'
      :breakpoint="0"
      :width='250'
    >
      <q-list padding style='q-pa-sm'>
        <q-item clickable v-ripple @click.prevent.stop='refreshTickets'>
          <q-item-section avatar>
            <q-icon name='fas fa-sync-alt' />
          </q-item-section>
          <q-item-section class='text-h6'>
            Обновить
          </q-item-section>
        </q-item>
        <q-item clickable v-ripple @click.prevent.stop='openFilter'>
          <q-item-section avatar>
            <q-icon name='fas fa-filter' :color='filterIconColor' />
          </q-item-section>
          <q-item-section class='text-h6'>
            Фильтр
          </q-item-section>
        </q-item>
        <q-item clickable v-ripple class='text-negative' @click.prevent.stop='newTicket'>
          <q-item-section avatar>
            <q-icon name='fas fa-exclamation-triangle' />
          </q-item-section>
          <q-item-section class='text-h6'>
            Новая заявка
          </q-item-section>
        </q-item>
        <q-item clickable v-ripple @click.prevent.stop='messagingConfig'>
          <q-item-section avatar>
            <q-icon name='fas fa-sms' />
          </q-item-section>
          <q-item-section class='text-h6'>
            Настройка сообщений
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>
    <q-page-container>
      <q-page>
      </q-page>
    </q-page-container>
    <main-footer></main-footer>
  </q-layout>
</template>

<script>
import MainHeader from '../components/MainHeader'
import MainFooter from '../components/MainFooter'
import FilterDialog from '../components/FilterDialog'
// import CustomPrompt from '../components/CustomPrompt'

export default {
  name: 'TechSupport',
  data () {
    return {
      miniState: true,
      tickets: [],
      drawerForceOpen: false,
      filterIconColor: 'black',
      filter: {
        contract: null,
        division: null
      }
    }
  },
  computed: {
  },
  components: {
    MainHeader,
    MainFooter
    // FilterDialog
    // CustomPrompt,
  },
  methods: {
    openFilter () {
      this.$q.dialog({
        component: FilterDialog,
        parent: this,
        filter: this.filter
      }).onOk(filter => {
        this.filter = filter
      })
    }
  },
  mounted () {
  }
}
</script>
