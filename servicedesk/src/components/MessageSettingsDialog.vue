<template>
  <q-card
    class="q-dialog-plugin"
    style="max-width: 1000px; width: 80vw;"
  >
    <q-card-section class="bg-primary text-white text-center text-h6 q-pa-sm">
      <q-toolbar>
        <q-toolbar-title>
          Настройки сообщений
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
        <div class="row q-gutter-md q-pb-md">
          <q-input
            v-model="role"
            class="col"
            dense
            label="Роль"
            readonly
          >
            <template v-slot:prepend>
              <q-icon name="fa fa-user-tag" />
            </template>
          </q-input>
          <q-input
            v-model="email"
            class="col"
            dense
            label="Адрес электронной почты"
            readonly
          >
            <template v-slot:prepend>
              <q-icon name="fa fa-envelope" />
            </template>
          </q-input>
          <q-input
            v-model="cellphone"
            class="col"
            dense
            label="Номер для SMS"
            prefix="+7"
            mask="(###) ###-##-##"
            unmasked-value
          >
            <template v-slot:prepend>
              <q-icon name="fas fa-sms" />
            </template>
          </q-input>
        </div>
        <q-table
          v-if="contracts.length == 1"
          class="events-table"
          row-key="id"
          dense
          hide-bottom
          :data="events"
          :columns="columns0"
          :pagination.sync="pagination"
          :rows-per-page-options="[0]"
        >
          <template
            v-if="methods.includes('email')"
            v-slot:body-cell-email="props"
          >
            <q-td :props="props">
              <q-toggle
                v-model="settings[allContracts][props.row.id].email.send"
                dense
                keep-color
                color="primary"
                :disable="settings[allContracts][props.row.id].email.forced"
              />
            </q-td>
          </template>
          <template
            v-if="methods.includes('sms')"
            v-slot:body-cell-sms="props"
          >
            <q-td :props="props">
              <q-toggle
                dense
                keep-color
                color="primary"
                :value="settings[allContracts][props.row.id].sms.send"
                :disable="settings[allContracts][props.row.id].sms.forced"
              />
            </q-td>
          </template>
          <template
            v-if="methods.includes('teams')"
            v-slot:body-cell-teams="props"
          >
            <q-td :props="props">
              <q-toggle
                dense
                keep-color
                color="primary"
                :value="settings[allContracts][props.row.id].teams.send"
                :disable="settings[allContracts][props.row.id].teams.forced"
              />
            </q-td>
          </template>
        </q-table>
        <q-table
          v-else
          class="events-table contracts-events-table"
          row-key="id"
          dense
          hide-bottom
          :data="contracts"
          :columns="columns"
          :pagination.sync="pagination"
          :rows-per-page-options="[0]"
        >
          <template v-slot:header="props">
            <q-tr :props="props">
              <q-th
                v-for="col in props.cols"
                :key="col.name"
                :props="props"
              >
                <div class="header">
                  {{ col.label }}
                </div>
                <div
                  v-if="col.name !== 'contract'"
                  class="toggles q-mx-xs"
                >
                  <q-toggle
                    v-for="method in methods"
                    :key="method"
                    v-model="settings[allContracts][col.name][method].send"
                    dense
                    keep-color
                    color="primary"
                    size="sm"
                    :disable="settings[allContracts][col.name][method].forced"
                    :icon="methodsDesc[method].icon"
                    @input="toggle(allContracts, col.name, method)"
                  >
                    <q-tooltip>
                      {{ methodsDesc[method].label }}
                    </q-tooltip>
                  </q-toggle>
                </div>
              </q-th>
            </q-tr>
          </template>
          <template v-slot:body="props">
            <q-tr
              v-if="props.row.id !== allContracts"
              :props="props"
            >
              <q-td
                key="contract"
                :props="props"
              >
                <div>
                  {{ props.row.name }}
                </div>
              </q-td>
              <q-td
                v-for="event in events"
                :key="event.id"
              >
                <div>
                  <q-toggle
                    v-for="method in methods"
                    :key="method"
                    v-model="settings[props.row.id][event.id][method].send"
                    dense
                    keep-color
                    color="primary"
                    size="sm"
                    class="q-px-xs"
                    :disable="settings[allContracts][event.id][method].forced"
                    :icon="methodsDesc[method].icon"
                    @input="toggle(props.row.id, event.id, method)"
                  />
                </div>
              </q-td>
            </q-tr>
          </template>
        </q-table>
      </q-card-section>
      <q-card-actions align="right">
        <q-btn
          color="primary"
          label="Применить"
          icon="fas fa-check"
          size="sm"
          @click="apply"
        />
        <q-btn
          v-close-popup
          color="negative"
          label="Отменть"
          icon="fas fa-times"
          size="sm"
        />
      </q-card-actions>
    </q-form>
  </q-card>
