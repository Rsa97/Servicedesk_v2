<template>
  <q-card
    class="q-dialog-plugin"
    style="width: 700px; max-width: 700px;"
  >
    <q-card-section class="bg-primary text-white text-center text-h6 q-pa-xs">
      <q-toolbar>
        <q-toolbar-title>
          {{ number === 0 ? 'Новая заявка' : `Заявка ${number}` }}
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
    <q-form>
      <q-tabs
        v-show="number !== 0"
        v-model="tab"
        dense
        inline-label
        class="text-primary bg-grey-2"
      >
        <q-tab
          name="basic"
          icon="fas fa-bell"
          label="Основное"
        />
        <q-separator vertical />
        <q-tab
          name="solution"
          icon="fas fa-tools"
          label="Решение"
        />
        <q-separator vertical />
        <q-tab
          name="log"
          icon="fas fa-align-left"
          label="Журнал"
        />
        <q-separator vertical />
        <q-tab
          name="docs"
          icon="far fa-file"
          label="Документы"
        />
        <q-separator vertical />
        <q-tab
          v-show="false"
          name="works"
          label="Работы"
        />
      </q-tabs>
      <q-card-section v-show="tab === 'basic'">
        <q-select
          ref="selectContragent"
          v-model="selectedContragent"
          use-input
          hide-selected
          fill-input
          dense
          options-dense
          input-debounce="0"
          label="Заказчик"
          behaviuor="menu"
          :options="contragentsFiltered"
          :readonly="contragentsList.length <= 1"
          @filter="filterContragents"
          @input="onContragentChanged"
        >
          <template v-slot:no-option>
            <q-item>
              <q-item-section class="text-grey">
                Нет совпадений
              </q-item-section>
            </q-item>
          </template>
        </q-select>
        <div class="row">
          <q-select
            ref="selectContract"
            v-model="selectedContract"
            dense
            options-dense
            label="Договор"
            behaviuor="menu"
            class="col q-pr-sm"
            :options="contractsList"
            :readonly="contractsList.length <= 1"
            @input="onContractChanged"
          />
          <q-select
            ref="selectDivision"
            v-model="selectedDivision"
            use-input
            hide-selected
            fill-input
            dense
            options-dense
            input-debounce="0"
            label="Подразделение"
            behaviuor="menu"
            class="col q-pl-sm"
            :options="divisionsFiltered"
            :readonly="divisionsList.length <= 1"
            @filter="filterDivisions"
            @input="onDivisionChanged"
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey">
                  Нет совпадений
                </q-item-section>
              </q-item>
            </template>
          </q-select>
        </div>
        <div class="row">
          <q-select
            ref="selectService"
            v-model="selectedService"
            use-input
            hide-selected
            fill-input
            dense
            options-dense
            input-debounce="0"
            label="Услуга"
            behaviuor="menu"
            class="col q-pr-sm"
            :options="servicesFiltered"
            :option-label="opt => `${opt.name} (${opt.label})`"
            :readonly="servicesList.length <= 1"
            @filter="filterServices"
            @input="onServiceChanged"
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey">
                  Нет совпадений
                </q-item-section>
              </q-item>
            </template>
          </q-select>
          <q-select
            ref="selectLevel"
            v-model="selectedLevel"
            dense
            options-dense
            label="Уровень критичности"
            behaviuor="menu"
            class="col q-pl-sm"
            :options="levelsList"
            :readonly="levelsList.length <= 1"
            @input="onLevelChanged"
          />
        </div>
        <q-input
          v-model="problem"
          type="textarea"
          dense
          outlined
          label="Описание проблемы"
          class="q-py-xs"
          input-style="resize: none;"
        />
        <div class="row">
          <q-select
            v-model="selectedEquipment"
            use-input
            hide-selected
            fill-input
            dense
            options-dense
            clearable
            input-debounce="0"
            label="Сервисный номер"
            behaviuor="menu"
            class="col q-pr-sm"
            :options="equipmentFiltered"
            :readonly="!canChangeEquipment || equipmentList.length === 0"
            @filter="filterEquipment"
          >
            <template v-slot:no-option>
              <q-item>
                <q-item-section class="text-grey">
                  Нет совпадений
                </q-item-section>
              </q-item>
            </template>
          </q-select>
          <q-input
            v-model="selectedEquipmentSerialNumber"
            dense
            label="Серийный номер"
            class="col q-pl-sm"
            :readonly="true"
          />
        </div>
        <q-input
          v-model="selectedEquipmentType"
          dense
          label="Тип оборудования"
          :readonly="true"
        />
        <div class="row">
          <q-input
            v-model="createdAt"
            dense
            label="Дата поступления"
            class="col q-pr-sm"
            :readonly="true"
          />
          <q-input
            v-model="repairBefore"
            dense
            options-dense
            label="Срок исполнения"
            class="col q-pl-sm"
            :readonly="true"
          />
        </div>
        <div class="row">
          <q-select
            v-model="selectedPartner"
            dense
            options-dense
            clearable
            label="Назначена партнёру"
            behaviuor="menu"
            class="col q-pr-sm"
            :options="partnersList"
            :readonly="!canChangePartner || partnersList.length === 0"
          />
          <q-select
            v-model="selectedContact"
            dense
            options-dense
            label="Ответственное лицо"
            behaviuor="menu"
            class="col q-pl-sm"
            :options="contactsList"
            :readonly="!canChangeContact || contactsList.length === 0"
          />
        </div>
        <div class="row">
          <q-input
            v-model="selectedContactEmail"
            dense
            label="Электронная почта"
            class="col q-pr-sm"
            :readonly="true"
          />
          <q-input
            v-model="selectedContactPhone"
            dense
            options-dense
            label="Телефон"
            class="col q-pl-sm"
            :readonly="true"
          />
        </div>
        <q-input
          v-model="address"
          dense
          options-dense
          label="Адрес нахождения оборудования"
          :readonly="true"
        />
      </q-card-section>
      <q-card-section v-show="tab === 'log'">
        <div
          style="height: 490px; border-bottom: 1px solid; overflow-y: scroll;"
        >
          <q-list dense>
            <q-item
              v-for="event in events"
              :key="event.id"
            >
              <q-item-section>
                <q-item-label
                  lines="1"
                  class="q-pt-sm"
                >
                  <span class="text-primary">
                    {{ `${event.time}:` }}
                  </span>
                  <span
                    class="text-primary"
                    style="font-weight: bold;"
                  >
                    {{ event.user }}
                  </span>
                </q-item-label>
                <q-item-label
                  v-if="event.log"
                  class="q-pl-md"
                  style="font-weight: 500;"
                >
                  {{ event.log }}
                </q-item-label>
                <q-item-label
                  v-if="event.comment"
                  class="q-pl-md"
                >
                  {{ event.comment }}
                </q-item-label>
                <q-item-label
                  v-if="event.document"
                  class="q-pl-md"
                >
                  <q-icon
                    :name="event.document.icon"
                    size="xs"
                  />
                  <a
                    v-if="event.document.id"
                    href="#"
                    @click.prevent="getDoc(event.document)"
                  >
                    {{ event.document.name }}
                  </a>
                  <span v-else>
                    {{ event.document.name }}
                  </span>
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </div>
        <q-input
          v-model="comment"
          dense
          label="Добавить комментарий"
          :readonly="!canComment"
        >
          <template v-slot:append>
            <q-btn
              v-if="comment !== ''"
              icon="fas fa-comment-medical"
              dense
              round
              flat
              :disabled="!canComment"
              @click="doAddComment"
            />
          </template>
        </q-input>
      </q-card-section>
      <q-card-section v-show="tab === 'solution'">
        <q-input
          v-model="solution.problem"
          type="textarea"
          dense
          outlined
          label="Обнаруженная проблема"
          class="q-py-md"
          input-style="resize: none; height: 144px;"
          :readonly="true"
        />
        <q-input
          v-model="solution.solution"
          type="textarea"
          dense
          outlined
          label="Решение"
          class="q-pb-md"
          input-style="resize: none; height: 145px;"
          :readonly="true"
        />
        <q-input
          v-model="solution.recomendation"
          type="textarea"
          dense
          outlined
          label="Рекомендации клиенту"
          input-style="resize: none; height: 145px;"
          :readonly="true"
        />
      </q-card-section>
      <q-card-section v-show="tab === 'docs'">
        <div style="height: 490px; white-space: pre-wrap; overflow-y: scroll">
          <q-table
            v-if="documents.length > 0"
            row-key="id"
            flat
            dense
            hide-bottom
            :data="documents"
            :columns="documentsTable"
            :pagination.sync="pagination"
            :rows-per-page-options="[0]"
          >
            <template v-slot:body-cell-icon="props">
              <q-td :props="props">
                <q-icon
                  :name="props.row.document.icon"
                  size="xs"
                />
              </q-td>
            </template>
            <template v-slot:body-cell-size="props">
              <q-td :props="props">
                {{ props.row.document.size }}
              </q-td>
            </template>
            <template v-slot:body-cell-file="props">
              <q-td :props="props">
                <a
                  v-if="props.row.document.id"
                  href="#"
                  @click.prevent="getDoc(props.row.document)"
                >
                  {{ props.row.document.name }}
                </a>
                <span v-else>
                  {{ props.row.document.name }}
                </span>
              </q-td>
            </template>
          </q-table>
          <span v-else>
            Документов нет
          </span>
        </div>
        <q-file
          v-model="file"
          dense
          label="Добавить документ"
          :readonly="!canAddDocs"
        >
          <template v-slot:append>
            <q-btn
              v-show="file !== null"
              icon="fas fa-times"
              dense
              round
              flat
              :disabled="!canAddDocs"
              @click="file = null"
            />
            <q-btn
              v-show="file !== null"
              icon="fas fa-file-medical"
              dense
              round
              flat
              :disabled="!canAddDocs"
              @click="doAddDoc"
            />
          </template>
        </q-file>
      </q-card-section>
      <q-card-section v-show="tab === 'works'">
        <div style="height: 530px; white-space: pre-wrap;">
          Работы
        </div>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn
          v-show="number !== 0"
          color="negative"
          label="Отменить"
          icon="fas fa-times"
          size="sm"
          :disabled="!requestChanged"
          @click="undoChanges"
        />
        <q-btn
          v-show="number !== 0"
          color="positive"
          label="Изменить"
          icon="fas fa-check"
          size="sm"
          :disabled="!requestChanged"
          @click="doRequestChange"
        />
        <q-btn
          v-show="number === 0"
          color="positive"
          label="Отправить"
          icon="fas fa-bell"
          size="sm"
          @click="newRequest"
        />
      </q-card-actions>
    </q-form>
  </q-card>
