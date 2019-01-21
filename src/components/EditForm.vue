<template>
  <b-modal 
    id="editForm" 
    title="Новая запись"
    footer-class="d-block"
    @shown=shown
    @hidden=hidden>
    <form>
      <div class="form-group row">
        <label 
          for="formNomer" 
          class="col-sm-5 col-form-label">Автомобильный номер</label>
        <div class="col-sm-7">
          <input 
            type="text" 
            ref="nomer" 
            maxlength="10" 
            pattern="[A-Z-0-9-?]{8,9}" 
            class="form-control" 
            :class="{'is-valid': !this.error.nomer, 'is-invalid': this.error.nomer}"
            id="formNomer" 
            v-model.trim="form.nomer">
        </div>
        <small 
          v-if="error.nomer" 
          class="col-sm-12 text-danger">Только английские буквы в верхнем регистре и цифры!</small>
      </div>
      <div class="form-group row">
        <label 
          for="formSurname" 
          class="col-sm-2">Фамилия</label>
        <div class="col-sm-10">
          <input 
            type="text" 
            maxlength="60" 
            class="form-control" 
            id="formSurname" 
            v-model.trim="form.surname">
        </div>
      </div>
      <div class="form-group row">
        <label 
          for="formName" 
          class="col-sm-2">Имя</label>
        <div class="col-sm-10">
          <input
            type="text" 
            maxlength="30" 
            class="form-control" 
            id="formName" 
            v-model.trim="form.name">
        </div>
      </div>
      <div class="form-group row">
        <label 
          for="formFathername" 
          class="col-sm-2">Отчество</label>
        <div class="col-sm-10">
          <input 
            type="text" 
            maxlength="30" 
            class="form-control" 
            id="formFathername" 
            v-model.trim="form.fathername">
        </div>
      </div>
      <div class="form-group row">
        <label 
          for="formDateBirth" 
          class="col-sm-7">Дата рождения</label>
        <div class="col-sm-5">
          <input 
            type="date" 
            class="form-control" 
            id="formDateBirth" 
            v-model="form.date_birth">
        </div>
      </div>
      <div class="form-group">
        <label 
          for="formDepartment">Департамент</label>
        <b-form-select id="formDepartment" v-model="form.department" :options="options" />
      </div>
      <div class="form-group">
        <label 
          for="formSubdivision">Подразделение</label>
        <input 
          type="text" 
          maxlength="100" 
          class="form-control" 
          id="formSubdivision" 
          v-model.trim="form.subdivision">
      </div>
      <div class="form-group">
        <label 
          for="formComments">Комментарий</label>
        <textarea 
          rows="5" 
          maxlength="250" 
          class="form-control" 
          id="formComments" 
          v-model.trim="form.comments"></textarea>
      </div>
    </form>
    <div slot="modal-footer">
       <b-btn class="float-right" variant="primary" @click="submitAction">Добавить</b-btn>
       <b-btn class="float-left" variant="danger" @click="deleteAction">Удалить</b-btn>
     </div>
  </b-modal>
</template>

<script>
  export default {
    name: 'editForm',
    props: {
      ref_name: String,
      form_data: Object,
      submit_form: Function,
      delete_form: Function
    },
    data () {
      return {
        form: {},
        error: {
          nomer: true
        }
      }
    },
    methods: {
      shown () {
        this.form = this.form_data 
          ? Object.assign({}, this.form_data)
          : {
              nomer: '',
              surname: '',
              name: '',
              fathername: '',
              date_birth: '',
              department: '',
              subdivision: '',
              comments: ''
            }

        this.form.old_nomer = this.form.nomer
      },
      hidden () {
        this.form = {}
      },
      submitAction (e) {
        if (this.error.nomer) {
          this.$refs.nomer.focus()
          e.preventDefault()
        } else {
          this.submit_form(this.form)
          this.$root.$emit('bv::hide::modal','editForm')
        }
      },
      deleteAction () {
        this.delete_form(this.form.nomer)
        this.$root.$emit('bv::hide::modal','editForm')
      }
    },
    computed: {
      options () { return this.$store.getters.DEPARTMENTS }
    },
    watch: {
      'form.nomer' (e) {
        this.error.nomer = !(/[A-Z-0-9-?]{8,9}/.test(e))
      },
      'form.department' (e) {
        if (e) this.form.department = e.trim()

      }
    }
  }
</script>