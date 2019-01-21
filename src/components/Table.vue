<template>
	<div class="pk-table table-responsive-sm">
		<table class="table table-hover table-sm">
		  <thead class="thead-light">
		    <tr>
		      <th v-for="title in titles" scope="col">{{ title }}</th>
		    </tr>
		  </thead>
		  <tbody>
		    <tr @click="editForm && editForm(item)" v-for="(item, index) in table_data">
		      <th scope="row">{{ index + 1 }}</th>
          <td v-for="el in item">{{ el }}</td>
		    </tr>
		  </tbody>
		</table>
	</div>
</template>

<script>
export default {
  name: 'Table',
  props: {
  	titles: Array,
  	table_data: Array,
    editForm: Function
  }
}
</script>

<style lang="scss">
  .pk-table {
    margin-top: 10px;
    height: 80vh;
    display: block;
    overflow: auto;
  }
  table {
    border: 1px solid #dee2e6;

    & tr:hover { cursor: pointer; }
  }
</style>