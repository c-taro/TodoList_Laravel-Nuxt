<template>
  <ValidationObserver ref="form" v-slot="{ handleSubmit }">
    <form @submit.prevent="handleSubmit(submit)">
      <v-container>
        <v-row justify="center" class="mt-5">
          <div v-show="successMessage" class="mt-n5 help is-danger green--text text--accent-4 text-subtitle-2" style="width:100%;height:20px;">
            {{ successMessage }}
          </div>
          <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
            <div v-show="errors.length" class="mt-n5 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%;height:20px;">
              {{ errors[0] }}
            </div>
          </validation-provider>
        </v-row>
        <v-row justify="center" class="mt-5">
          {{ $t('message.forgot_your_password_no_problem_just_let_us_know_your_email_address_and_we_will_email_you_a_password_reset_link_that_will_allow_you_to_choose_a_new_one') }}
          <!-- プロパティ名にピリオドが入っている場合ピリオドの後が子要素だと認識されるためブラケットを使って指定する -->
        </v-row>
        <v-row justify="center" class="mt-5">
          <validation-provider vid="email" rules="required|email" :name="$t('form.email')" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="form.email" type="email" :label="$t('form.email')" :error-messages="errors[0]" required></v-text-field>
          </validation-provider>
        </v-row>
        <v-row justify="end" class="my-5">
          <v-btn type="submit" color="primary" dark large v-bind:loading="loading">{{ $t('button.send_password_reset_link') }}</v-btn>
        </v-row>
      </v-container>
    </form>
  </ValidationObserver>
</template>

<script>
export default {
  head() {
    return {
      title: this.$t('page.forgot_password'),
    };
  },
  layout: "guest",
  data() {
    return {
      loading: false,
      form: {
        email: null,
      },
      successMessage: '',
    }
  },
  methods: {
    async submit() {
      try{
        this.successMessage = '';
        this.$refs.form.setErrors({general: ''});
        this.loading = true;
        await this.$axios.get("/sanctum/csrf-cookie");
        const res = await this.$axios.post('/forgot-password', this.form);
        if(typeof res.status !== 'undefined' && res.status === 200){
          this.loading = false;
          this.successMessage = this.$t('success.email_was_sent_successfully');
        }
      }catch(err){
        this.loading = false;

        const { response } = err;
        const { data, status } = response || {};
        if(status){
          if (status === 422 && data?.errors) {
            this.$refs.form.setErrors(data.errors);//ok
          }else if(data?.detail){
            this.$refs.form.setErrors({general: this.$t('error.' + data.detail)});//ok
          }else{
            this.$refs.form.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});//ok
          }
        }else{
          this.$refs.form.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
        }
      }
    }
  }
};
</script>