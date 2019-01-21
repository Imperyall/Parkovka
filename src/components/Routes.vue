<template>
	<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
	  <li class="nav-item" :class="{ 'active': path == '/' }">
	    <router-link class="nav-link" to="/">Главная</router-link>
	  </li>
	  <li v-if="permission > 1" class="nav-item" :class="{ 'active': path == '/admin' }">
      <router-link class="nav-link" to="/admin">Администрирование</router-link>
    </li>
    <li v-if="permission > 1" class="nav-item" :class="{ 'active': path == '/trash' }">
      <router-link class="nav-link" to="/trash">Удаленные записи</router-link>
    </li>
	</ul>
</template>

<script>
  export default {
    name: 'routes',
    props: {
      permission: Number,
      path: String
    }
  }
</script>