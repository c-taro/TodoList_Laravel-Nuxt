<template>
  <ValidationObserver ref="form">
  <!-- Server Side Validation https://vee-validate.logaretm.com/v3/advanced/server-side-validation.html#setting-errors-manually -->
    <form @submit.prevent="create">
      <v-container>
        <v-col cols="6" class="mx-auto">
        <v-row justify="center" class="mt-16">
          <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
            <div v-show="errors.length" class="mt-n5 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%"><!-- nuxt.jsでフォームにバリデーションをつける。 https://qiita.com/fussy113/items/2693f3911f74b4b575a2 -->
              {{ errors[0] }}
            </div>
          </validation-provider>
        </v-row>
        <v-row>
          <validation-provider vid="title" rules="required|max:32" :name="$t('model.board.title')" v-slot="{ errors }" style="width:100%">

            <v-text-field v-model="board.title" :placeholder="$t('model.board.title')" :error-messages="errors[0]" outlined class="text-h6 centered-input"></v-text-field>
            <!-- https://v2.vuetifyjs.com/ja/components/text-fields/ -->
            <!-- https://blog.nightonly.com/2021/10/16/vee-validateで入力チェックした上で、apiからの入力エラー/ -->

          </validation-provider>
        </v-row>
        <v-row justify="end">
          <v-btn type="submit" color="primary" dark large>{{ $t('button.create') }}</v-btn>
        </v-row>
        </v-col>
      </v-container>
    </form>
  </ValidationObserver>
</template>
<script>
  export default {
    head() {
      return {
        title: this.$t('page.board_creation'),
      };
    },
    layout: "authenticated",
    data () {
      return {
        board: {
          title: '',
        }
      }
    },
    methods: {
      // Promiseとasync/await　使い方と注意したいこと https://qiita.com/hinako_n/items/e2cf6fd9dc6e9d069901
      // async/awaitを利用したコードのエラーハンドリング https://qiita.com/kenshiroh/items/18b709702bcbb265c055
      async create() {
        try{
          this.$refs.form.setErrors({general: ''});
          // https://stackoverflow.com/questions/69872044/nuxt-auth-module-axios-not-setting-csrf-token-on-request
          // https://github.com/laravel/sanctum/issues/11
          await this.$axios.get("/sanctum/csrf-cookie");
          const res = await this.$axios.post(`/api/board/create`, this.board);
          await this.$nuxt.$emit('getBoards');//Nuxtを使ったコンポーネント間の通信EventBus。知らなかったのでメモ記事 https://qiita.com/viverra/items/3eafaec3eb72c128e7c4
          this.$router.push(this.localePath(`/board/${res.data.id}`));//作成したボードを開く
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
      },
    }
  }
</script>
<style>
  .centered-input input {
    text-align: center
  }
</style>