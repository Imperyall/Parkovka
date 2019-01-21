<template>
  <div class="home">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="collapse navbar-collapse">
        <Routes 
          :permission=permission 
          :path=path />
        <form class="form-inline">
          <input 
            class="form-control b-2" 
            type="search" 
            placeholder="Поиск..." 
            v-model="search" 
            aria-label="Search" />
        </form>
      </div>
    </nav>
    <Table 
      :titles=titles
      :table_data="table_data | searched(search)" />
  </div>
</template>

<script>
	import Table from '@/components/Table.vue'
  import Routes from '@/components/Routes.vue'
  import { GET_DATA, SET_SEARCH } from '@/constants/index'

	export default {
		name: 'home',
		data () {
  		return {
  			test: ''
  		}
  	},
    mounted () {
      this.$store.dispatch(GET_DATA)
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
	  	titles () { return this.$store.getters.SHORT_TITLES },
	  	table_data () { return this.$store.getters.DATA },
      path () { return this.$route.path }
  	},
  	components: {
    	Table,
      Routes
  	}
	}
</script>
