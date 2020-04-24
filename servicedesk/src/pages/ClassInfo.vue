<template>
  <q-layout view="hHh lpR fFf">
    <q-drawer
      show-if-above
      side="left"
      elevated
      :breakpoint="0"
      :width='250'
    >
      <q-list dense>
        <q-item
          v-for='id in classList'
          :key=id
          clickable
          :active='id == classId'
          active-class='bg-info text-black'
          @click='setClass(id)'
        >
          <q-item-section>
            <q-item-label> {{ id }} </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>
    <q-page-container>
      <q-page>
        <q-table
          :title='className'
          :data='classInfo'
          :columns='descriptionTable'
          row-key='name'
          dense
          hide-header
          hide-bottom
          :pagination.sync="pagination"
          :rows-per-page-options="[0]"
        >
          <template v-slot:body-cell-type='props'>
            <q-td :props='props' auto-width>
              {{ props.row.virtual ? 'virt ' : '' }}
              {{ props.row.readonly ? 'ro ' : '' }}
              <span
                v-if='props.row.type === "ref"'
                class="class"
                @click='setClass(props.row.class)'
              >
                {{ props.row.nullable ? '?' : '' }}{{ props.row.class }}
              </span>
              <span
                v-else-if='props.row.type === "backRef"'
                class="class"
                @click='setClass(props.row.class)'
              >
                  {{ props.row.nullable ? '?' : '' }}{{ props.row.class }}[]
                </span>
              <span
                v-else-if='props.row.type === "refm2m"'
                class="class"
                @click='setClass(props.row.class)'
              >
                {{ props.row.nullable ? '?' : '' }}{{ props.row.class }}[]
              </span>
              <span v-else>{{ props.row.nullable ? '?' : '' }}{{ props.row.type }}</span>
            </q-td>
          </template>
          <template v-slot:body-cell-name='props'>
            <q-td :props='props' auto-width class='text-weight-bold'>
              {{ props.row.name }}
            </q-td>
          </template>
        </q-table>
      </q-page>
    </q-page-container>
    </q-layout>
</template>

<script>

const descriptionTable = [
  { name: 'type', label: '', field: 'type', align: 'right' },
  { name: 'name', label: '', field: 'name', align: 'left' },
  { name: 'desc', label: '', field: 'desc', align: 'left' }
]

export default {
  name: 'ClassInfo',
  data () {
    return {
      classId: 'DivisionType',
      classList: [],
      className: '',
      classInfo: [],
      descriptionTable: descriptionTable,
      pagination: {
        rowsPerPage: 0
      }
    }
  },
  methods: {
    setClass: function (id) {
      if (id === this.classId) {
        return
      }
      this.$router.push('/info/' + id)
    },
    getClassInfo: async function (id) {
      this.classId = id
      let response
      try {
        response = await this.$axios.get('http://10.149.0.206/info/' + id)
      } catch (e) {
        console.log(e)
      }
      this.className = response.data.desc
      this.classInfo = response.data.fields.sort((a, b) => a.name.toLowerCase().localeCompare(b.name.toLowerCase()))
    },
    getClassesList: async function () {
      let response
      try {
        response = await this.$axios.get('http://10.149.0.206/info/list')
      } catch (e) {
        console.log(e)
      }
      this.classList = response.data.sort((a, b) => a.localeCompare(b))
      if (this.classList.includes(this.$route.params.class)) {
        this.class = this.$route.params.class
        this.getClassInfo(this.class)
      } else {
        this.$router.push('/info/' + this.classList[0])
      }
    }
  },
  mounted () {
    this.getClassesList()
  },
  watch: {
    $route (to, from) {
      this.class = to.params.class
      this.getClassInfo(to.params.class)
    }
  }
}
</script>

<style lang="scss">
  .class {
    text-decoration: underline;
    color: hsl(240,100%,50%);
    cursor: pointer;
  }
  tr:nth-child(2n){
    background-color: hsl(0,0%,95%);
  }
  tr:nth-child(2n+1){
    background-color: hsl(0,0%,98%);
  }
</style>
