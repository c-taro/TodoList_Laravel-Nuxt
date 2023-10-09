<template>
  <ValidationObserver ref="form" v-slot="{ handleSubmit }">
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
      </v-row>
      <form @submit.prevent="handleSubmit(sendMail)">
          <v-row justify="center" class="mt-5">
            <validation-provider vid="email" rules="required|email" :name="$t('form.email')" v-slot="{ errors }" style="width:100%">
              <v-text-field v-model="form.email" type="email" :label="$t('form.email')" :error-messages="errors[0]" required></v-text-field>
            </validation-provider>
          </v-row>
          <v-row justify="end" class="my-5">
            <v-btn type="submit" color="primary" dark large>{{ $t('button.send_password_reset_link') }}</v-btn>
          </v-row>
      </form>
    </v-container>
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
      form: {
        email: null,
      },
      successMessage: '',
    }
  },
  methods: {
    async sendMail() {
      try{
        this.successMessage = '';
        this.$refs.form.setErrors({general: ''});
        await this.$axios.get("/sanctum/csrf-cookie");
        const res = await this.$axios.post('/forgot-password', this.form);
        if(typeof res.status !== 'undefined' && res.status === 200){
          this.successMessage = this.$t('success.email_was_sent_successfully');
        }
      }catch(err){
        const { response } = err;
        const { data, status } = response || {};
        if(status){
          if (status === 422 && data?.errors) {
            this.$refs.form.setErrors(data.errors);
          }else if(data?.detail){
            this.$refs.form.setErrors({general: this.$t('error.' + data.detail)});
          }else{
            this.$refs.form.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});
          }
        }else{
          this.$refs.form.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
        }
      }
    }
  }
};
</script>