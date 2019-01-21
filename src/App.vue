<template>
  <div id="app" class="p-2">
    <router-view v-if="permission > 0" />
  </div>
</template>

<script type="text/javascript">
  import "bootstrap/dist/css/bootstrap.min.css"
  import "bootstrap-vue/dist/bootstrap-vue.css"
  import { GET_PERMISSION } from '@/constants/index'

  export default {
    name: 'App',
    data () {
      return {
        test: ''
      }
    },
    mounted () {
      this.$store.dispatch(GET_PERMISSION)
    },
    computed: {
      permission () { return this.$store.getters.PERMISSION },
    }
  }
</script>