<template>
  <q-layout view="hHh lpR fFf">
    <main-header />
    <q-drawer
      show-if-above
      :mini="!drawerForceOpen && miniState"
      side="right"
      elevated
      :mini-to-overlay="!drawerForceOpen"
      :breakpoint="0"
      :width="250"
      @mouseover="miniState=false"
      @mouseout="miniState=true"
    >
      <div align="right">
        <q-btn
          flat
          round
          size="sm"
          icon="fas fa-thumbtack"
          class="pin q-ma-sm"
          :class="{pinned: drawerForceOpen}"
          @click="drawerForceOpen = !drawerForceOpen"
        />
      </div>
      <q-list
        padding
        style="q-pa-sm"
      >
        <q-item
          v-ripple
          clickable
          @click.prevent.stop="refreshRequests"
        >
          <q-item-section avatar>
            <q-icon name="fas fa-sync-alt" />
          </q-item-section>
          <q-item-section class="text-h6">
            Обновить
          </q-item-section>
        </q-item>
        <q-item
          v-ripple
          clickable
          class="text-negative"
          @click.prevent.stop="newRequest"
        >
          <q-item-section avatar>
            <q-icon name="fas fa-bell" />
          </q-item-section>
          <q-item-section class="text-h6">
            Новая заявка
          </q-item-section>
        </q-item>
        <q-item
          v-ripple
          clickable
          @click.prevent.stop="showFilterSetup = true"
        >
          <q-item-section avatar>
            <q-icon
              name="fas fa-filter"
              :color="filterIconColor"
            />
          </q-item-section>
          <q-item-section class="text-h6">
            Фильтр
          </q-item-section>
        </q-item>
        <q-item
          v-ripple
          clickable
          @click.prevent.stop="showMessageSettings = true"
        >
          <q-item-section avatar>
            <q-icon name="fas fa-sms" />
          </q-item-section>
          <q-item-section class="text-h6">
            Настройка сообщений
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>
    <q-page-container>
      <q-page>
        <q-splitter
          v-model="splitter"
          style="min-height: calc( 100vh - 106px);"
          unit="px"
          :limits="[130, 130]"
        >
          <template v-slot:before>
            <q-tabs
              v-model="selectedTab"
              vertical
              class="text-primary"
              active-bg-color="primary"
              active-color="white"
              indicator-color="primary"
              @input="onChangeTab"
            >
              <q-tab
                v-for="(tab, name) in tabs"
                :key="name"
                no-caps
                :name="name"
                :icon="tab.icon"
                :label="tab.label"
              >
                <q-badge
                  floating
                  transparent
                  :color="tab.badgeColor"
                >
                  {{ tab.show }} / {{ tab.total }}
                </q-badge>
              </q-tab>
            </q-tabs>
          </template>
          <template v-slot:after>
            <q-table
              v-if="selectedTab === 'planned'"
              row-key="number"
              flat
              dense
              hide-bottom
              virtual-scroll
              separator="cell"
              class="mainTable"
              :data="requests"
              :columns="plannedTable"
              :pagination.sync="pagination"
              :rows-per-page-options="[0]"
              :virtual-scroll-sticky-size-start="28"
            >
              <template v-slot:body-cell-icons="props">
                <q-td
                  :props="props"
                  :data-number="props.row.number"
                >
                  <q-icon
                    :name="props.row.state.canGet ? states.planned.icon : 'far fa-calendar-times'"
                    :color="props.row.state.canGet ? states.planned.color : 'warning'"
                    size="1em"
                  >
                    <q-tooltip
                      content-style="font-size: 0.9em; white-space: pre-line;"
                      :delay="1000"
                    >
                      {{ states.planned.tooltip }}
                    </q-tooltip>
                  </q-icon>
                  <q-icon
                    v-if="props.row.partner.id !== null"
                    :name="states.partner.icon"
                    :color="states.partner.color"
                    size="1em"
                    class="q-ml-xs"
                  >
                    <q-tooltip
                      content-style="font-size: 0.9em; white-space: pre-line;"
                      :delay="1000"
                    >
                      {{ props.row.partner.name }}
                    </q-tooltip>
                  </q-icon>
                </q-td>
              </template>
              <template v-slot:body-cell-service="props">
                <q-td :props="props">
                  {{ props.row.service.text }}
                  <q-tooltip
                    content-style="font-size: 0.9em; white-space: pre-line;"
                    :delay="1000"
                  >
                    {{ props.row.service.tooltip }}
                  </q-tooltip>
                </q-td>
              </template>
            </q-table>
            <q-table
              v-else
              row-key="number"
              flat
              dense
              hide-bottom
              virtual-scroll
              separator="cell"
              class="mainTable"
              selection="none"
              :data="requests"
              :columns="mainTable"
              :selected="[activeRequest === undefined ? {number: 0} : activeRequest]"
              :pagination.sync="pagination"
              :rows-per-page-options="[0]"
              :virtual-scroll-sticky-size-start="28"
              :virtual-scroll-item-size="37"
              @row-dblclick.prevent="onRequestDblClick"
            >
              <template v-slot:body-cell-icons="props">
                <q-td
                  :props="props"
                  :data-number="props.row.number"
                >
                  <q-icon
                    :name="states[props.row.state.current].icon"
                    :color="states[props.row.state.current].color"
                    size="1em"
                  >
                    <q-tooltip
                      content-style="font-size: 0.9em; white-space: pre-line;"
                      :delay="1000"
                    >
                      {{ states[props.row.state.current].tooltip }}
                    </q-tooltip>
                  </q-icon>
                  <q-icon
                    v-if="props.row.state.sync1C === null"
                    :name="states.sync1C.icon"
                    :color="states.sync1C.color"
                    size="1em"
                    class="q-ml-xs"
                  >
                    <q-tooltip
                      content-style="font-size: 0.9em; white-space: pre-line;"
                      :delay="1000"
                    >
                      {{ states.sync1C.tooltip }}
                    </q-tooltip>
                  </q-icon>
                  <q-icon
                    v-if="props.row.partner.id !== null"
                    :name="states.partner.icon"
                    :color="states.partner.color"
                    size="1em"
                    class="q-ml-xs"
                  >
                    <q-tooltip
                      content-style="font-size: 0.9em; white-space: pre-line;"
                      :delay="1000"
                    >
                      {{ props.row.partner.name }}
                    </q-tooltip>
                  </q-icon>
                  <q-icon
                    v-if="props.row.state.wait"
                    :name="states.wait.icon"
                    :color="states.wait.color"
                    size="1em"
                    class="q-ml-xs"
                  >
                    <q-tooltip
                      content-style="font-size: 0.9em; white-space: pre-line;"
                      :delay="1000"
                    >
                      {{ states.wait.tooltip }}
                      <span v-if="props.row.waitTo">{{ ` до ${props.row.waitTo}` }}</span>
                    </q-tooltip>
                  </q-icon>
                </q-td>
              </template>
              <template v-slot:body-cell-number="props">
                <q-td :props="props">
                  {{ props.row.number }}
                  <q-tooltip
                    v-if="props.row.state.sync1C !== null"
                    content-style="font-size: 0.9em; white-space: pre-line;"
                    :delay="1000"
                  >
                    {{ props.row.state.sync1C }}
                  </q-tooltip>
                </q-td>
              </template>
              <template v-slot:body-cell-service="props">
                <q-td :props="props">
                  {{ props.row.service.text }}
                  <q-tooltip
                    content-style="font-size: 0.9em; white-space: pre-line;"
                    :delay="1000"
                  >
                    {{ props.row.service.tooltip }}
                  </q-tooltip>
                </q-td>
              </template>
              <template v-slot:body-cell-contragent="props">
                <q-td :props="props">
                  {{ props.row.contragent.text }}
                  <q-tooltip
                    content-style="font-size: 0.9em; white-space: pre-line;"
                    :delay="1000"
                  >
                    {{ props.row.contragent.tooltip }}
                  </q-tooltip>
                </q-td>
              </template>
              <template v-slot:body-cell-division="props">
                <q-td :props="props">
                  {{ props.row.division.text }}
                  <q-tooltip
                    content-style="font-size: 0.9em; white-space: pre-line;"
                    :delay="1000"
                  >
                    {{ props.row.division.tooltip }}
                  </q-tooltip>
                </q-td>
              </template>
              <template v-slot:body-cell-engineer="props">
                <q-td :props="props">
                  {{ props.row.engineer.text }}
                  <q-tooltip
                    v-if="props.row.engineer.tooltip"
                    content-style="font-size: 0.9em; white-space: pre-line;"
                    :delay="1000"
                  >
                    {{ props.row.engineer.tooltip }}
                  </q-tooltip>
                </q-td>
              </template>
              <template v-slot:body-cell-time="props">
                <q-td :props="props">
                  <div class="q-pa-xs">
                    <q-linear-progress
                      :value="props.row.time.reactRate"
                      :indeterminate="props.row.time.reactRate === null"
                      :style="`color: ${props.row.time.reactColor}`"
                    />
                    <q-linear-progress
                      class="q-mt-xs"
                      :value="props.row.time.fixRate"
                      :indeterminate="props.row.time.fixRate === null"
                      :style="`color: ${props.row.time.fixColor}`"
                    />
                    <q-linear-progress
                      class="q-mt-xs"
                      :value="props.row.time.repairRate"
                      :indeterminate="props.row.time.repairRate === null"
                      :style="`color: ${props.row.time.repairColor}`"
                    />
                  </div>
                  <q-tooltip
                    v-if="props.row.time.tooltip"
                    content-style="font-size: 0.9em; white-space: pre-line;"
                    :delay="1000"
                  >
                    {{ props.row.time.tooltip }}
                  </q-tooltip>
                </q-td>
              </template>
            </q-table>
            <q-menu
              ref="menu"
              target=".mainTable .q-virtual-scroll__content"
              touch-position
              context-menu
              auto-close
              @before-show="showContextMenu"
              @hide="hideContextMenu"
            >
              <q-list dense>
                <q-item
                  v-show="!syncedState"
                  v-ripple
                  dense
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.sync1C.icon"
                      :color="states.sync1C.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Не синхронизировано с 1С
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="selectedTab !== 'planned'"
                  v-ripple
                  dense
                  clickable
                  @click="doOpen"
                >
                  <q-item-section side>
                    <q-icon
                      name="fas fa-envelope-open-text"
                      color="info"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Открыть заявку
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canAccept"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doAccept"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.accepted.icon"
                      :color="states.accepted.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Принять заявку
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canFix"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doFix"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.fixed.icon"
                      :color="states.fixed.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Работоспособность восстановлена
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canRepair"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doRepair"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.repaired.icon"
                      :color="states.repaired.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Работы завершены
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canClose"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doClose"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.closed.icon"
                      :color="states.closed.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Подтвердить закрытие
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canDeny"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doDeny"
                >
                  <q-item-section side>
                    <q-icon
                      name="fas fa-undo"
                      color="negative"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Отменить закрытие
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canUnCancel"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doUnCancel"
                >
                  <q-item-section side>
                    <q-icon
                      name="fas fa-undo"
                      color="negative"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Открыть повторно
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canReopen"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doReopen"
                >
                  <q-item-section side>
                    <q-icon
                      name="fas fa-undo"
                      color="negative"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Открыть повторно
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canCancel"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doCancel"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.canceled.icon"
                      :color="states.canceled.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Отменить заявку
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canChangePartner"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState || waitState"
                  @click="doChangePartner"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.partner.icon"
                      :color="states.partner.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Назначить партнёра
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canWaitOn"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState"
                  @click="doWaitOn"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.wait.icon"
                      :color="states.wait.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Приостановить выполнение
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="canWaitOff"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState"
                  @click="doWaitOff"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.wait.icon"
                      :color="states.wait.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Возобновить выполнение
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="selectedTab != 'planned'"
                  v-ripple
                  dense
                  clickable
                  :disable="!syncedState"
                  @click="doAddComment"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.comment.icon"
                      :color="states.comment.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Добавить комментарий
                  </q-item-section>
                </q-item>
                <q-item
                  v-show="selectedTab === 'planned'"
                  v-ripple
                  dense
                  clickable
                  :disabled="!canDoNow"
                  @click="doDoNow"
                >
                  <q-item-section side>
                    <q-icon
                      :name="states.received.icon"
                      :color="states.received.color"
                      size="1em"
                    />
                  </q-item-section>
                  <q-item-section>
                    Взять сейчас
                  </q-item-section>
                </q-item>
              </q-list>
            </q-menu>
          </template>
        </q-splitter>
      </q-page>
    </q-page-container>
    <main-footer />
    <q-dialog
      v-model="showFilterSetup"
      no-backdrop-dismiss
    >
      <filter-dialog
        :filter="filter"
        @ok="setFilter"
      />
    </q-dialog>
    <q-dialog
      v-model="showMessageSettings"
      no-backdrop-dismiss
    >
      <message-settings-dialog
        @ok="showMessageSettings = false"
      />
    </q-dialog>
    <q-dialog
      v-model="showRequest"
      no-backdrop-dismiss
      @hide="activeRequest = { number: 0 }"
    >
      <request
        :number="activeRequest.number"
        @close="onUpdateRequest"
        @cancel="showRequest = false"
      />
    </q-dialog>
  </q-layout>
