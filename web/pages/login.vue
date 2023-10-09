<template>
  <ValidationObserver ref="form">
    <form @submit.prevent="login">
      <v-container>
        <v-row justify="center" class="mt-5">
          <div v-show="successMessage" class="mt-n5 help is-danger green--text text--accent-4 text-subtitle-2" style="width:100%;">
            {{ successMessage }}
          </div>
          <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
            <div v-show="errors.length" class="mt-n5 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%;">
              {{ errors[0] }}
            </div>
          </validation-provider>
        </v-row>
        <v-row justify="center" class="mt-5"><!-- https://www.fuwamaki.com/article/61 -->
          <validation-provider vid="email" rules="required|email" :name="$t('form.email')" v-slot="{ errors }" style="width:100%">
            <v-text-field type="email" :label="$t('form.email')" v-model="form.email" :error-messages="errors[0]" required></v-text-field>
            <!-- vee-validateで入力チェックした上で、APIからの入力エラーも各項目に表示する https://blog.nightonly.com/2021/10/16/vee-validateで入力チェックした上で、apiからの入力エラー/ -->
          </validation-provider>
        </v-row>
        <v-row justify="center">
          <validation-provider vid="password" rules="required|min:8" :name="$t('form.password')" v-slot="{ errors }" style="width:100%">
            <v-text-field type="password" :label="$t('form.password')" v-model="form.password" :error-messages="errors[0]"></v-text-field>
          </validation-provider>
        </v-row>
        <v-row justify="end" class="mb-5">
          <v-col cols="8">
            <v-row justify="end">
              <NuxtLink :to="localePath('/forgot-password')" class="mt-2 mr-5">
                {{ $t('link.forgot_your_password') }}
              </NuxtLink>
              <NuxtLink :to="localePath('/register')" class="mt-2 mr-5">
                {{ $t('link.create_an_account') }}
              </NuxtLink>
            </v-row>
          </v-col>
          <v-col cols="4">
            <!-- Nuxt.jsでのページ遷移設定方法（Vue Routerやnuxt-link）｜Nuxt.jsの基本 https://webrandum.net/nuxt-link/ -->
            <v-btn type="submit" color="primary" dark large>{{ $t('button.log_in') }}</v-btn>
          </v-col>
        </v-row>
      </v-container>
    </form>
  </ValidationObserver>
</template>
<script>
  export default {
    async asyncData({ store }) {
      // console.log(window.navigator.language);//JavaScriptで各ブラウザの使用言語を検出する方法 https://qiita.com/shi_ma/items/781b5e2ec49f3bb7ad7d
      const successMessage = store.state.successMessage;
      store.commit('clearSuccessMessage');
      return { successMessage };
    },
    head() {
      return {
        title: this.$t('page.login'),
      };
    },
    layout: "guest",
    data() {
      return {
        form: {
          email: "",
          password: "",
        },
      };
    },
    methods: {
      async login() {
        try {
          this.successMessage = '';
          this.$refs.form.setErrors({general: ''});
          await this.$auth.loginWith("laravelSanctum", { data: this.form });
          this.redirectToBoard();
        } catch (err) {
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if(data?.detail){
              this.$refs.form.setErrors({general: this.$t('error.' + data.detail)});
            }else{
              this.$refs.form.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});
            }
          }else{
            this.$refs.form.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
          }
        }
      },
      async redirectToBoard(){
        await this.getBoards();
        this.$router.push(this.localePath(`/board/${this.$store.state.boards[0].id}`));
      },
      async getBoards(){
        const res = await this.$axios.get('/api/boards');
        await this.$store.commit('setBoards', res.data);
      },
    },
  };
</script>