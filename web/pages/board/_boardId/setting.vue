<template>
  <ValidationObserver ref="form" v-slot="{ handleSubmit }">
  <!-- Server Side Validation https://vee-validate.logaretm.com/v3/advanced/server-side-validation.html#setting-errors-manually -->
      <v-container>
        <v-row>
          <v-col cols="6" class="mx-auto">
            <form @submit.prevent="handleSubmit(update)">
              <v-row justify="center" class="mt-10">
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
                <v-btn type="submit" color="primary" dark large>{{ $t('button.update') }}</v-btn>
              </v-row>
            </form>
            <v-row class="mt-16" justify="end">
              <v-dialog v-model="dialog" width="500">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn color="red" dark v-bind="attrs" v-on="on">{{ $t('button.delete_this_board') }}</v-btn>
                </template>
                <v-card>
                  <v-card-title class="text-h5 grey lighten-2">{{ $t('modal.do_you_want_to_delete_the_board_XXX', { _field_: board.title }) }}</v-card-title>
                  <v-card-text class="mt-2">{{ $t('message.deleting_a_board_also_deletes_all_tasks_in_that_board_and_they_cannot_be_restored') }}</v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" text @click="dialog = false">{{ $t('button.cancel') }}</v-btn>
                    <v-btn color="red" text @click="deleteBoard()">{{ $t('button.delete') }}</v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-row>
          </v-col>
        </v-row>
      </v-container>
  </ValidationObserver>
</template>
<script>
  export default {
    head() {
      return {
        title: this.$t('page.board_setting'),
      };
    },
    layout: "authenticated",
    data () {
      return {
        dialog: false,
        board: {
          title: '',
        }
      }
    },
    methods: {
      async getBoard() {
        try{
          const res = await this.$axios.get(`/api/board/${this.$route.params.boardId}`);
          this.board = res.data;
        }catch(err){
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if(data?.detail){
              this.$refs.form.setErrors({general: this.$t('error.' + data.detail)});//ok
            }else{
              this.$refs.form.setErrors({general: this.$t('error.XXX_error_occurred_please_try_again_later', { _field_: status })});//ok
            }
          }else{
            this.$refs.form.setErrors({general: this.$t('error.an_error_occurred_please_try_again_later')});//statusがない場合ここが処理される前にCORSエラーになる
          }
        }
      },
      // Promiseとasync/await　使い方と注意したいこと https://qiita.com/hinako_n/items/e2cf6fd9dc6e9d069901
      // async/awaitを利用したコードのエラーハンドリング https://qiita.com/kenshiroh/items/18b709702bcbb265c055
      async update() {
        try{
          this.$refs.form.setErrors({general: ''});
          // https://stackoverflow.com/questions/69872044/nuxt-auth-module-axios-not-setting-csrf-token-on-request
          // https://github.com/laravel/sanctum/issues/11
          await this.$axios.get("/sanctum/csrf-cookie");
          await this.$axios.put(`/api/board/${this.$route.params.boardId}`, this.board);
          await this.$nuxt.$emit('getBoards');
          this.$router.push(this.localePath(`/board/${this.$route.params.boardId}`));
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
      async deleteBoard(){
        await this.$axios.get("/sanctum/csrf-cookie");
        await this.$axios.delete(`/api/board/${this.$route.params.boardId}`);
        this.dialog = false;
        await this.$nuxt.$emit('getBoards');
        this.$router.push(this.localePath(`/board/${this.$store.state.boards[0].id}`));
      }
    },
    mounted() {
      this.getBoard();
    }
  }
</script>
<style>
  .centered-input input {
    text-align: center
  }
</style>