</template>

<script>
import MainHeader from '../components/MainHeader';
import MainFooter from '../components/MainFooter';
import FilterDialog from '../components/FilterDialog';
import MessageSettingsDialog from '../components/MessageSettingsDialog';
import Request from '../components/Request';
import CustomPrompt from '../components/CustomPrompt';
import CustomPromptSolution from '../components/CustomPromptSolution';
import CustomPromptWithDate from '../components/CustomPromptWithDate';
import CustomPromptWithSelect from '../components/CustomPromptWithSelect';

const tabs = {
  received: {
    icon: 'far fa-bell',
    label: 'Новые',
    badgeColor: 'negative',
    show: 0,
    total: 0,
    states: ['preReceived', 'received'],
  },
  accepted: {
    icon: 'fas fa-hammer',
    label: 'В работе',
    badgeColor: 'accent',
    show: 0,
    total: 0,
    states: ['accepted', 'fixed'],
  },
  repaired: {
    icon: 'fas fa-tools',
    label: 'Ждут закрытия',
    badgeColor: 'info',
    show: 0,
    total: 0,
    states: ['repaired'],
  },
  closed: {
    icon: 'fas fa-check-double',
    label: 'Закрытые',
    badgeColor: 'positive',
    show: 0,
    total: 0,
    states: ['closed'],
  },
  canceled: {
    icon: 'far fa-bell-slash',
    label: 'Отменённые',
    badgeColor: 'positive',
    show: 0,
    total: 0,
    states: ['canceled'],
  },
  planned: {
    icon: 'far fa-calendar-alt',
    label: 'Плановые',
    badgeColor: 'info',
    show: 0,
    total: 0,
    states: ['planned'],
  },
};

