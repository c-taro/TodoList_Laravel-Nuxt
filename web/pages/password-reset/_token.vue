<template>
  <ValidationObserver ref="form" v-slot="{ handleSubmit }">
    <form @submit.prevent="handleSubmit(submit)">
      <v-container>
        <v-row justify="center" class="mt-5">
          <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
            <div v-show="errors.length" class="mt-n5 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%;height:20px;">
              {{ errors[0] }}
            </div>
          </validation-provider>
        </v-row>
        <v-row justify="center"><!-- https://www.fuwamaki.com/article/61 -->
          <validation-provider vid="email" rules="required|email" :name="$t('form.email')" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="form.email" :label="$t('form.email')"  :error-messages="errors[0]" type="email"></v-text-field>
            <!-- https://v2.vuetifyjs.com/ja/components/text-fields/ -->
            <!-- https://blog.nightonly.com/2021/10/16/vee-validateで入力チェックした上で、apiからの入力エラー/ -->

          </validation-provider>
        </v-row>
        <v-row justify="center">
          <validation-provider vid="password" rules="required|min:8" :name="$t('form.password')" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="form.password" :label="$t('form.password')" :error-messages="errors[0]" type="password"></v-text-field>
          </validation-provider>
        </v-row>
        <v-row justify="center">
          <validation-provider vid="password_confirmation" rules="required|min:8" :name="$t('form.password_confirmation')" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="form.password_confirmation" :label="$t('form.password_confirmation')" :error-messages="errors[0]" type="password"></v-text-field>
          </validation-provider>
        </v-row>
        <v-row justify="end" class="my-5">
          <v-btn type="submit" color="primary" dark large>{{ $t('button.reset_password') }}</v-btn>
        </v-row>
      </v-container>
    </form>
  </ValidationObserver>
</template>
<script>
  export default {
    head() {
      return {
        title: this.$t('page.password_reset'),
      };
    },
    layout: "guest",
    data() {
      return {
        form: {
          token: '',
          email: '',
          password: '',
          password_confirmation: '',
        },
      };
    },
    methods: {
      // https://qiita.com/hinako_n/items/e2cf6fd9dc6e9d069901
      // https://qiita.com/kenshiroh/items/18b709702bcbb265c055
      async submit() {
        try {
          this.$refs.form.setErrors({general: ''});
          await this.$axios.get("/sanctum/csrf-cookie");
          this.form.token = this.$route.params.token;
          await this.$axios.post('/reset-password', this.form);
          this.$store.commit('setSuccessMessage', this.$t('success.password_was_changed_successfully'));
          this.$router.push(this.localePath('/login'));
        } catch (err) {
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if(status === 422 && data?.errors){
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
      },
    },
  };
</script>