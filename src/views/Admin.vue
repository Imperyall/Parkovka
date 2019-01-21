<template>
	<div class="admin">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="collapse navbar-collapse">
        <Routes 
          :permission=permission 
          :path=path />
        <form class="form-inline">
          <div class="btn-toolbar">
            <div class="btn-group">
              <b-dropdown id="reports" text="Отчеты" variant="info" class="mr-2">
                <b-dropdown-item @click=getReport()>Общий</b-dropdown-item>
              </b-dropdown>
            </div>
            <div class="btn-group">
              <b-btn 
                @click=editForm()
                class="btn btn-info mr-2">Новая запись</b-btn>
            </div>
          </div>
          <div class="input-group">
            <div class="input-group-prepend">
              <span 
                class="input-group-text" 
                v-b-popover.hover.bottom="'Количество записей'"
                id="basic-addon1">{{ table_data.length }}</span>
            </div>
            <input 
              class="form-control b-2" 
              type="search" 
              placeholder="Поиск..." 
              v-model="search" 
              aria-label="Search" />
          </div>
        </form>
      </div>
    </nav>
    <div class="mt-3" v-show="timer != 0">
      <b-alert 
        :variant=alert.type
        :show=timer
        @dismiss-count-down="countDownChanged"
        dismissible>{{ alert.msg }}</b-alert>
    </div>
		<Table 
			:titles=titles
      :editForm=editForm
			:table_data="table_data | searched(search)" />
    <EditForm :submit_form=submit_form :delete_form=delete_form :form_data=form_data />
	</div>
</template>

<script>
	import Table from '@/components/Table.vue'
  import Routes from '@/components/Routes.vue'
  import EditForm from '@/components/EditForm.vue'
  import { DELETE_DATA, EDIT_DATA, GET_DATA, SET_SEARCH, SET_ALERT_TIMER, GET_REPORT } from '@/constants/index'

	export default {
		name: 'admin',
		data () {
  		return {
  			form_data: {}
  		}
  	},
  	mounted () {
  		this.$store.dispatch(GET_DATA, 'get_all')
  	},
    methods: {
      editForm (item) {
        this.form_data = item
        this.$root.$emit('bv::show::modal','editForm')
      },
      submit_form (form) {
        this.$store.dispatch(EDIT_DATA, form)
      },
      delete_form (nomer) {
        this.$store.dispatch(DELETE_DATA, nomer)
      },
      countDownChanged (dismissCountDown) {
        this.timer = dismissCountDown
      },
      getReport () {
        this.$store.dispatch(GET_REPORT)
      }
    },
  	filters: {
  		searched: (value, query) => query.trim() == '' || query.length < 3 
        ? [] 
        : value.filter(item => {
            const str = 
              item.nomer +
              item.name +
              item.department

            return str.toLowerCase().replace(/(?:null|\s)/gi,'').includes(query.trim().toLowerCase())
          })
  	},
  	computed: {
      search: {
        get () { return this.$store.getters.SEARCH },
        set (value) { this.$store.commit(SET_SEARCH, value) }
      },
      permission () { return this.$store.getters.PERMISSION },
	  	titles () { return this.$store.getters.TITLES },
	  	table_data () { return this.$store.getters.DATA },
      path () { return this.$route.path },
      alert () { return this.$store.getters.ALERT },
      timer: {
        get () { return this.$store.getters.ALERT.timer },
        set (value) { this.$store.commit(SET_ALERT_TIMER, value) }
      },
  	},
  	components: {
    	Table,
      Routes,
      EditForm
  	}
	}
</script>