const mainTable = [
  {
    name: 'icons',
    label: '',
    field: 'state',
    align: 'left',
  }, {
    name: 'number',
    label: 'Номер',
    field: 'number',
    align: 'right',
    sortable: true,
  }, {
    name: 'level',
    label: 'Уровень',
    field: 'slaLevel',
    align: 'left',
  }, {
    name: 'service',
    label: 'Услуга',
    field: 'service',
    align: 'left',
  }, {
    name: 'received',
    label: 'Поступила',
    field: 'createdAt',
    align: 'left',
  }, {
    name: 'repair',
    label: 'Выполнить до',
    field: 'repairBefore',
    align: 'left',
  }, {
    name: 'contragent',
    label: 'Заказчик',
    field: 'contragent',
    align: 'left',
    style: 'max-width: 150px; overflow: hidden; text-overflow: ellipsis;',
  }, {
    name: 'division',
    label: 'Подразделение',
    field: 'division',
    align: 'left',
    style: 'max-width: 250px; overflow: hidden; text-overflow: ellipsis;',
  }, {
    name: 'engineer',
    label: 'Ответственный',
    field: 'engineer',
    align: 'left',
  }, {
    name: 'time',
    label: 'Осталось',
    field: 'time',
    align: 'center',
  },
];

const plannedTable = [
  {
    name: 'icons',
    label: '',
    field: 'state',
    align: 'left',
  }, {
    name: 'level',
    label: 'Уровень',
    field: 'slaLevel',
    align: 'left',
  }, {
    name: 'service',
    label: 'Услуга',
    field: 'service',
    align: 'left',
  }, {
    name: 'date',
    label: 'Дата',
    field: 'date',
    align: 'left',
  }, {
    name: 'contragent',
    label: 'Заказчик',
    field: 'contragent',
    align: 'left',
    style: 'max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: pre-line;',
  }, {
    name: 'problem',
    label: 'Задача',
    field: 'problem',
    align: 'left',
    style: 'white-space: pre-line;',
  },
];

