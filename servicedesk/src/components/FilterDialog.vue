<template>
  <q-card class="q-dialog-plugin">
    <q-card-section class="bg-primary text-white text-center text-h6 q-pa-xs">
      <q-toolbar>
        <q-toolbar-title>
          Фильтр заявок
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
      <q-card-section>
        <q-select
          v-if="divisionsList.length > 4"
          v-model="selectedDivision"
          use-input
          hide-selected
          fill-input
          dense
          options-dense
          input-debounce="0"
          :label="selectedDivision.prefix=== '' ? 'Клиент' : selectedDivision.prefix"
          behaviuor="menu"
          :options="divisionsFiltered"
          @filter="filterDivisions"
          @input="limitDivisionTypesList"
        >
          <template v-slot:no-option>
            <q-item>
              <q-item-section class="text-grey">
                Нет совпадений
              </q-item-section>
            </q-item>
          </template>
          <template v-slot:option="props">
            <q-item
              v-bind="props.itemProps"
              v-on="props.itemEvents"
            >
              <q-item-section>
                <q-item-label>
                  <span :class="`option-${props.opt.type}`">
                    {{ props.opt.label }}
                  </span>
                </q-item-label>
              </q-item-section>
            </q-item>
          </template>
        </q-select>
        <q-select
          v-if="divisionTypesLimited.length > 2"
          v-model="selectedDivisionType"
          use-input
          hide-selected
          fill-input
          dense
          options-dense
          input-debounce="0"
          label="Тип подразделения"
          behaviuor="menu"
          :options="divisionTypesFiltered"
          @filter="filterDivisionTypes"
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
          v-if="partnersList.length > 2"
          v-model="selectedPartner"
          use-input
          hide-selected
          fill-input
          dense
          options-dense
          input-debounce="0"
          label="Партнёр"
          behaviuor="menu"
          :options="partnersFiltered"
          @filter="filterPartners"
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
          v-if="servicesList.length > 2"
          v-model="selectedServices"
          dense
          options-dense
          multiple
          emit-value
          input-debounce="0"
          label="Услуга"
          behaviuor="menu"
          :options="servicesList"
          :display-value="selectedServicesDisplay"
        >
          <template v-slot:no-option>
            <q-item>
              <q-item-section class="text-grey">
                Нет совпадений
              </q-item-section>
            </q-item>
          </template>
          <template v-slot:option="scope">
            <q-item
              v-bind="scope.itemProps"
              v-on="scope.itemEvents"
            >
              <q-item-section side>
                <q-toggle
                  v-if="scope.opt.value === null"
                  v-model="selectedServicesAll"
                  dense
                  indeterminate-value="null"
                  @input="toggleServicesAll"
                />
                <q-toggle
                  v-else
                  v-model="selectedServices"
                  dense
                  :val="scope.opt.value"
                  @input="toggleService"
                />
              </q-item-section>
              <q-item-section>
                <q-item-label>
                  {{ scope.opt.name }} {{ scope.opt.shortName ? `( ${scope.opt.shortName} )` : '' }}
                </q-item-label>
              </q-item-section>
            </q-item>
          </template>
        </q-select>
        <q-input
          v-model="text"
          dense
          clearable
          label="Текст"
        />
        <div class="q-pt-xs">
          Дата поступления (для закрытых и отменённых):
        </div>
        <q-option-group
          v-model="interval"
          dense
          size="xs"
          :options="intervalOptions"
          @input="intervalChanged"
        />
        <div
          v-if="interval === null"
          class="row"
        >
          <q-input
            v-model="fromDateHuman"
            dense
            readonly
            label="С даты"
            class="col q-pr-sm"
          >
            <template v-slot:append>
              <q-icon
                name="fas fa-calendar-alt"
                class="cursor-pointer"
              >
                <q-popup-proxy
                  ref="qFromDateProxy"
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date
                    v-model="fromDate"
                    mask="YYYY-MM-DD"
                    minimal
                    @input="inputFromDate"
                  />
                </q-popup-proxy>
              </q-icon>
              <q-icon
                v-if="fromDate"
                name="fas fa-times-circle"
                class="cursor-pointer"
                @click.stop="fromDate = null"
              />
            </template>
          </q-input>
          <q-input
            v-model="toDateHuman"
            dense
            readonly
            label="До даты"
            class="col q-pl-sm"
          >
            <template v-slot:append>
              <q-icon
                name="fas fa-calendar-alt"
                class="cursor-pointer"
              >
                <q-popup-proxy
                  ref="qToDateProxy"
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date
                    v-model="toDate"
                    mask="YYYY-MM-DD"
                    minimal
                    @input="inputToDate"
                  />
                </q-popup-proxy>
              </q-icon>
              <q-icon
                v-if="toDate"
                name="fas fa-times-circle"
                class="cursor-pointer"
                @click.stop="toDate = null"
              />
            </template>
          </q-input>
        </div>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn
          color="info"
          label="Сохранить"
          icon="fas fa-save"
          size="sm"
          @click.prevent.stop="onSaveFilterClick"
        />
        <q-btn
          color="warning"
          label="Сбросить"
          icon="fas fa-recycle"
          size="sm"
          @click.prevent.stop="onResetClick"
        />
        <q-btn
          type="submit"
          color="primary"
          label="Применить"
          icon="fas fa-check"
          size="sm"
          @click.prevent.stop="onOkClick"
        />
      </q-card-actions>
    </q-form>
  </q-card>