</template>

<script>
const methodsDesc = {
  email: { label: 'E-Mail', icon: 'fas fa-envelope' },
  sms: { label: 'SMS', icon: 'fas fa-sms' },
  teams: { label: 'Teams', icon: 'fas fa-user-friends' },
};

const allContracts = '00000000000000000000000000000000';

export default {
  name: 'MessageSettingsDialog',
  data() {
    return {
      role: '',
      cellphone: '',
      email: '',
      events: [],
      rows: [
      ],
      methods: [],
      contracts: [],
      settings: {},
      columns0: [],
      columns: [],
      pagination: { rowsPerPage: 0 },
      methodsDesc,
      allContracts,
    };
  },

  async mounted() {
    const data = await this.$jsonRPC('User::getMessageSettings', {});
    this.role = data.role;
    this.email = data.email;
    this.cellphone = data.cellphone;
    this.contracts = data.contracts.sort(
      (a, b) => a.name.toLowerCase().localeCompare(b.name.toLowerCase()),
    );
    this.events = data.events;
    this.methods = data.methods;
    this.columns0 = [{
      name: 'event',
      label: 'Событие',
      field: 'name',
      required: true,
      align: 'left',
    }];
    for (let i = 0; i < this.methods.length; i += 1) {
      this.columns0.push({
        name: this.methods[i],
        label: methodsDesc[this.methods[i]].label,
        required: true,
        align: 'center',
      });
    }
    this.columns = [{
      name: 'contract',
      label: 'Договор',
      field: 'name',
      required: true,
      align: 'left',
    }];
    for (let i = 0; i < this.events.length; i += 1) {
      this.columns.push({
        name: this.events[i].id,
        label: this.events[i].name,
        required: true,
        align: 'center',
      });
    }
    data.contracts.unshift({ id: allContracts, name: ' Все ' });
    this.settings = {};
    for (let i = 0; i < data.contracts.length; i += 1) {
      this.$set(this.settings, data.contracts[i].id, {});
      for (let j = 0; j < data.events.length; j += 1) {
        this.$set(this.settings[data.contracts[i].id], data.events[j].id, {});
        for (let k = 0; k < data.methods.length; k += 1) {
          this.$set(
            this.settings[data.contracts[i].id][data.events[j].id],
            data.methods[k],
            {},
          );
          this.$set(
            this.settings[data.contracts[i].id][data.events[j].id][data.methods[k]],
            'forced',
            data.settings[allContracts][data.events[j].id][data.methods[k]].forced,
          );
          this.$set(
            this.settings[data.contracts[i].id][data.events[j].id][data.methods[k]],
            'send',
            data.settings[allContracts][data.events[j].id][data.methods[k]].send
            || data.settings[allContracts][data.events[j].id][data.methods[k]].forced,
          );
        }
        if (data.settings[data.contracts[i].id] !== undefined
          && data.settings[data.contracts[i].id][data.events[j].id] !== undefined
        ) {
          const methods = Object.keys(this.settings[data.contracts[i].id][data.events[j].id]);
          for (let k = 0; k < methods.length; k += 1) {
            // eslint-disable-next-line operator-linebreak
            this.settings[data.contracts[i].id][data.events[j].id][methods[k]].send =
              data.settings[data.contracts[i].id][data.events[j].id][methods[k]].send
              || data.settings[allContracts][data.events[j].id][data.methods[k]].forced;
          }
        }
      }
    }
  },

  methods: {
    toggle(contract, event, method) {
      if (this.settings[contract][event][method].forced) {
        return;
      }
      const value = this.settings[contract][event][method].send;
      if (contract === allContracts) {
        for (let i = 0; i < this.contracts.length; i += 1) {
          this.settings[this.contracts[i].id][event][method].send = value;
        }
        return;
      }
      let indeterminate = false;
      for (let i = 0; i < this.contracts.length && !indeterminate; i += 1) {
        if (this.contracts[i].id !== contract
          && this.contracts[i].id !== allContracts
          && this.settings[this.contracts[i].id][event][method].send !== value
        ) {
          indeterminate = true;
        }
      }
      if (indeterminate) {
        this.settings[allContracts][event][method].send = null;
      } else {
        this.settings[allContracts][event][method].send = value;
      }
    },

    async apply() {
      const sends = [];
      const contracts = Object.keys(this.settings);
      for (let i = 0; i < contracts.length; i += 1) {
        const events = Object.keys(this.settings[contracts[i]]);
        for (let j = 0; j < events.length; j += 1) {
          const methods = Object.keys(this.settings[contracts[i]][events[j]]);
          for (let k = 0; k < this.methods.length; k += 1) {
            if (this.contracts[i].id === allContracts) {
              if (this.settings[contracts[i]][events[j]][methods[k]].send
                || this.settings[contracts[i]][events[j]][methods[k]].forced
              ) {
                sends.push({
                  contract: contracts[i],
                  event: events[j],
                  method: methods[k],
                });
              }
            } else if (this.settings[contracts[i]][events[j]][methods[k]].send
                && !this.settings[allContracts][events[j]][methods[k]].send
                && !this.settings[allContracts][events[j]][methods[k]].forced
            ) {
              sends.push({
                contract: contracts[i],
                event: events[j],
                method: methods[k],
              });
            }
          }
        }
      }
      const response = await this.$jsonRPC(
        'User::setMessageSettings',
        {
          cellphone: this.cellphone,
          settings: sends,
        },
      );
      if (response.ok === 'ok') {
        this.$q.notify({
          position: 'top',
          timeout: 5000,
          type: 'info',
          message: 'Настройки сохранены.',
        });
      }
      this.$emit('ok');
    },
  },
};
</script>

<style lang="scss">
  .events-table {
    max-height: 70vh;

    thead tr th {
      position: sticky;
      z-index: 1;
    }

    thead tr:first-child th {
      top: 0;
    }

    th {
      font-size: 1em;
      font-weight: bold;
      background-color: #EEE;
    }
  }
  .contracts-events-table {
    th:not(:first-child) {
      white-space: normal;
    }

    tr th {
      position: sticky;
      z-index: 2;
      background: #fff;
    }

    td:first-child {
      background-color: #F7F7F7;
      div {
        width: 300px;
        white-space: normal;
      }
    }

    thead tr:last-child th {
      z-index: 3;
    }

    thead tr:first-child th {
      top: 0;
      z-index: 1;
      background: #EEE;
    }

    tr:first-child th:first-child {
      z-index: 3;
    }

    td:first-child {
      z-index: 1
    }

    td:first-child, th:first-child {
      position: sticky;
      left: 0;
    }

    td:not(:first-child) + div
    th:not(:first-child) + div {
      text-align: center;
    }

    th div.header {
      padding-bottom: 1.5em;
    }

    th div.toggles {
      position: absolute;
      bottom: 0.5em;
    }
  }
</style>
