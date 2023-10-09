<template>
  <ValidationObserver ref="form">
    <form @submit.prevent="create">
      <v-container class="mt-5">
        <v-row justify="center" class="mt-10">
          <validation-provider vid="general" name="general" v-slot="{ errors }" style="width:100%">
            <div v-show="errors.length" class="mt-n5 help is-danger red--text text--accent-3 text-subtitle-2" style="width:100%"><!-- nuxt.jsでフォームにバリデーションをつける。 https://qiita.com/fussy113/items/2693f3911f74b4b575a2 -->
              {{ errors[0] }}
            </div>
          </validation-provider>
        </v-row>
        <v-row>
          <validation-provider vid="title" rules="required|max:255" name="タイトル" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="task.title" placeholder="タイトル" :error-messages="errors[0]" outlined class="title-font centered-input"></v-text-field>
            <!-- https://v2.vuetifyjs.com/ja/components/text-fields/ -->
            <!-- https://blog.nightonly.com/2021/10/16/vee-validateで入力チェックした上で、apiからの入力エラー/ -->
          </validation-provider>
        </v-row>
        <v-row class="mt-5">
          <validation-provider vid="content" rules="max:16384" name="内容" v-slot="{ errors }" style="width:100%">
            <v-textarea v-model="task.content" label="内容" :error-messages="errors[0]" outlined></v-textarea>
          </validation-provider>
        </v-row>
        <v-row>
          <validation-provider vid="person_in_charge" rules="max:255" name="担当" v-slot="{ errors }" style="width:100%">
            <v-text-field v-model="task.person_in_charge" label="担当" :error-messages="errors[0]" outlined></v-text-field>
          </validation-provider>
        </v-row>
        <v-row justify="end">
          <v-btn type="submit" color="primary" dark large>create</v-btn>
        </v-row>
      </v-container>
    </form>
  </ValidationObserver>
</template>
<script>
  export default {
    layout: "authenticated",
    head: {
      title: "TaskCreation",
    },
    data(){
      return{
        task: {
          title: '',
          content: '',
          person_in_charge: '',
          board_id: this.$route.params.boardId,
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
          await this.$axios.post(`/api/board/${this.$route.params.boardId}/task/create`, this.task);
          this.$router.push(this.localePath('/board/' + this.$route.params.boardId));
        }catch(err){
          const { response } = err;
          const { data, status } = response || {};
          if(status){
            if (status === 422 && data?.errors) {
              this.$refs.form.setErrors(data.errors);
            }else if(data?.detail){
              this.$refs.form.setErrors({general: data.detail});
            }else{
              this.$refs.form.setErrors({general: status + ' error occurred please try again later'});
            }
          }else{
            this.$refs.form.setErrors({general: 'An error occurred please try again later'});//statusがない場合ここが処理される前にCORSエラーになる
          }
        }
      }
    }
  }
</script>