</template>

<script>
import { date } from 'quasar';

const states = {
  received: 'Получена',
  accepted: 'Принята',
  fixed: 'Работоспособность восстановлена',
  repaired: 'Работа завершена',
  closed: 'Закрыта',
  canceled: 'Отменена',
};

const multipliers = ['', ' K', ' M', ' G', ' T', ' P'];

const mimeIcons = {
  text: 'fas fa-file-alt',
  video: 'fas fa-file-video',
  audio: 'fas fa-file-audio',
  word: 'fas fa-file-word',
  excel: 'fas fa-file-excel',
  pdf: 'fas fa-file-pdf',
  image: 'fas fa-file-image',
  archive: 'fas fa-file-archive',
  file: 'fas fa-file',
};

const documentsTable = [
  {
    name: 'icon',
    field: 'document.icon',
    label: '',
    align: 'center',
  }, {
    name: 'time',
    field: 'time',
    label: 'Время',
    align: 'left',
  }, {
    name: 'user',
    field: 'user',
    label: 'Пользователь',
    align: 'left',
  }, {
    name: 'size',
    field: 'document.size',
    label: 'Размер',
    align: 'left',
  }, {
    name: 'file',
    fiels: 'document.file',
    label: 'Файл',
    align: 'left',
  },
];

const levelsOrder = ['critical', 'high', 'medium', 'low', 'user'];