</template>

<script>
import { date } from 'quasar';

const intervalOptions = [
  { label: '1 последний год', value: '1y' },
  { label: '3 последних месяца', value: '3m' },
  { label: '1 последний месяц', value: '1m' },
  { label: '1 последняя неделя', value: '1w' },
  { label: 'указать интервал', value: null },
];

export default {
  name: 'FilterDialog',

  props: {
    filter: {
      type: Object,
      default: () => {},
    },
  },

  data() {
    return {
      selectedDivision: {},
      selectedDivisionType: {},
      selectedPartner: {},
      selectedServices: [],
      selectedServicesAll: null,
      text: '',
      interval: '3m',
      fromDate: null,
      toDate: null,
      divisionsFiltered: [],
      divisionsList: [],
      divisionTypesFiltered: [],
      divisionTypesList: [],
      divisionTypesLimited: [],
      partnersList: [],
      partnersFiltered: [],
      servicesList: [],
      servicesFiltered: [],
      intervalOptions,
    };
  },

  computed: {
    fromDateHuman() {
      return (this.fromDate === null ? '' : this.formatDate(this.fromDate));
    },

    toDateHuman() {
      return (this.toDate === null ? '' : this.formatDate(this.toDate));
    },

    selectedServicesDisplay() {
      if (this.selectedServicesAll !== null) {
        return 'Все';
      }
      if (this.selectedServices.length === 1) {
        return this.servicesList
          .find(el => el.value !== null && this.selectedServices.includes(el.value))
          .name;
      }
      if (this.selectedServices.length <= 5) {
        return this.servicesList
          .filter(el => this.selectedServices.includes(el.value))
          .map(el => el.shortName)
          .join(', ');
      }
      if (this.selectedServices.length === this.servicesList.length - 2) {
        const names = this.servicesList
          .find(el => el.value !== null && !this.selectedServices.includes(el.value))
          .name;
        return `КРОМЕ ${names}`;
      }
      if (this.selectedServices.length >= this.servicesList.length - 6) {
        const names = this.servicesList
          .filter(el => el.value !== null && !this.selectedServices.includes(el.value))
          .map(el => el.shortName)
          .join(', ');
        return `КРОМЕ ${names}`;
      }
      return `Выбраны ${this.selectedServices.length} из ${this.servicesList.length - 1}`;
    },
  },

  mounted() {
    this.fillFilter(this.filter);
  },

  methods: {
    fillFilter(filter) {
      this.fillFilterLists(filter);
      this.text = filter.text;
      this.interval = filter.interval;
      this.fromDate = filter.fromDate;
      this.toDate = filter.toDate;
    },

    buildFilter() {
      return {
        contragent:
          this.selectedDivision.type === 'contragent' ? this.selectedDivision.value : null,
        contract: this.selectedDivision.type === 'contract' ? this.selectedDivision.value : null,
        division: this.selectedDivision.type === 'division' ? this.selectedDivision.value : null,
        divisionType: this.selectedDivisionType.value,
        excludedServices: this.selectedServices.length === this.servicesList.length - 1
          ? []
          : this.servicesList
            .map(el => el.value)
            .filter(el => el !== null && !this.selectedServices.includes(el)),
        partner: this.selectedPartner.value,
        text: this.text,
        interval: this.interval,
        fromDate: this.fromDate,
        toDate: this.toDate,
      };
    },

    onOkClick() {
      this.$emit('ok', this.buildFilter());
    },

    onResetClick() {
      const savedFilter = localStorage.getItem('filter');
      if (savedFilter === null) {
        [this.selectedDivision] = this.divisionsList;
        [this.selectedDivisionType] = this.divisionTypesList;
        [this.selectedPartner] = this.partnersList;
        this.selectedServices = this.servicesList
          .map(el => el.value);
        this.selectedServices.shift();
        this.selectedServicesAll = true;
        this.text = '';
        this.interval = this.filter.interval;
        this.fromDate = null;
        this.toDate = null;
      } else {
        this.fillFilter(JSON.parse(savedFilter));
      }
    },

    onSaveFilterClick() {
      localStorage.setItem('filter', JSON.stringify(this.buildFilter()));
    },

    inputFromDate() {
      if (this.toDate < this.fromDate) {
        this.toDate = this.fromDate;
      }
      this.$refs.qFromDateProxy.hide();
    },

    inputToDate() {
      if (this.fromDate > this.toDate) {
        this.fromDate = this.toDate;
      }
      this.$refs.qToDateProxy.hide();
    },

    formatDate(dt) {
      return date.formatDate(
        date.extractDate(dt, 'YYYY-MM-DD'),
        'D MMMM YYYY',
        {
          months: [
            'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
            'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря',
          ],
        },
      );
    },

    sortByName(a, b) {
      return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
    },

    async fillDivisionsList(filter) {
      const data = await this.$jsonRPC('Division::getList', {});
      this.divisionsList = [{
        type: 'contragent',
        value: null,
        label: 'Все',
        prefix: '',
        divisionTypes: null,
      }];
      data.contragents.sort(this.sortByName);
      for (let i = 0; i < data.contragents.length; i += 1) {
        this.divisionsList.push({
          type: 'contragent',
          value: data.contragents[i].id,
          label: data.contragents[i].name,
          prefix: '',
          divisionTypes: data.contragents[i].divTypeIds,
        });
        data.contragents[i].contracts.sort(this.sortByName);
        for (let j = 0; j < data.contragents[i].contracts.length; j += 1) {
          this.divisionsList.push({
            type: 'contract',
            value: data.contragents[i].contracts[j].id,
            label: `Договор ${data.contragents[i].contracts[j].name}`,
            prefix: data.contragents[i].name,
            divisionTypes: data.contragents[i].contracts[j].divTypeIds,
          });
          data.contragents[i].contracts[j].divisions.sort(this.sortByName);
          for (let k = 0; k < data.contragents[i].contracts[j].divisions.length; k += 1) {
            this.divisionsList.push({
              type: 'division',
              value: data.contragents[i].contracts[j].divisions[k].id,
              label: data.contragents[i].contracts[j].divisions[k].name,
              prefix: `${data.contragents[i].name} / Договор ${data.contragents[i].contracts[j].name}`,
              divisionTypes: data.contragents[i].contracts[j].divisions[k].divTypeIds,
            });
          }
        }
      }

      const needle = filter.contragent || filter.contract || filter.division;
      this.selectedDivision = this.divisionsList.find(el => el.value === needle);
      if (this.selectedDivision === undefined) {
        [this.selectedDivision] = this.divisionsList;
      }

      this.divisionTypesList = data.divisionTypes
        .sort(this.sortByName)
        .map(el => ({ label: el.name, value: el.id }));
      this.divisionTypesList.unshift({ value: null, label: 'Все' });
      this.selectedDivisionType = this.divisionTypesList
        .find(el => el.value === filter.divisionType);
      if (this.selectedDivisionType === undefined) {
        [this.selectedDivisionType] = this.divisionTypesList;
      }
      this.limitDivisionTypesList();
    },

    async fillPartnersList(filter) {
      const data = await this.$jsonRPC('Partner::getList', {});
      this.partnersList = data.partners
        .sort(this.sortByName)
        .map(el => ({ label: el.name, value: el.id }));
      this.partnersList.unshift({ value: null, label: 'Все' });
      this.selectedPartner = this.partnersList
        .find(el => el.value === filter.partner);
      if (this.selectedPartner === undefined) {
        [this.selectedPartner] = this.partnersList;
      }
    },

    async fillServicesList(filter) {
      const data = await this.$jsonRPC('Service::getList', {});
      this.servicesList = data.services
        .sort(this.sortByName)
        .map(el => ({ name: el.name, value: el.id, shortName: el.shortName }));
      this.selectedServicesAll = null;
      if (filter.excludedServices.length === 0) {
        this.selectedServices = this.servicesList
          .map(el => el.value);
        this.selectedServicesAll = true;
      } else {
        this.selectedServices = this.servicesList
          .filter(el => !filter.excludedServices.includes(el.value))
          .map(el => el.value);
        if (this.selectedServices.length === this.servicesList.length) {
          this.selectedServicesAll = true;
        }
      }
      this.servicesList.unshift({ value: null, name: 'Все', shortName: false });
    },

    async fillFilterLists(filter) {
      await Promise.all([
        this.fillDivisionsList(filter),
        this.fillPartnersList(filter),
        this.fillServicesList(filter),
      ]);
    },

    filterDivisions(val, update) {
      if (val === '') {
        update(() => { this.divisionsFiltered = this.divisionsList; });
      }
      const needle = val.toLowerCase();
      update(() => {
        let contragent = null;
        let contract = null;
        let allContracts = false;
        let allDivisions = false;
        this.divisionsFiltered = [];
        for (let i = 0; i < this.divisionsList.length; i += 1) {
          switch (this.divisionsList[i].type) {
            case 'contragent':
              contragent = this.divisionsList[i];
              allContracts = false;
              if (this.divisionsList[i].label.toLowerCase().indexOf(needle) > -1) {
                allContracts = true;
                this.divisionsFiltered.push(this.divisionsList[i]);
              }
              break;
            case 'contract':
              contract = this.divisionsList[i];
              allDivisions = false;
              if (allContracts) {
                this.divisionsFiltered.push(this.divisionsList[i]);
                break;
              }
              if (this.divisionsList[i].label.toLowerCase().indexOf(needle) > -1) {
                if (contragent !== null) {
                  this.divisionsFiltered.push(contragent);
                  contragent = null;
                }
                this.divisionsFiltered.push(this.divisionsList[i]);
                allDivisions = true;
              }
              break;
            case 'division':
            default:
              if (allContracts || allDivisions) {
                this.divisionsFiltered.push(this.divisionsList[i]);
                break;
              }
              if (this.divisionsList[i].label.toLowerCase().indexOf(needle) > -1) {
                if (contragent !== null) {
                  this.divisionsFiltered.push(contragent);
                  contragent = null;
                }
                if (contract !== null) {
                  this.divisionsFiltered.push(contract);
                  contract = null;
                }
                this.divisionsFiltered.push(this.divisionsList[i]);
              }
          }
        }
      });
    },

    limitDivisionTypesList() {
      const limit = this.selectedDivision.divisionTypes;
      if (limit === null) {
        this.divisionTypesLimited = this.divisionTypesList;
      } else {
        this.divisionTypesLimited = this.divisionTypesList
          .filter(el => limit.includes(el.value) || el.value === null);
      }
      if (!this.divisionTypesLimited.includes(this.selectedDivisionType)) {
        [this.selectedDivisionType] = this.divisionTypesLimited;
      }
    },

    filterDivisionTypes(val, update) {
      if (val === '') {
        update(() => { this.divisionTypesFiltered = this.divisionTypesLimited; });
      }
      const needle = val.toLowerCase();
      update(
        () => {
          this.divisionTypesFiltered = this.divisionTypesLimited
            .filter(el => el.label.toLowerCase().indexOf(needle) > -1);
        },
      );
    },

    filterPartners(val, update) {
      if (val === '') {
        update(() => { this.partnersFiltered = this.partnersList; });
      }
      const needle = val.toLowerCase();
      update(
        () => {
          this.partnersFiltered = this.partnersList
            .filter(el => el.label.toLowerCase().indexOf(needle) > -1);
        },
      );
    },

    toggleServicesAll(val) {
      if (val) {
        this.selectedServices = this.servicesList
          .map(el => el.value)
          .slice(1);
      } else {
        this.selectedServices = [];
      }
    },

    toggleService(val) {
      if (val.length === 0) {
        this.selectedServicesAll = false;
      } else if (val.length === this.servicesList.length - 1) {
        this.selectedServicesAll = true;
      } else {
        this.selectedServicesAll = null;
      }
    },

    intervalChanged(val) {
      if (val !== null) {
        this.fromDate = null;
        this.toDate = null;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
  .option-contragent {
    font-weight: bold;
  }
  .option-contract {
    padding-left: 1em;
  }
  .option-division {
    padding-left: 2em;
    font-size: 0.9em;
  }
</style>