const states = {
  preReceived: {
    icon: 'fas fa-bell',
    color: 'negative',
    tooltip: 'Получена',
    tab: 'received',
  },
  received: {
    icon: 'fas fa-bell',
    color: 'negative',
    tooltip: 'Получена',
    tab: 'received',
  },
  accepted: {
    icon: 'fas fa-hammer',
    color: 'info',
    tooltip: 'Принята в работу',
    tab: 'accepted',
  },
  fixed: {
    icon: 'fas fa-tape',
    color: 'info',
    tooltip: 'Работоспособность восстановлена',
    tab: 'accepted',
  },
  repaired: {
    icon: 'fas fa-tools',
    color: 'positive',
    tooltip: 'Работы завершены',
    tab: 'repaired',
  },
  closed: {
    icon: 'fas fa-check',
    color: 'positive',
    tooltip: 'Закрыта',
    tab: 'closed',
  },
  canceled: {
    icon: 'fas fa-bell-slash',
    color: 'warning',
    tooltip: 'Отменена',
    tab: 'canceled',
  },
  planned: {
    icon: 'fas fa-calendar-alt',
    color: 'info',
    tooltip: 'Плановая',
    tab: 'planned',
  },
  wait: {
    icon: 'fas fa-stopwatch',
    color: 'accent',
    tooltip: 'Приостановлена',
  },
  sync1C: {
    icon: 'fas fa-exclamation-triangle',
    color: 'warning',
    tooltip: 'Нет синхронизации с 1С',
  },
  partner: {
    icon: 'fas fa-hands-helping',
    color: 'info',
    tooltip: 'Передана партнёру',
  },
  comment: {
    icon: 'fas fa-comment-medical',
    color: 'grey',
    tooltip: 'Комментарий',
  },
};