export default {
  name: 'Request',

  props: {
    number: {
      type: Number,
      default: 0,
    },
  },

  data() {
    return {
      changed: false,
      contragentsList: [],
      contragentsFiltered: [],
      selectedContragent: null,
      contractsList: [],
      selectedContract: null,
      divisionsList: [],
      divisionsFiltered: [],
      selectedDivision: null,
      servicesList: [],
      servicesFiltered: [],
      selectedService: null,
      initialService: null,
      levelsList: [],
      selectedLevel: null,
      initialLevel: null,
      problem: '',
      equipmentList: [],
      equipmentFiltered: [],
      selectedEquipment: null,
      initialEquipment: null,
      canChangeEquipment: false,
      partnersList: [],
      selectedPartner: null,
      initialPartner: null,
      canChangePartner: false,
      contactsList: [],
      selectedContact: null,
      initialContact: null,
      canChangeContact: false,
      createdAt: null,
      repairBefore: null,
      initialRepairBefore: null,
      tab: 'basic',
      comment: '',
      canComment: true,
      solution: {
        problem: null,
        solution: null,
        recomendation: null,
      },
      file: null,
      canAddDocs: true,
      events: [],
      documentsTable,
      pagination: { rowsPerPage: 0 },
    };
  },

  computed: {
    selectedEquipmentSerialNumber() {
      return this.selectedEquipment === null
        ? null
        : this.selectedEquipment.serialNumber;
    },

    selectedEquipmentType() {
      return this.selectedEquipment === null
        ? null
        : this.selectedEquipment.type;
    },

    selectedContactEmail() {
      return this.selectedContact === null
        ? null
        : this.selectedContact.email;
    },

    selectedContactPhone() {
      return this.selectedContact === null
        ? null
        : this.selectedContact.phone;
    },

    documents() {
      return this.events
        .filter(el => el.document !== null);
    },

    address() {
      if (this.selectedDivision !== null && this.selectedDivision.address !== null) {
        return this.selectedDivision.address;
      }
      if (this.selectedContract !== null) {
        return this.selectedContract.address;
      }
      return null;
    },

    requestChanged() {
      return (this.selectedService !== null
          && this.selectedService.value !== this.initialService)
        || (this.selectedLevel !== null && this.selectedLevel.value !== this.initialLevel)
        || (this.selectedEquipment === null && this.initialEquipment !== null)
        || (this.selectedEquipment !== null
          && this.selectedEquipment.value !== this.initialEquipment)
        || (this.selectedPartner === null && this.initialPartner !== null)
        || (this.selectedPartner !== null && this.selectedPartner.value !== this.initialPartner)
        || (this.selectedContact === null && this.initialContact !== null)
        || (this.selectedContact !== null && this.selectedContact.value !== this.initialContact);
    },
  },

  mounted() {
    if (this.number === 0) {
      this.fillContragents();
    } else {
      this.loadRequest();
    }
  },

  methods: {
    eventView(ev) {
      let log = null;
      let comment = null;
      let document = null;
      switch (ev.event) {
        case 'open':
          log = 'Заявка создана';
          break;
        case 'changeState':
          log = `Статус заявки изменён на '${states[ev.newState]}'`;
          if (ev.newState === 'canceled') {
            comment = `Причина отмены: ${ev.text}`;
          }
          break;
        case 'changeDate':
          log = `Срок завершения перенесён на ${ev.text}`;
          break;
        case 'changePartner':
          if (ev.text === null) {
            log = 'Отменено назначение заявки партнёру';
          } else {
            log = 'Заявка назначена партнёру';
            comment = ev.text;
          }
          break;
        case 'changeContact':
          log = 'Новое контактное лицо';
          comment = ev.text;
          break;
        case 'changeService':
          log = 'Услуга изменена';
          comment = ev.text;
          break;
        case 'comment':
          comment = ev.text;
          break;
        case 'onWait':
          log = 'Заявка поставлена в ожидание';
          comment = `Причина: ${ev.text}`;
          break;
        case 'offWait':
          log = 'Заявка снята с ожидания';
          comment = `Комментарий: ${ev.text}`;
          break;
        case 'unClose':
          log = 'Отказано в закрытии заявки';
          comment = `Причина: ${ev.text}`;
          break;
        case 'unCancel':
          log = 'Заявка открыта повторно';
          comment = `Причина: ${ev.text}`;
          break;
        case 'eqChange':
          log = 'Изменено оборудование';
          comment = ev.text;
          break;
        case 'addDocument':
          log = 'Добавлен документ';
          if (ev.document) {
            document = {
              id: ev.document.id,
              name: ev.document.name,
              size: this.formatSize(ev.document.size),
              icon: this.mimeIcon(ev.document.type),
            };
          }
          break;
        default:
      }
      return {
        id: ev.id,
        time: ev.time,
        user: ev.user,
        log,
        comment,
        document,
      };
    },

    formatSize(size) {
      let s = parseFloat(size);
      let m = 0;
      while (s > 1024) {
        s /= 1024;
        m += 1;
      }
      s = s.toPrecision(4);
      m = multipliers[m];
      return `${s}${m}`;
    },

    mimeIcon(mime) {
      const type = mime.split('/');
      switch (type[0]) {
        case 'text':
        case 'video':
        case 'audio':
        case 'image':
          return mimeIcons[type[0]];
        case 'application':
          switch (type[1]) {
            case 'pdf':
              return mimeIcons.pdf;
            case 'msword':
            case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.oasis.opendocument.text':
              return mimeIcons.word;
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.oasis.opendocument.spreadsheet':
              return mimeIcons.excel;
            case 'zip':
            case 'x-rar-compressed':
            case 'x-7z-compressed':
              return mimeIcons.archive;
            default:
              return mimeIcons.file;
          }
        default:
          return mimeIcons.file;
      }
    },

    sortByName(a, b) {
      return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
    },

    sortLevels(a, b) {
      return levelsOrder.indexOf(a.id) - levelsOrder.indexOf(b.id);
    },

    sortEquipment(a, b) {
      return a.serviceNumber.toLowerCase().localeCompare(b.serviceNumber.toLowerCase());
    },

    listMapper(el) {
      return {
        value: el.id,
        label: el.name,
        default: el.default === undefined ? false : el.default,
      };
    },

    listWithAddressMapper(el) {
      return {
        value: el.id,
        label: el.name,
        address: el.address,
        default: el.default === undefined ? false : el.default,
      };
    },

    servicesMapper(el) {
      return {
        value: el.id,
        label: el.shortName,
        name: el.name,
        default: el.default === undefined ? false : el.default,
        autoOnly: el.autoOnly,
      };
    },

    equipmentMapper(el) {
      return {
        value: el.id,
        label: `${el.serviceNumber} - ${el.manufacturer} ${el.model}`,
        serialNumber: el.serialNumber,
        type: `${el.type} / ${el.subType}`,
        default: el.default === undefined ? false : el.default,
      };
    },

    contactMapper(el) {
      return {
        value: el.id,
        label: el.name,
        email: el.email,
        phone: el.phone,
        default: el.default === undefined ? false : el.default,
      };
    },

    fillList(list, mapper = this.listMapper, sorter = this.sortByName, nodefault = false) {
      const filledList = list.sort(sorter).map(mapper);
      let selected = filledList.find(el => el.default);
      if (selected === undefined) {
        if (nodefault || filledList.length === 0) {
          selected = null;
        } else {
          [selected] = filledList;
        }
      }
      return [filledList, selected, selected === null ? null : selected.value];
    },

    async loadRequest() {
      const data = await this.$jsonRPC('Request::getRequest', { number: this.number });
      [this.contragentsList, this.selectedContragent] = this.fillList(data.contragents);
      [this.contractsList, this.selectedContract] = this.fillList(
        data.contracts,
        this.listWithAddressMapper,
      );
      [this.divisionsList, this.selectedDivision] = this.fillList(
        data.divisions,
        this.listWithAddressMapper,
      );
      [this.servicesList, this.selectedService, this.initialService] = this.fillList(
        data.services,
        this.servicesMapper,
      );
      [this.levelsList, this.selectedLevel, this.initialLevel] = this.fillList(
        data.levels,
        undefined,
        this.sortLevels,
      );
      [this.equipmentList, this.selectedEquipment, this.initialEquipment] = this.fillList(
        data.equipment,
        this.equipmentMapper,
        this.sortEquipment,
        true,
      );
      this.canChangeEquipment = data.canChangeEquipment;
      [this.partnersList, this.selectedPartner, this.initialPartner] = this.fillList(
        data.partners,
        undefined,
        undefined,
        true,
      );
      this.canChangePartner = data.canChangePartner;
      [this.contactsList, this.selectedContact, this.initialContact] = this.fillList(
        data.contacts,
        this.contactMapper,
      );
      this.problem = data.problem;
      this.createdAt = data.createdAt;
      this.repairBefore = data.repairBefore;
      this.initialRepairBefore = data.repairBefore;
      this.solution = {
        problem: data.solution.problem,
        solution: data.solution.solution,
        recomendation: data.solution.recomendation,
      };
      this.events = data.events
        .sort((a, b) => {
          const aTime = date.extractDate(a.time, 'DD.MM.YYYY HH:mm');
          const bTime = date.extractDate(b.time, 'DD.MM.YYYY HH:mm');
          if (aTime < bTime) {
            return -1;
          }
          if (aTime > bTime) {
            return 1;
          }
          return a.id - b.id;
        })
        .map(this.eventView);
    },

    undoChanges() {
      this.selectedService = this.servicesList.find(el => el.value === this.initialService);
      this.selectedLevel = this.levelsList.find(el => el.value === this.initialLevel);
      this.selectedEquipment = this.initialEquipment === null
        ? null
        : this.equipmentList.find(el => el.value === this.initialEquipment);
      this.selectedPartner = this.initialPartner === null
        ? null
        : this.partnersList.find(el => el.value === this.initialPartner);
      this.selectedContact = this.initialContact === null
        ? null
        : this.contactsList.find(el => el.value === this.initialContact);
      this.repairBefore = this.initialRepairBefore;
    },

    filterContragents(val, update) {
      if (val === '') {
        update(() => { this.contragentsFiltered = this.contragentsList; });
      }
      const needle = val.toLowerCase();
      update(
        () => {
          this.contragentsFiltered = this.contragentsList
            .filter(el => el.label.toLowerCase().indexOf(needle) > -1);
        },
      );
    },

    filterDivisions(val, update) {
      if (val === '') {
        update(() => { this.divisionsFiltered = this.divisionsList; });
      }
      const needle = val.toLowerCase();
      update(
        () => {
          this.divisionsFiltered = this.divisionsList
            .filter(el => el.label.toLowerCase().indexOf(needle) > -1);
        },
      );
    },

    filterServices(val, update) {
      if (val === '') {
        update(() => { this.servicesFiltered = this.servicesList; });
      }
      const needle = val.toLowerCase();
      update(
        () => {
          this.servicesFiltered = this.servicesList
            .filter(el => el.label.toLowerCase().indexOf(needle) > -1);
        },
      );
    },

    filterEquipment(val, update) {
      if (val === '') {
        update(() => { this.equipmentFiltered = this.equipmentList; });
      }
      const needle = val.toLowerCase();
      update(
        () => {
          this.equipmentFiltered = this.equipmentList
            .filter(el => el.label.toLowerCase().indexOf(needle) > -1);
        },
      );
    },

    async fillContragents() {
      const data = await this.$jsonRPC('Contragent::getAllowed', {});
      [this.contragentsList, this.selectedContragent] = this.fillList(
        data,
        this.listWithAddressMapper,
        undefined,
        true,
      );
      if (this.contragentsList.length === 1) {
        [this.selectedContragent] = this.contragentsList;
      }
      if (this.selectedContragent === null) {
        setTimeout(this.$refs.selectContragent.showPopup, 100);
      } else {
        this.onContragentChanged();
      }
    },

    onContragentChanged() {
      if (this.selectedContragent === null) {
        this.contractsList = [];
        this.selectedContract = null;
        this.onContractChanged();
      } else {
        this.fillContracts();
      }
    },

    async fillContracts() {
      const data = await this.$jsonRPC(
        'Contract::getAllowed',
        { contragent: this.selectedContragent.value },
      );
      [this.contractsList, this.selectedContract] = this.fillList(
        data,
        undefined,
        undefined,
        true,
      );
      if (this.contractsList.length === 1) {
        [this.selectedContract] = this.contractsList;
      }
      if (this.selectedContract === null) {
        setTimeout(this.$ref.selectContract.showPopup, 100);
      } else {
        this.onContractChanged();
      }
    },

    onContractChanged() {
      if (this.selectedContract === null) {
        this.divisionsList = [];
        this.selectedDivision = null;
        this.onDivisionChanged();
      } else {
        this.fillDivisions();
      }
    },

    async fillDivisions() {
      const data = await this.$jsonRPC(
        'Division::getAllowed',
        { contract: this.selectedContract.value },
      );
      [this.divisionsList, this.selectedDivision] = this.fillList(
        data,
        this.listWithAddressMapper,
        undefined,
        true,
      );
      if (this.divisionsList.length === 1) {
        [this.selectedDivision] = this.divisionsList;
      }
      if (this.selectedDivision === null) {
        setTimeout(this.$refs.selectDivision.showPopup, 100);
      } else {
        this.onDivisionChanged();
      }
    },

    onDivisionChanged() {
      if (this.selectedDivision === null) {
        this.servicesList = [];
        this.selectedService = null;
        this.equipmentList = [];
        this.selectedEquipment = null;
        this.partnersList = [];
        this.selectedPartner = null;
        this.contactsList = [];
        this.selectedContact = null;
        this.onServiceChanged();
      } else {
        this.fillServices();
        this.fillEquipment();
        this.fillPartners();
        this.fillContacts();
      }
    },

    async fillServices() {
      const data = await this.$jsonRPC(
        'Service::getAllowed',
        { division: this.selectedDivision.value },
      );
      [this.servicesList, this.selectedService] = this.fillList(
        data,
        this.servicesMapper,
        undefined,
        true,
      );
      if (this.servicesList.length === 1) {
        [this.selectedService] = this.servicesList;
      }
      if (this.selectedService === null) {
        setTimeout(this.$refs.selectService.showPopup, 100);
      } else {
        this.onServiceChanged();
      }
    },

    onServiceChanged() {
      if (this.selectedService === null) {
        this.levelsList = [];
        this.selectedLevel = null;
        this.onLevelChanged();
      } else {
        this.fillLevels();
      }
    },

    async fillLevels() {
      const data = await this.$jsonRPC(
        'Service::getLevels',
        { division: this.selectedDivision.value, service: this.selectedService.value },
      );
      [this.levelsList, this.selectedLevel] = this.fillList(
        data,
        undefined,
        this.sortLevels,
        true,
      );
      if (this.levelsList.length === 1) {
        [this.selectedLevel] = this.levelsList;
      }
      if (this.selectedLevel === null) {
        setTimeout(this.$refs.selectLevel.showPopup, 100);
      } else {
        this.onLevelChanged();
      }
    },

    async fillEquipment() {
      const data = await this.$jsonRPC(
        'Equipment::getAllowed',
        { division: this.selectedDivision.value },
      );
      [this.equipmentList, this.selectedEquipment] = this.fillList(
        data,
        this.equipmentMapper,
        this.sortEquipment,
        true,
      );
      this.canChangeEquipment = true;
    },

    async fillPartners() {
      const data = await this.$jsonRPC(
        'Partner::getAllowed',
        { division: this.selectedDivision.value },
      );
      [this.partnersList, this.selectedPartner] = this.fillList(data, undefined, undefined, true);
      this.canChangePartner = true;
    },

    async fillContacts() {
      const data = await this.$jsonRPC(
        'Contact::getAllowed',
        { division: this.selectedDivision.value },
      );
      [this.contactsList, this.selectedContact] = this.fillList(data, this.contactMapper);
      this.canChangeContact = true;
    },

    async onLevelChanged() {
      const data = await this.$jsonRPC(
        'Request::calcTime',
        {
          division: this.selectedDivision.value,
          service: this.selectedService.value,
          level: this.selectedLevel.value,
          createdAt: this.number === 0 ? null : this.createdAt,
        },
      );
      this.createdAt = data.createdAt;
      this.repairBefore = data.repairBefore;
    },

    async newRequest() {
      if (this.problem.trim().length < 10) {
        this.$q.notify({
          position: 'top',
          timeout: 5000,
          type: 'warning',
          message: 'Опишите проблему подробнее.',
        });
        return;
      }
      const reply = await this.$jsonRPC(
        'Request::new',
        {
          division: this.selectedDivision.value,
          service: this.selectedService.value,
          level: this.selectedLevel.value,
          problem: this.problem.trim(),
          equipment: (this.selectedEquipment === null ? null : this.selectedEquipment.value),
          partner: (this.selectedPartner === null ? null : this.selectedPartner.value),
          contact: (this.selectedContact === null ? null : this.selectedContact.value),
        },
      );
      if (reply.ok === 'ok') {
        this.$q.notify({
          position: 'top',
          timeout: 5000,
          type: 'info',
          message: `Создана заявка №${reply.number}.`,
        });
        this.$emit('close');
      }
    },

    async doAddComment() {
      const commentText = this.comment.trim();
      if (commentText === '') {
        return;
      }
      const reply = await this.$jsonRPC(
        'Request::addComment',
        { number: this.number, comment: commentText },
      );
      if (reply.ok === 'ok') {
        this.$q.notify({
          position: 'top',
          timeout: 5000,
          type: 'info',
          message: `Добавлен комментарий к заявке №${this.number}.`,
        });
        this.comment = '';
        this.events.push(this.eventView(reply.event));
      }
    },

    readFile(file) {
      return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onloadend = () => {
          resolve(reader.result.replace(/^data:.+;base64,/, ''));
        };
        reader.readAsDataURL(file);
      });
    },

    async doAddDoc() {
      if (this.file === null) {
        return;
      }
      const fileData = await this.readFile(this.file);
      const reply = await this.$jsonRPC(
        'Request::addDocument',
        {
          number: this.number,
          name: this.file.name,
          size: this.file.size,
          content: fileData,
        },
      );
      if (reply.ok === 'ok') {
        this.$q.notify({
          position: 'top',
          timeout: 5000,
          type: 'info',
          message: `Документ '${this.file.name} добавлен к заявке №${this.number}.`,
        });
        this.file = null;
        this.events.push(this.eventView(reply.event));
      }
    },

    base64_decode(encoded) {
      const binary = atob(encoded);
      const array = new Uint8Array(binary.length);
      for (let i = 0; i < binary.length; i += 1) {
        array[i] = binary.charCodeAt(i);
      }
      return array;
    },

    async getDoc(doc) {
      const reply = await this.$jsonRPC('Request::getDocument', { id: doc.id });
      if (reply.ok === 'ok') {
        const binContent = this.base64_decode(reply.content);
        const blob = new Blob(
          [binContent],
          { type: reply.type },
        );
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.style = 'display: none';
        link.href = url;
        link.setAttribute('download', doc.name);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    },

    async doRequestChange() {
      if (!this.requestChanged) {
        return;
      }
      const changes = { number: this.number };
      if (this.selectedService.value !== this.initialService) {
        changes.service = this.selectedService.value;
      }
      if (this.selectedLevel.value !== this.initialLevel) {
        changes.level = this.selectedLevel.value;
      }
      if ((this.selectedEquipment === null && this.initialEquipment !== null)
          || (this.selectedEquipment !== null
            && this.selectedEquipment.value !== this.initialEquipment)) {
        changes.equipment = this.selectedEquipment === null ? null : this.selectedEquipment.value;
      }
      if ((this.selectedPartner === null && this.initialPartner !== null)
          || (this.selectedPartner !== null
            && this.selectedPartner.value !== this.initialPartner)) {
        changes.partner = this.selectedPartner === null ? null : this.selectedPartner.value;
      }
      if ((this.selectedContact === null && this.initialContact !== null)
          || (this.selectedContact !== null
            && this.selectedContact.value !== this.initialContact)) {
        changes.contact = this.selectedContact === null ? null : this.selectedContact.value;
      }
      const reply = await this.$jsonRPC('Request::change', changes);
      if (reply.ok === 'ok') {
        this.$q.notify({
          position: 'top',
          timeout: 5000,
          type: 'info',
          message: `Данные заявки №${this.number} изменены.`,
        });
        this.$emit('close');
      }
    },
  },
};
</script>