export default {
  name: 'TechSupport',
  components: {
    MainHeader,
    MainFooter,
    FilterDialog,
    MessageSettingsDialog,
    Request,
  },
  data() {
    return {
      miniState: true,
      drawerForceOpen: false,
      showFilterSetup: false,
      showMessageSettings: false,
      filter: {
        contragent: null,
        contract: null,
        division: null,
        divisionType: null,
        partner: null,
        excludedServices: [],
        text: '',
        interval: '3m',
        fromDate: null,
        toDate: null,
      },
      splitter: 130,
      selectedTab: 'received',
      tabs,
      mainTable,
      plannedTable,
      pagination: {
        rowsPerPage: 0,
      },
      states,
      requests: [],
      activeRequest: { number: 0 },
      allowedActions: {},
      rateInterval: null,
      showRequest: false,
      showWaitOn: true,
      lockNumber: false,
    };
  },

  computed: {
    filterIconColor() {
      return this.filter.contragent !== null || this.filter.contract !== null
        || this.filter.division !== null || this.filter.divisionType !== null
        || this.filter.partner !== null || this.filter.excludedServices.length !== 0
        || this.filter.text !== '' || this.filter.fromDate !== null || this.filter.toDate !== null
        ? 'accent'
        : 'black';
    },

    isClient() {
      return !this.$store.state.auth.isAuthorized
        || this.$store.getters['auth/payload'].rights === 'client';
    },

    canAccept() {
      return this.canDoAction('accept')
        && this.$store.getters['auth/payload'].partner === this.activeRequest.partner.id;
    },

    canChangePartner() {
      return this.canDoAction('change_partner') && this.activeRequest.hasPartners;
    },

    canFix() {
      return this.canDoAction('fix')
        && this.$store.getters['auth/payload'].partner === this.activeRequest.partner.id;
    },

    canRepair() {
      return this.canDoAction('repair')
        && this.$store.getters['auth/payload'].partner === this.activeRequest.partner.id;
    },

    canClose() {
      return this.canDoAction('close');
    },

    canDeny() {
      return this.canDoAction('deny');
    },

    canCancel() {
      return this.canDoAction('cancel');
    },

    canUnCancel() {
      return this.canDoAction('unCancel');
    },

    canReopen() {
      return this.canDoAction('reopen');
    },

    canDoNow() {
      return this.canDoAction('doNow') && this.activeRequest.state.canGet
        && (this.$store.getters['auth/payload'].rights !== 'partner'
          || this.activeRequest.partners.includes(this.$store.getters['auth/payload'].partner));
    },

    canWaitOn() {
      return this.canDoAction('waitOn') && !this.waitState;
    },

    canWaitOff() {
      return this.canDoAction('waitOff') && this.waitState;
    },

    syncedState() {
      return this.activeRequest.number !== 0
        && (this.activeRequest.state.current === 'planned'
          || this.activeRequest.state.sync1C !== null);
    },

    waitState() {
      return this.activeRequest.number !== 0 && this.activeRequest.state.wait;
    },
  },

  mounted() {
    const filter = localStorage.getItem('filter');
    if (filter !== null) {
      this.filter = JSON.parse(filter);
    }
    this.getAllowedActions();
    this.refreshRequests();
    this.rateInterval = setInterval(this.updateRates, 1000);
  },

  beforeDestroy() {
    if (this.rateInterval !== null) {
      clearInterval(this.rateInterval);
      this.rateInterval = null;
    }
  },

  methods: {
    onChangeTab() {
      this.requests = [];
      this.refreshRequests();
    },

    refreshRequests() {
      this.getCounts();
      if (this.selectedTab === 'planned') {
        this.getPlannedRequests();
      } else {
        this.getRequests();
      }
    },

    onUpdateRequest() {
      this.showRequest = false;
      this.activeRequest = { number: 0 };
      this.requests = [];
      this.refreshRequests();
    },

    async updateRates() {
      const numbers = this.requests
        .filter(r => r.time !== undefined
          && (r.time.reactRate === null || r.time.fixRate === null || r.time.repairRate === null))
        .slice(0, 10)
        .map(r => r.number);
      if (numbers.length === 0) {
        return;
      }
      const rates = await this.$jsonRPC('Request::getRates', { numbers }, true);
      Object.keys(rates).forEach(number => {
        const req = this.requests.find(el => +el.number === +number);
        if (req === undefined) {
          return;
        }
        req.time.reactRate = rates[number].reactRate;
        req.time.fixRate = rates[number].fixRate;
        req.time.repairRate = rates[number].repairRate;
        const reactColor = Math.max(0, Math.round(120 * (1 - req.time.reactRate)));
        const fixColor = Math.max(0, Math.round(120 * (1 - req.time.fixRate)));
        const repairColor = Math.max(0, Math.round(120 * (1 - req.time.repairRate)));
        req.time.reactColor = `hsl(${reactColor},100%,40%)`;
        req.time.fixColor = `hsl(${fixColor},100%,40%)`;
        req.time.repairColor = `hsl(${repairColor},100%,40%)`;
      });
    },

    setFilter(filter) {
      this.filter = filter;
      this.showFilterSetup = false;
      this.refreshRequests();
    },

    showContextMenu(evt) {
      const needle = evt.target.closest('tr').cells[0].dataset.number;
      this.activeRequest = this.requests.find(el => +el.number === +needle);
      this.lockNumber = false;
    },

    hideContextMenu() {
      if (!this.lockNumber) {
        this.activeRequest = { number: 0 };
      }
    },

    async getAllowedActions() {
      this.allowedActions = await this.$jsonRPC('User::getAllowedActions', {});
    },

    async getCounts() {
      const counts = await this.$jsonRPC('Request::getCounts', { filter: this.filter });
      Object.values(tabs).forEach(tab => {
        tab.show = 0;
        tab.total = 0;
      });
      Object.keys(counts.total).forEach(state => {
        this.tabs[this.states[state].tab].total += counts.total[state];
      });
      Object.keys(counts.filtered).forEach(state => {
        this.tabs[this.states[state].tab].show += counts.filtered[state];
      });
      const plannedCounts = await this.$jsonRPC('PlannedRequest::getCounts', { filter: this.filter });
      this.tabs.planned.total += plannedCounts.total;
      this.tabs.planned.show += plannedCounts.filtered;
    },

    async getRequests() {
      const requests = await this.$jsonRPC(
        'Request::getList',
        { filter: this.filter, states: this.tabs[this.selectedTab].states },
      );
      this.requests = requests
        .sort((a, b) => a.number - b.number)
        .map(req => {
          let timeTooltip;
          if (req.time.fixedAt === null) {
            req.time.fixedAt = req.time.repairedAt;
          }
          if (req.state.current === 'canceled') {
            timeTooltip = 'Заявка отменена';
            req.time.reactRate = 0;
            req.time.fixRate = 0;
            req.time.repairRate = 0;
          } else {
            const createdTooltip = `Поступила ${req.time.createdAt}`;
            let fixedTooltip;
            if (req.time.fixedAt !== null) {
              fixedTooltip = `\nВосстановлена ${req.time.fixedAt}`;
            } else if (req.wait) {
              fixedTooltip = `\nПриостановлена ${req.time.waitFrom}`;
            } else {
              fixedTooltip = `\nВосстановить до ${req.time.fixBefore}`;
            }
            let repairedTooltip;
            if (req.time.repairedAt !== null) {
              repairedTooltip = `\nЗавершена ${req.time.repairedAt}`;
            } else if (!req.wait) {
              repairedTooltip = `\nЗавершить до ${req.time.repairBefore}`;
            } else if (req.time.fixedAt === null) {
              repairedTooltip = '';
            } else {
              repairedTooltip = `\nПриостановлена ${req.time.waitFrom}`;
            }
            timeTooltip = `${createdTooltip}${fixedTooltip}${repairedTooltip}`;
          }
          const reactColor = Math.max(0, Math.round(120 * (1 - req.time.reactRate)));
          const fixColor = Math.max(0, Math.round(120 * (1 - req.time.fixRate)));
          const repairColor = Math.max(0, Math.round(120 * (1 - req.time.repairRate)));
          return {
            number: req.number,
            state: req.state,
            slaLevel: req.slaLevel,
            partner: req.partner,
            waitTo: req.time.waitTo,
            service: {
              text: req.service.shortName,
              tooltip: `${req.service.name}\n${req.problem}`,
            },
            createdAt: req.time.createdAt,
            repairBefore: req.time.repairBefore,
            contragent: {
              text: req.contragent,
              tooltip: `Договор ${req.contract}`,
            },
            division: {
              text: req.division,
              tooltip: `Контактное лицо: ${req.contact.name}\nE-mail: ${req.contact.email}\nТелефон: ${req.contact.phone}`,
            },
            engineer: req.engineer === null
              ? { name: null, tooltip: null }
              : {
                text: req.engineer.shortName,
                tooltip: `${req.engineer.name}\nE-mail: ${req.engineer.email}\nТелефон: ${req.engineer.phone}`,
              },
            time: {
              reactRate: req.time.reactRate,
              reactColor: req.time.reactRate === null
                ? 'hsl(240,100%,80%)'
                : `hsl(${reactColor},100%,40%)`,
              fixRate: req.time.fixRate,
              fixColor: req.time.fixRate === null
                ? 'hsl(240,100%,80%)'
                : `hsl(${fixColor},100%,40%)`,
              repairRate: req.time.repairRate,
              repairColor: req.time.repairRate === null
                ? 'hsl(240,100%,80%)'
                : `hsl(${repairColor},100%,40%)`,
              tooltip: timeTooltip,
            },
            hasPartners: req.hasPartners,
          };
        });
    },

    async getPlannedRequests() {
      const requests = await this.$jsonRPC(
        'PlannedRequest::getList',
        { filter: this.filter },
      );
      this.requests = requests
        .sort((a, b) => a.date.localeCompare(b.date))
        .map(req => ({
          number: req.number,
          state: req.state,
          slaLevel: req.slaLevel,
          service: {
            text: req.service.shortName,
            tooltip: req.service.name,
          },
          partner: req.partner,
          date: req.date,
          contragent: req.contragent === req.division
            ? req.contragent
            : `${req.division},\n${req.contragent}`,
          problem: `${req.problem}\n${req.addProblem}`,
          partners: req.partners,
        }));
    },

    async getPartners(number) {
      const partners = await this.$jsonRPC('Request::getPartners', { number });
      return partners.map(el => ({ value: el.id, label: el.name }));
    },

    canDoAction(action) {
      if (!this.$store.state.auth.isAuthorized || this.activeRequest.number === 0) {
        return false;
      }
      const { rights } = this.$store.getters['auth/payload'];
      const state = this.activeRequest.state.current;
      return this.allowedActions[rights][state].includes(action);
    },

    async doWithParams(method, params, successMessage) {
      const reply = await this.$jsonRPC(method, params);
      if (reply.ok === 'ok') {
        this.$q.notify({
          position: 'top',
          timeout: 5000,
          type: 'info',
          message: successMessage,
        });
        this.onUpdateRequest();
      }
    },

    do(method, successMessage) {
      this.doWithParams(method, { number: this.activeRequest.number }, successMessage);
    },

    doAccept() {
      this.do('Request::accept', `Заявка №${this.activeRequest.number} принята в работу.`);
    },

    doFix() {
      this.do('Request::fix', `Заявка №${this.activeRequest.number}: работоспособность восстановлена.`);
    },

    doClose() {
      this.do('Request::close', `Заявка №${this.activeRequest.number} закрыта.`);
    },

    async doWithCause(header, question, method, successMessage) {
      this.lockNumber = true;
      let cause = await this.prompt(header, question);
      cause = cause === null ? '' : cause.trim();
      if (cause === '') {
        this.activeRequest = { number: 0 };
        return;
      }
      await this.doWithParams(
        method,
        { number: this.activeRequest.number, cause },
        successMessage,
      );
      this.activeRequest = { number: 0 };
    },

    doCancel() {
      this.doWithCause(
        'Отмена заявки',
        'Причина отмены',
        'Request::cancel',
        `Заявка №${this.activeRequest.number} отменена.`,
      );
    },

    doUnCancel() {
      this.doWithCause(
        'Повторное открытие заявки',
        'Причина повторного открытия',
        'Request::unCancel',
        `Заявка №${this.activeRequest.number} открыта повторно.`,
      );
    },

    async doReopen() {
      this.doWithCause(
        'Повторное открытие заявки',
        'Причина повторного открытия',
        'Request::reopen',
        `Заявка №${this.activeRequest.number} открыта повторно.`,
      );
    },

    async doDeny() {
      this.doWithCause(
        'Отказ в закрытии заявки',
        'Причина отказа',
        'Request::deny',
        `В закрытии заявки №${this.activeRequest.number} отказано.`,
      );
    },

    async doWaitOff() {
      this.doWithCause(
        'Возобновление выполнения заявки',
        'Комментарий',
        'Request::waitOff',
        `Выполнение заявки №${this.activeRequest.number} возобновлено.`,
      );
    },

    async doRepair() {
      this.lockNumber = true;
      const solution = await this.promptSolution();
      if (solution === null || solution.problem === '' || solution.solution === '') {
        this.activeRequest = { number: 0 };
        return;
      }
      await this.doWithParams(
        'Request::repair',
        { number: this.activeRequest.number, solution },
        `Заявка №${this.activeRequest.number}: работы завершены.`,
      );
      this.activeRequest = { number: 0 };
    },

    async doWaitOn() {
      this.lockNumber = true;
      const { text, date } = await this.promptWithDate(
        'Приостановка выполнения заявки',
        'Причина приостановки',
        'fas fa-question',
        'Дата возобновления работ',
      );
      const cause = text === null ? '' : text.trim();
      if (cause === '') {
        this.activeRequest = { number: 0 };
        return;
      }
      await this.doWithParams(
        'Request::waitOn',
        { number: this.activeRequest.number, cause, date },
        `Выполнение заявки №${this.activeRequest.number} приостановлено.`,
      );
      this.activeRequest = { number: 0 };
    },

    async doChangePartner() {
      const partners = await this.getPartners(this.activeRequest.number);
      partners
        .sort((a, b) => a.label.toLowerCase().localeCompare(b.label.toLowerCase()))
        .unshift({ value: null, label: 'Отменить назначение' });
      let currentPartner = partners.filter(el => el.value === this.activeRequest.partner.id);
      if (currentPartner.length === 0) {
        [currentPartner] = partners;
      } else {
        [currentPartner] = currentPartner;
      }
      this.lockNumber = true;
      const partner = await this.promptWithSelect(
        'Назначение заявки партнёру',
        'Партнёр',
        states.partner.icon,
        partners,
        currentPartner,
      );
      if (partner === null || partner.value === this.activeRequest.partner.id) {
        this.activeRequest = { number: 0 };
        return;
      }
      await this.doWithParams(
        'Request::setPartner',
        { number: this.activeRequest.number, partnerId: partner.value },
        `Изменено назначение заявки №${this.activeRequest.number} партнёру.`,
      );
      this.activeRequest = { number: 0 };
    },

    async doAddComment() {
      this.lockNumber = true;
      let comment = await this.prompt('Комментарий к заявке', 'Текст комментария');
      comment = comment === null ? '' : comment.trim();
      if (comment === '') {
        this.activeRequest = { number: 0 };
        return;
      }
      await this.doWithParams(
        'Request::addComment',
        { number: this.activeRequest.number, comment },
        `Добавлен комментарий к заявке №${this.activeRequest.number}.`,
      );
      this.activeRequest = { number: 0 };
    },

    doDoNow() {
      this.do('PlannedRequest::doNow', 'Плановая заявка активирована.');
    },

    doOpen() {
      this.lockNumber = true;
      this.showRequest = true;
    },

    prompt(header = '', label = '', icon = 'fas fa-question') {
      return new Promise((resolve) => {
        this.$q.dialog({
          component: CustomPrompt,
          parent: this,
          header,
          label,
          icon,
          cancel: true,
          persistent: true,
        }).onOk(data => {
          resolve(data);
        }).onCancel(() => {
          resolve(null);
        });
      });
    },

    promptSolution() {
      return new Promise((resolve) => {
        this.$q.dialog({
          component: CustomPromptSolution,
          parent: this,
          cancel: true,
          persistent: true,
        }).onOk(data => {
          resolve(data);
        }).onCancel(() => {
          resolve(null);
        });
      });
    },

    promptWithDate(header = '', label = '', icon = 'fas fa-question', dateLabel = '') {
      return new Promise((resolve) => {
        this.$q.dialog({
          component: CustomPromptWithDate,
          parent: this,
          header,
          label,
          dateLabel,
          icon,
          cancel: true,
          persistent: true,
        }).onOk(data => {
          resolve(data);
        }).onCancel(() => {
          resolve({ text: null, date: null });
        });
      });
    },

    promptWithSelect(header = '', label = '', icon = 'fas fa-question', options = [], current = null) {
      return new Promise((resolve) => {
        this.$q.dialog({
          component: CustomPromptWithSelect,
          parent: this,
          header,
          label,
          options,
          current,
          icon,
          cancel: true,
          persistent: true,
        }).onOk(data => {
          resolve(data);
        }).onCancel(() => {
          resolve(null);
        });
      });
    },

    onRequestDblClick(evt) {
      const needle = evt.target.closest('tr').cells[0].dataset.number;
      this.activeRequest = this.requests.find(el => +el.number === +needle);
      this.doOpen();
    },

    newRequest() {
      this.activeRequest = { number: 0 };
      this.doOpen();
    },

    messageSettings() {
    },
  },
};
</script>

<style lang="scss">
  .pin {
    color: $grey-6;
    transform: rotate(45deg);
    transition: transform ease-in-out 0.5s;
    &:hover {
      transform: rotate(0deg);
    }
    &.pinned {
      color: $grey-9;
      transform: rotate(0deg);
    }
  }
  .q-tab__content {
    width: calc( 100% - 16px );
  }
  .mainTable,
  .plannedTable {
    height: calc( 100vh - 106px );
    thead th {
      background-color: $grey-8;
      color: white;
      font-weight: 700;
      text-align: center;
      position: sticky;
      z-index: 1;
      top: 0;
    }
    tbody td {
      cursor: pointer;
      -moz-user-select: none;
      -webkit-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }
  }
</style>
